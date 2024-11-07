<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Throwable;
use Symfony\Component\String\Slugger\AsciiSlugger;


class FileUploaderService
{
    private string $baseUploadDir;
    private Filesystem $filesystem;
    private AsciiSlugger $slugger;

    public function __construct(
        KernelInterface                  $kernel,
        private readonly LoggerInterface $logger,
        ?string                          $baseUploadDir = null
    ){
        $this->baseUploadDir = rtrim($baseUploadDir ?? ($kernel->getProjectDir() . '/public/uploads/files'), '/');
        $this->filesystem = new Filesystem();
        $this->slugger = new AsciiSlugger('fr');
    }

    public function upload(UploadedFile $file, string $model): ?string
    {
        $model = strtolower(trim($model));
        $modelSlug = $this->slugger->slug($model, '-')->toString() ?: 'autres';
        $targetDir = $this->baseUploadDir . '/' . $modelSlug;

        try {
            $oldUmask = umask(0002);
            $this->filesystem->mkdir($targetDir, 0775);
            umask($oldUmask);

            if (!is_writable($targetDir)) {
                $this->logger->error('Upload target not writable', [
                    'targetDir' => $targetDir,
                    'perms' => @substr(sprintf('%o', @fileperms($targetDir)), -4),
                ]);
                return null;
            }

            $ext = $file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'pdf';
            if ($ext === 'jpeg') {
                $ext = 'jpg';
            }

            $newFilename = sprintf('%s_%s.%s', $modelSlug, date('YmdHis'), $ext);
            $this->logger->info('Moving uploaded file', [
                'tmp' => $file->getPathname(),
                'targetDir' => $targetDir,
                'name' => $newFilename,
                'size' => $file->getSize(),
                'error' => $file->getError(),
                'errorMsg' => method_exists($file, 'getErrorMessage') ? $file->getErrorMessage() : null,
            ]);
            $file->move($targetDir, $newFilename);

            $publicPath = sprintf('/uploads/files/%s/%s', $modelSlug, $newFilename);
            $this->logger->info(' FileUploaderService :: ', [
                'model' => $modelSlug,
                'publicPath' => $publicPath,
                'absolutePath' => $targetDir . '/' . $newFilename
            ]);

            return $publicPath;
        } catch (Throwable $e) {
            $this->logger->error('mkdir failed', [
                'targetDir' => $targetDir,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
