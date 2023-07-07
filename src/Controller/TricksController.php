<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Images;
use App\Entity\Tricks;
use App\Entity\Videos;
use App\Form\TricksType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
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
        $trick = null;
        $edit = false;
        if ($id !== null) {
            // Récupérer le trick existant à modifier
            $trick = $this->entityManager->getRepository(Tricks::class)->find($id);
            if (!$trick) {
                throw $this->createNotFoundException('Trick non trouvé.');
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

            // Pas besoin de la boucle foreach pour les vidéos

            $this->entityManager->persist($trick);
            $this->entityManager->flush();
            return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
        }

        return $this->render('tricks/add_index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/trick/{id}', name: 'trick_show')]
    public function show($id): Response
    {
        $trick = $this->entityManager->getRepository(Tricks::class)->findOneBy(['id' => $id]);
        
        return $this->render('tricks/show.html.twig', [
            'trick' => $trick
        ]);
    }

    #[Route('/trick/delete/{id}', name: 'trick_delete')]
    public function delete($id): Response
    {
        //Supprimer un trick et ses images et videos
        $trick = $this->entityManager->getRepository(Tricks::class)->findOneBy(['id' => $id]);
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

}
