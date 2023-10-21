<?php
namespace App\Service;

use App\Entity\Tricks;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TrickManager
{
    private $entityManager;
    private $slugger;
    private $security;
    private $imageManager;

    public function __construct(EntityManagerInterface $entityManager, Security $security, ImageManager $imageManager, AsciiSlugger $slugger)
    {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
        $this->security = $security;
        $this->imageManager = $imageManager;
    }

    public function findTrickBySlug(string $slug): Tricks
    {
        $trick = $this->entityManager->getRepository(Tricks::class)->findOneBy(['slug' => $slug]);

        return $trick;
    }

    public function createTrick($trickDataFrom)
    {
        $user = $this->security->getUser();
        $trick = new Tricks;
        $trick
            ->setCreatedBy($user)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setSlug($this->slugger->slug($trickDataFrom->get('name')->getData()))
            ->setName($trickDataFrom->get('name')->getData())
            ->setIdGroup($trickDataFrom->get('id_group')->getData())
            ->setDescription($trickDataFrom->get('description')->getData());
        
            foreach($trickDataFrom->get('videos')->getData() as $video){
                $trick->addVideo($video);
            }

            $trick = $this->imageManager->uploadImages($trickDataFrom->get('images')->getData(), 'assets/img', $trick);

        $this->entityManager->persist($trick);
        $this->entityManager->flush();
        return $trick;
    }

    public function updateTrick($trickDataFrom, Tricks $trick, $request)
    {
        $existingImages = $this->imageManager->getExistingImages($trick);
        $trick
            ->setSlug($this->slugger->slug($trickDataFrom->get('name')->getData()))
            ->setName($trickDataFrom->get('name')->getData())
            ->setIdGroup($trickDataFrom->get('id_group')->getData())
            ->setDescription($trickDataFrom->get('description')->getData());

        foreach($trickDataFrom->get('videos')->getData() as $video){
            $trick->addVideo($video);
        }

        $trick = $this->imageManager->handleUploadedFiles($trickDataFrom->get('images')->getData(), $existingImages, 'assets/img', $trick, $request); 

        $this->entityManager->flush();
        return $trick;
    }

    public function deleteTrick(Tricks $trick)
    {

        $this->entityManager->remove($trick);
        $this->entityManager->flush();
    }
}
