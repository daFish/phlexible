<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaManager\Upload;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\Volume\FileSource\UploadedFileSource;
use Phlexible\Component\Volume\Model\VolumeManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Upload handler
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UploadHandler
{
    /**
     * @var VolumeManagerInterface
     */
    private $volumeManager;

    /**
     * @var TempStorage
     */
    private $tempStorage;

    /**
     * @param VolumeManagerInterface $volumeManager
     * @param TempStorage            $tempStorage
     */
    public function __construct(VolumeManagerInterface $volumeManager, TempStorage $tempStorage)
    {
        $this->volumeManager = $volumeManager;
        $this->tempStorage = $tempStorage;
    }

    /**
     * @return bool
     */
    private function useWizard()
    {
        return false;
    }

    /**
     * Handle upload
     *
     * @param UploadedFile $uploadedFile
     * @param string       $folderId
     * @param string       $userId
     *
     * @return TempFile|ExtendedFileInterface
     */
    public function handle(UploadedFile $uploadedFile, $folderId, $userId)
    {
        $file = new File($uploadedFile->getPathname());
        $mimetype = $file->getMimeType();
        $uploadFileSource = new UploadedFileSource($uploadedFile, (string) $mimetype);

        $volume = $this->volumeManager->getByFolderId($folderId);
        $folder = $volume->findFolder($folderId);
        $file = $volume->findFileByFolderAndName($folder, $uploadFileSource->getName());
        $originalFileId = null;

        if ($file) {
            $originalFileId = $file->getId();
        }

        $useWizard = $this->useWizard();

        if ($originalFileId || $useWizard) {
            return $this->tempStorage->store(
                $uploadFileSource,
                $folderId,
                $userId,
                $originalFileId,
                $useWizard
            );
        }

        $file = $volume->createFile($folder, $uploadFileSource, array(), $userId);

        return $file;
    }
}
