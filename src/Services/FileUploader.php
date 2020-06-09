<?php
namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class FileUploader
{
    private $targetDirectory;
    private $targetDirectoryCorp;

    public function __construct($targetDirectory,$targetDirectoryCorp)
    {
        $this->targetDirectory = $targetDirectory;
        $this->targetDirectoryCorp = $targetDirectoryCorp;
    }

    public function upload(UploadedFile $file,int $id = null,string $filename=null)
    {
        if(is_null($filename)){
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
            $finalFileName = $id.'_'.$safeFilename.'.'.$file->guessExtension();
        }
        else {
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $filename);
            $finalFileName = $safeFilename.'.'.$file->guessExtension();
        }

        try {
            $file->move($this->getTargetDirectory(), $finalFileName);
        } catch (FileException $e) {
            new \Exception('El video ya esta disponible');
        }

        return $finalFileName;
    }
    public function uploadImagenCorp(UploadedFile $file,int $id = null)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $id.'_'.$safeFilename.'.'.$file->guessExtension();
        try {
            $file->move($this->getTargetDirectoryCorp(), $fileName);
        } catch (FileException $e) {
            new \Exception('La imagen corporativa ya esta disponible');
        }

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    public function getTargetDirectoryCorp()
    {
        return $this->targetDirectoryCorp;
    }
}
