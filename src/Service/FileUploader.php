<?php
/**
 * Created by PhpStorm.
 * Date: 20-Sep-18
 * Time: 16:36
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileUploader
 *
 * @package App\Service
 */
class FileUploader
{
    private $targetDirectory;

    /**
     * FileUploader constructor.
     *
     * @param $targetDirectory
     */
    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * @param UploadedFile $file
     *
     * @return string
     */
    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        $file->move($this->getTargetDirectory(), $fileName);

        return $fileName;
    }

    /**
     * @param string $fileName
     */
    public function delete(string $fileName)
    {
        $filePath = $this->getTargetDirectory() . '/' . $fileName;

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * @return mixed
     */
    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
