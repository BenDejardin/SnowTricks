<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Tricks;
use App\Entity\Images;

class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {

        // Recupere tous les tricks
        $tricks = $this->entityManager->getRepository(Tricks::class)->findAll();

        // Recupere la premiere image de chaque trick
        $images = [];
        foreach ($tricks as $trick) {
            $image = $this->entityManager->getRepository(Images::class)->findOneBy(['trick' => $trick->getId()]);
            $images[] = $image ?? null;
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'tricks' => $tricks,
            'images' => $images
        ]);
    }
}
