<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Tricks;
use App\Service\ImageManager;

class HomeController extends AbstractController
{
    private $entityManager;
    private $imageManager;

    public function __construct(EntityManagerInterface $entityManager, ImageManager $imageManager)
    {
        $this->entityManager = $entityManager;
        $this->imageManager = $imageManager;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {

        // Recupere tous les tricks
        $tricks = $this->entityManager->getRepository(Tricks::class)->findByCreationDate();

        // Recupere la premiere image de chaque trick
        $images = $this->imageManager->getFirstImagesByTricks($tricks);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'tricks' => $tricks,
            'images' => $images
        ]);
    }
}
