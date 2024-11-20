<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageUploadService
{
    public function __construct(protected string $targetDirectory, protected SluggerInterface $slugger)
    {
    }

    /**
     * @throws FileException
     */
    public function upload(UploadedFile $file): string
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = $file->guessExtension();

        if (!in_array($extension, $allowedExtensions)) {
            throw new FileException('Extension de fichier non autorisée.');
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $newFilename);
        } catch (FileException $e) {
            throw new FileException('Impossible de télécharger l\'image: ' . $e->getMessage());
        }

        return '/uploads/images/' . $newFilename;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}