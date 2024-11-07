<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

trait ImageUploaderTrait
{
    public function uploadImage(UploadedFile $file, string $targetDirectory, SluggerInterface $slugger): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($targetDirectory, $newFilename);
        } catch (FileException $e) {
            throw new \Exception('Impossible de télécharger l\'image');
        }

        return '/uploads/images/' . $newFilename;
    }
}
