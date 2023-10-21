<?php
namespace App\Service;

use App\Entity\Images;
use App\Entity\Tricks;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageManager{

    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function uploadFile(UploadedFile $file, string $uploadDirectory): string
    {
        $fileName = uniqid() . '.' . $file->guessExtension();
        $file->move($uploadDirectory, $fileName);
        return $fileName;
    }

    public function deleteFile(string $filePath)
    {
        if (file_exists($filePath) && is_writable($filePath)) {
            unlink($filePath);
        }
    }

    public function getExistingImages(Tricks $trick): array
    {
        $existingImages = [];

        foreach ($trick->getImages() as $image) {
            $existingImages[] = $image->getPath();
        }

        return $existingImages;
    }

    public function handleUploadedFiles(array $uploadedFiles, array $existingImages, string $uploadDirectory, Tricks $trick, $request)
    {
        $this->uploadImages($uploadedFiles, $uploadDirectory, $trick);

        $uploadImagesForm = $this->getUploadedFileNames($uploadedFiles);
        foreach($request->get('images') as $oldImage){
            $uploadImagesForm[] = $oldImage;
        }
        
        $imagesToDelete = array_diff($existingImages, $uploadImagesForm);
        foreach ($imagesToDelete as $image) {
            $this->deleteFile($uploadDirectory . '/' . $image);
            $imageEntity = $this->entityManager->getRepository(Images::class)->findOneBy(['path' => $image]);
            if ($imageEntity) {
                $this->entityManager->remove($imageEntity);
            }
        }

        return $trick;
    }

    public function uploadImages(array $uploadedFiles, string $uploadDirectory, Tricks $trick){
        if(empty($uploadedFiles)){
            $newImage = new Images();
            $newImage->setPath('defaut.jpg');
            $newImage->setAlt('Image sur le trick ' . $trick->getName());
            $trick->addImage($newImage);
        }
        else{
            foreach ($uploadedFiles as $uploadedFile) {
                if ($uploadedFile instanceof UploadedFile) {
                    $fileName = $this->uploadFile($uploadedFile, $uploadDirectory);

                    $newImage = new Images();
                    $newImage->setPath($fileName);
                    $newImage->setAlt('Image sur le trick ' . $trick->getName());

                    $trick->addImage($newImage);
                }
            }
        }
        return $trick;
    }


    private function getUploadedFileNames(array $uploadedFiles): array
    {
        $fileNames = [];

        foreach ($uploadedFiles as $uploadedFile) {
            if ($uploadedFile instanceof UploadedFile) {
                $fileNames[] = $uploadedFile->getClientOriginalName();
            }
        }

        return $fileNames;
    }

    public function getFirstImagesByTricks($tricks): array
    {
        $images = [];
        foreach ($tricks as $trick) {
            $image = $trick->getImages()[0];
            $images[] = $image ?? null;
        }
        return $images;
    }
}