<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Reply;
use App\Entity\Images;
use App\Entity\Tricks;
use App\Entity\Videos;
use App\Form\ReplyType;
use App\Form\TricksType;
use App\Entity\Discussions;
use App\Form\DiscussionsType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// ReplyType
use Doctrine\Common\Collections\ArrayCollection;

// DiscussionType
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TricksController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/tricks', name: 'tricks')]
    public function indexTricks(): Response
    {
        // Recupere tous les tricks
        $tricks = $this->entityManager->getRepository(Tricks::class)->findBy(
            [],
            ['createdAt' => 'DESC']
        );

        // Recupere la premiere image de chaque trick
        $images = [];
        foreach ($tricks as $trick) {
            $image = $trick->getImages()[0];
            $images[] = $image ?? null;
        }
        
        return $this->render('tricks/tricks.html.twig', [
            'tricks' => $tricks,
            'images' => $images
        ]);
    }

    #[Route('/trick/add', name: 'add_trick')]
    #[Route('/trick/edit/{slug}', name: 'edit_trick')]
    public function formTrick(Request $request, ?string $slug = null): Response
    {
        // Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $trick = null;
        $edit = false;
        if ($slug !== null) {
            // Récupérer le trick existant à modifier
            $trick = $this->entityManager->getRepository(Tricks::class)->findOneBy(['slug' => $slug]);
            // Si le trick n'existe pas ou si l'utilisateur qui la creer != de celui connecter on renvoie vers la page des tricks
            if (!$trick || $trick->getCreatedBy() != $this->getUser()) {
                return $this->redirectToRoute('tricks');
            }
            $edit = true;
            $existingImages = [];
            if ($trick !== null) {
                foreach ($trick->getImages() as $image) {
                    $existingImages[] = $image->getPath();
                }
            }
            } else {
                $trick = new Tricks();
            }    
        $form = $this->createForm(TricksType::class, $trick);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupere la personne qui a créé le trick
            $user = $this->getUser();
            $trick->setCreatedBy($user);

            // Recupere la date de création du trick
            $trick->setCreatedAt(new \DateTimeImmutable());

            // Creer le slug a partir du nom du trick
            $slugger = new AsciiSlugger();
            $slug = $slugger->slug($trick->getName());
            $trick->setSlug($slug);

            // Récupérer les images soumises
            $uploadedFilesArray = $form->get('images')->getData();

            // Supprime tous les images qui n'existe plus 
            $uploadImagesExist = $request->get('images');
            if ($uploadImagesExist === null) {
                $uploadImagesExist = [];
            }
            if (!isset($existingImages)) {
                $existingImages = [];
            }
            $imagesToDelete = array_diff($existingImages, $uploadImagesExist);
            
            foreach ($imagesToDelete as $image) {
                
                $imageEntity = $this->entityManager->getRepository(Images::class)->findOneBy(['path' => $image]);
                // Supprimez l'entité image de la base de données
                $this->entityManager->remove($imageEntity);

                // Supprimez également le fichier physique
                $filePath = "assets/img" . $image;
            
                // Vérifier si le fichier existe avant de le supprimer
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            $this->entityManager->flush();
 
            if (empty($uploadedFilesArray)) {
                $newImage = new Images();
                $newImage->setPath('defaut.jpg');
                $newImage->setAlt('Image sur le trick ' . $trick->getName());
                $trick->addImage($newImage);
            } else {
                // Traiter chaque fichier téléchargé
                foreach ($uploadedFilesArray as $uploadedFileArray) {
                    $uploadedFile = $uploadedFileArray;
            
                    if (isset($uploadedFile) && $uploadedFile instanceof UploadedFile) {
                        $fileName = uniqid() . '.' . $uploadedFile->guessExtension();
                        $uploadedFile->move('assets/img', $fileName);
            
                        $newImage = new Images();
                        $newImage->setPath($fileName);
                        $newImage->setAlt('Image sur le trick ' . $trick->getName());
            
                        // Associer l'entité Images au Trick
                        $trick->addImage($newImage);
                    }
                }
            }

            $this->entityManager->persist($trick);
            $this->entityManager->flush();
            $edit == false ? 
            $this->addFlash('success', "Votre tricks a été créer avec succes"): 
            $this->addFlash('success', "Votre tricks a été modifier avec succes");
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        $images = $trick->getImages();

        return $this->render('tricks/edit.html.twig', [
            'form' => $form->createView(),
            'edit' => $edit,
            'images' => $images
        ]);
    }

    #[Route('/trick/{slug}', name: 'trick_show')]
    public function show(Request $request, $slug): Response
    {
        $trick = $this->entityManager->getRepository(Tricks::class)->findOneBy(['slug' => $slug]);
        
        // Si le trick n'existe pas, on renvoie vers la page des tricks
        if (!$trick) {
            return $this->redirectToRoute('tricks');
            // dd('Le trick n\'existe pas');
        }

        $discussion = new Discussions();
        $discussions = $this->entityManager->getRepository(Discussions::class)
        ->createQueryBuilder('d')
        ->join('d.author', 'a')
        ->orderBy('a.isVerified', 'DESC')
        ->getQuery()
        ->getResult();

        $form = $this->createForm(DiscussionsType::class, $discussion);
        $form->handleRequest($request);

        $reply = new Reply();
        $replyForm = $this->createForm(ReplyType::class, $reply);
        $replyForm->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $discussion = $form->getData();
            $discussion->setIdTricks($trick);
            $discussion->setAuthor($this->getUser());
            $discussion->setCreatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($discussion);
            $this->entityManager->flush();
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->render('tricks/show.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
            'discussions' => $discussions,
            'replyForm' => $replyForm->createView()
        ]);
    }

    #[Route('/trick/delete/{id}', name: 'trick_delete')]
    public function delete($id): Response
    {
        // Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        //Supprimer un trick et ses images et videos
        $trick = $this->entityManager->getRepository(Tricks::class)->findOneBy(['id' => $id]);
        // Si le trick n'existe pas, on renvoie vers la page des tricks
        if (!$trick || $trick->getCreatedBy() != $this->getUser()) {
            return $this->redirectToRoute('tricks');
        }
        $images = $trick->getImages();
        $videos = $trick->getVideos();
        foreach ($images as $image) {
            $this->entityManager->remove($image);
        }
        foreach ($videos as $video) {
            $this->entityManager->remove($video);
        }
        $this->entityManager->remove($trick);
        $this->entityManager->flush();
        $this->addFlash('success', "Votre tricks a été supprimer avec succes");
        return $this->redirectToRoute('tricks');
    }

    #[Route('/trick/reply/{id}', name: 'reply')]
    public function reply(Request $request, $id): Response
    {
        $reply = new Reply();
        $replyForm = $this->createForm(ReplyType::class, $reply);
        $replyForm->handleRequest($request);

        if ($replyForm->isSubmitted() && $replyForm->isValid()) {
            $reply = $replyForm->getData();

            // Renseignez la discussion parent ici avec id mais depuis le slug de tricks
            $discussion = $this->entityManager->getRepository(Discussions::class)->findOneBy(['id' => $id]);
            

            $reply->setDiscussion($discussion);
    
            // Renseignez l'auteur de la réponse
            $reply->setAuthor($this->getUser());
    
            $this->entityManager->persist($reply);
    
            $this->entityManager->flush();
    
            // Rediriger vers la même page pour afficher la réponse nouvellement ajoutée
            return $this->redirectToRoute('trick_show', ['slug' => $discussion->getIdTricks()->getSlug()]);
        }
    }

}
