<?php

namespace App\Core\Form;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * class FileUploadHandler.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class FileUploadHandler
{
    public function handleForm(?UploadedFile $uploadedFile, string $path, callable $afterUploadCallback): void
    {
        if (null === $uploadedFile) {
            return;
        }

        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $filename = date('Ymd-his').$safeFilename.'.'.$uploadedFile->guessExtension();

        $uploadedFile->move($path, $filename);

        $afterUploadCallback($filename);
    }
}
