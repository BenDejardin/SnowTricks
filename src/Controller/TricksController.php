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
        return $this->render('tricks/tricks_index.html.twig', [
            'controller_name' => 'TricksController',
        ]);
    }

    #[Route('/trick/add', name: 'add_trick')]
    public function indexAdd(Request $request): Response
{
    $trick = new Tricks();
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

        // Enregistrer les vidéos associées au trick
        $iframe = $form->get('videos')->getData();

        // Utiliser DOMCrawler pour extraire les balises <iframe>
        // $crawler = new Crawler($videosData);
        // $iframes = $crawler->filter('iframe');

        // foreach ($videosData as $iframe) {
            $video = new Videos();
            $video->setIframe($iframe);
            // $video->getTrick($trick->getId());
            $trick->addVideo($video);

            // $this->entityManager->persist($video);
        // }

        $this->entityManager->persist($trick);
        $this->entityManager->flush();
        return $this->redirectToRoute('home');
    }

    return $this->render('tricks/add_index.html.twig', [
        'form' => $form->createView(),
    ]);
}

//     #[Route('/trick/add', name: 'add_trick')]
//     public function add(Request $request): Response
//     {
//     $groupRepository = $this->entityManager->getRepository(Group::class);
//     $groups = $groupRepository->findAll();

//     if ($request->isMethod('POST')) {
//         // Récupération des données du formulaire
//         $formData = $request->request->all();
//         dump($formData);

//         // Création d'une nouvelle entité Trick
//         $trick = new Tricks();
//         $trick->setName($formData['name']);
//         $trick->setDescription($formData['description']);
//         $groupId = $formData['group'];
//         $group = $groupRepository->find($groupId);
//         if (!$group) {
//             throw $this->createNotFoundException('Groupe non trouvé');
//         }
//         $trick->setIdGroup($group);

        // Gestion des images
        // $uploadedFiles = $request->files->get('illustration');
        // if ($uploadedFiles) {
        //     foreach ($uploadedFiles as $uploadedFile) {
        //         // Vérifiez si le fichier est valide
        //         if ($uploadedFile instanceof UploadedFile && $uploadedFile->isValid()) {
        //             // Générez un nom de fichier unique pour éviter les conflits
        //             $fileName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();

        //             // Déplacez le fichier vers le répertoire de destination
        //             $uploadedFile->move(
        //                 'assets/img',
        //                 $fileName
        //             );

        //             // Création d'une nouvelle entité Image
        //             $image = new Images();
        //             $image->setPath($fileName);
        //             $image->setAlt('Image sur le trick ' . $trick->getName());
        //             $image->setTricks($trick);

        //             // Ajout de l'image au trick
        //             $trick->addImage($image);
        //         }
        //     }
        // }

//         // Gestion des vidéos
//         if ($formData['video']) {
//             $video = new Videos();
//             $video->setUrl($formData['video']);
//             $video->setTrick($trick);

//             // Ajout de la vidéo au trick
//             $trick->addVideo($video);
//         }

//         // Persistez l'entité dans l'EntityManager
//         $this->entityManager->persist($trick);
//         $this->entityManager->flush();

//         // Redirection vers une autre page ou affichage d'un message de succès
//         return $this->redirectToRoute('trick_list');
//     }
// }
}
