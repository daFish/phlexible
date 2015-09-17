<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaManager\Volume;

use Phlexible\Bundle\MediaManagerBundle\MediaManagerEvents;
use Phlexible\Component\Volume\Event\FileEvent;
use Phlexible\Component\Volume\Event\FolderEvent;
use Phlexible\Component\Volume\Exception\IOException;
use Phlexible\Component\Volume\Volume;

/**
 * Extended volume.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExtendedVolume extends Volume implements ExtendedVolumeInterface
{
    /**
     * {@inheritdoc}
     */
    public function setFolderMetasets(ExtendedFolderInterface $folder, array $metasets, $user)
    {
        $folder
            ->setMetasets($metasets)
            ->setModifiedAt(new \DateTime())
            ->setModifyUser($user);

        $event = new FolderEvent($folder);
        if ($this->getEventDispatcher()->dispatch(MediaManagerEvents::BEFORE_SET_FOLDER_METASETS, $event)->isPropagationStopped()) {
            throw new IOException("Move folder {$folder->getName()} cancelled.");
        }

        $this->getVolumeManager()->updateFolder($folder);

        $event = new FolderEvent($folder);
        $this->getEventDispatcher()->dispatch(MediaManagerEvents::SET_FOLDER_METASETS, $event);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileMetasets(ExtendedFileInterface $file, array $metasets, $user)
    {
        $file
            ->setMetasets($metasets)
            ->setModifiedAt(new \DateTime())
            ->setModifyUser($user);

        $event = new FileEvent($file);
        if ($this->getEventDispatcher()->dispatch(MediaManagerEvents::BEFORE_SET_FILE_METASETS, $event)->isPropagationStopped()) {
            throw new IOException("Set file meta sets {$file->getName()} cancelled.");
        }

        $this->getVolumeManager()->updateFile($file);

        $event = new FileEvent($file);
        $this->getEventDispatcher()->dispatch(MediaManagerEvents::SET_FILE_METASETS, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileMediaType(ExtendedFileInterface $file, $mediaType, $user)
    {
        $file
            ->setMediaType($mediaType)
            ->setModifiedAt(new \DateTime())
            ->setModifyUser($user);

        $event = new FileEvent($file);
        if ($this->getEventDispatcher()->dispatch(MediaManagerEvents::BEFORE_SET_FILE_MEDIA_TYPE, $event)->isPropagationStopped()) {
            throw new IOException("Set file media type {$file->getName()} cancelled.");
        }

        $this->getVolumeManager()->updateFile($file);

        $event = new FileEvent($file);
        $this->getEventDispatcher()->dispatch(MediaManagerEvents::SET_FILE_MEDIA_TYPE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileMimeType(ExtendedFileInterface $file, $mimeType, $userId)
    {
        $file
            ->setMimeType($mimeType)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        $event = new FileEvent($file);
        if ($this->getEventDispatcher()->dispatch(MediaManagerEvents::BEFORE_SET_FILE_MIME_TYPE, $event)->isPropagationStopped()) {
            throw new IOException("Set file mime type {$file->getName()} cancelled.");
        }

        $this->getDriver()->updateFile($file);

        $event = new FileEvent($file);
        $this->getEventDispatcher()->dispatch(MediaManagerEvents::SET_FILE_MIME_TYPE, $event);

        return $file;
    }
}
