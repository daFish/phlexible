<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaManager\Upload;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\Volume\FileSource\UploadedFileSource;
use Phlexible\Component\Volume\VolumeManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Temp\MimeSniffer\MimeSniffer;

/**
 * Upload handler
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UploadHandler
{
    /**
     * @var VolumeManager
     */
    private $volumeManager;

    /**
     * @var TempStorage
     */
    private $tempStorage;

    /**
     * @var MimeSniffer
     */
    private $mimeSniffer;

    /**
     * @param VolumeManager $volumeManager
     * @param TempStorage   $tempStorage
     * @param MimeSniffer   $mimeSniffer
     */
    public function __construct(VolumeManager $volumeManager, TempStorage $tempStorage, MimeSniffer $mimeSniffer)
    {
        $this->volumeManager = $volumeManager;
        $this->tempStorage = $tempStorage;
        $this->mimeSniffer = $mimeSniffer;
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
        $mimetype = $this->mimeSniffer->detect($uploadedFile->getPathname());
        $uploadFileSource = new UploadedFileSource($uploadedFile, (string) $mimetype);

        $volume = $this->volumeManager->getByFolderId($folderId);
        $folder = $volume->findFolder($folderId);
        $file = $volume->findFileByPath($folder->getPath() . '/' . $uploadFileSource->getName());
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
