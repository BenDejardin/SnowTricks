<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Reply;
use App\Entity\Images;
use App\Entity\Tricks;
use App\Entity\Videos;
use App\Form\TricksType;
use App\Entity\Discussions;
use App\Form\DiscussionsType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
// ReplyType
use App\Form\ReplyType;

// DiscussionType
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
        $tricks = $this->entityManager->getRepository(Tricks::class)->findAll();

        // Recupere la premiere image de chaque trick
        $images = [];
        foreach ($tricks as $trick) {
            $image = $this->entityManager->getRepository(Images::class)->findOneBy(['trick' => $trick->getId()]);
            $images[] = $image ?? null;
        }


        
        return $this->render('tricks/tricks_index.html.twig', [
            'tricks' => $tricks,
            'images' => $images
        ]);
    }

    #[Route('/trick/add', name: 'add_trick')]
    #[Route('/trick/edit/{id}', name: 'edit_trick')]
    public function formTrick(Request $request, ?int $id = null): Response
    {
        // Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $trick = null;
        $edit = false;
        if ($id !== null) {
            // Récupérer le trick existant à modifier
            $trick = $this->entityManager->getRepository(Tricks::class)->find($id);
            // Si le trick n'existe pas, on renvoie vers la page des tricks
            if (!$trick) {
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

            // Récupérer les images soumises
            $uploadedFiles = $form->get('images')->getData();

            // Traiter chaque fichier téléchargé
            foreach ($uploadedFiles as $uploadedFile) {
                // Vérifier si un fichier d'image a été soumis
                if ($uploadedFile instanceof UploadedFile) {
                    // Gérer le téléchargement du fichier ici (par exemple, le déplacer vers un répertoire)
                    $fileName = uniqid().'.'.$uploadedFile->guessExtension();
                    $uploadedFile->move('assets/img', $fileName);

                    // Créer une nouvelle instance de l'entité Images
                    $newImage = new Images();
                    $newImage->setPath($fileName);
                    $newImage->setAlt('Image sur le trick ' . $trick->getName());

                    // Associer l'entité Images au Trick
                    $trick->addImage($newImage);
                }
            }

            $this->entityManager->persist($trick);
            $this->entityManager->flush();
            return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
        }

        return $this->render('tricks/add_index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/trick/{id}', name: 'trick_show')]
    public function show(Request $request, $id): Response
    {
        $trick = $this->entityManager->getRepository(Tricks::class)->findOneBy(['id' => $id]);
        
        // Si le trick n'existe pas, on renvoie vers la page des tricks
        if (!$trick) {
            // return $this->redirectToRoute('tricks');
            // dd('Le trick n\'existe pas');
        }

        $discussion = new Discussions();
        $discussions = $this->entityManager->getRepository(Discussions::class)->findBy(['trick' => $trick->getId()]);

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
            return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
        }

        // if ($replyForm->isSubmitted() && $replyForm->isValid()) {
        //     $reply = $replyForm->getData();

        //     // Renseignez la discussion parent ici
        //     $reply->setDiscussion($id);
    
        //     // Renseignez l'auteur de la réponse
        //     $reply->setAuthor($this->getUser());
    
        //     $this->entityManager->persist($reply);
    
        //     $this->entityManager->flush();
    
        //     // Rediriger vers la même page pour afficher la réponse nouvellement ajoutée
        //     return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
        // }

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
        if (!$trick) {
            return $this->redirectToRoute('tricks');
        }
        $images = $this->entityManager->getRepository(Images::class)->findBy(['trick' => $id]);
        $videos = $this->entityManager->getRepository(Videos::class)->findBy(['trick' => $id]);
        foreach ($images as $image) {
            $this->entityManager->remove($image);
        }
        foreach ($videos as $video) {
            $this->entityManager->remove($video);
        }
        $this->entityManager->remove($trick);
        $this->entityManager->flush();
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

            // Renseignez la discussion parent ici
            $discussion = $this->entityManager->getRepository(Discussions::class)->findOneBy(['id' => $id]);

            $reply->setDiscussion($discussion);
    
            // Renseignez l'auteur de la réponse
            $reply->setAuthor($this->getUser());
    
            $this->entityManager->persist($reply);
    
            $this->entityManager->flush();
    
            // Rediriger vers la même page pour afficher la réponse nouvellement ajoutée
            return $this->redirectToRoute('trick_show', ['id' => $discussion->getIdTricks()->getId()]);
        }
    }

}
