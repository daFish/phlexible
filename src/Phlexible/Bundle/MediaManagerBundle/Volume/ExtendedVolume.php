<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Volume;

use Phlexible\Bundle\MediaManagerBundle\MediaManagerEvents;
use Phlexible\Component\Volume\Event\FileEvent;
use Phlexible\Component\Volume\Event\FolderEvent;
use Phlexible\Component\Volume\Exception\IOException;
use Phlexible\Component\Volume\Volume;

/**
 * Extended volume
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExtendedVolume extends Volume implements ExtendedVolumeInterface
{
    /**
     * {@inheritdoc}
     */
    public function setFolderMetasets(ExtendedFolderInterface $folder, array $metasets, $userId)
    {
        $folder
            ->setMetasets($metasets)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        $event = new FolderEvent($folder);
        if ($this->getEventDispatcher()->dispatch(MediaManagerEvents::BEFORE_SET_FOLDER_METASETS, $event)->isPropagationStopped()) {
            throw new IOException("Move folder {$folder->getName()} cancelled.");
        }

        $this->getDriver()->updateFolder($folder);

        $event = new FolderEvent($folder);
        $this->getEventDispatcher()->dispatch(MediaManagerEvents::SET_FOLDER_METASETS, $event);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileMetasets(ExtendedFileInterface $file, array $metasets, $userId)
    {
        $file
            ->setMetasets($metasets)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        $event = new FileEvent($file);
        if ($this->getEventDispatcher()->dispatch(MediaManagerEvents::BEFORE_SET_FILE_METASETS, $event)->isPropagationStopped()) {
            throw new IOException("Delete file {$file->getName()} cancelled.");
        }

        $this->getDriver()->updateFile($file);

        $event = new FileEvent($file);
        $this->getEventDispatcher()->dispatch(MediaManagerEvents::SET_FILE_METASETS, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileAssetType(ExtendedFileInterface $file, $assetType, $userId)
    {
        $file
            ->setAssetType($assetType)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        $event = new FileEvent($file);
        if ($this->getEventDispatcher()->dispatch(MediaManagerEvents::BEFORE_SET_FILE_ASSET_TYPE, $event)->isPropagationStopped()) {
            throw new IOException("Delete file {$file->getName()} cancelled.");
        }

        $this->getDriver()->updateFile($file);

        $event = new FileEvent($file);
        $this->getEventDispatcher()->dispatch(MediaManagerEvents::SET_FILE_ASSET_TYPE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileDocumenttype(ExtendedFileInterface $file, $documenttype, $userId)
    {
        $file
            ->setDocumenttype($documenttype)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        $event = new FileEvent($file);
        if ($this->getEventDispatcher()->dispatch(MediaManagerEvents::BEFORE_SET_FILE_DOCUMENTTYPE, $event)->isPropagationStopped()) {
            throw new IOException("Delete file {$file->getName()} cancelled.");
        }

        $this->getDriver()->updateFile($file);

        $event = new FileEvent($file);
        $this->getEventDispatcher()->dispatch(MediaManagerEvents::SET_FILE_DOCUMENTTYPE, $event);

        return $file;
    }
}
