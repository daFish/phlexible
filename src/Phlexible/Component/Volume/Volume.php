<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Volume;

use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Component\Volume\Event\CopyFileEvent;
use Phlexible\Component\Volume\Event\CopyFolderEvent;
use Phlexible\Component\Volume\Event\CreateFileEvent;
use Phlexible\Component\Volume\Event\FileEvent;
use Phlexible\Component\Volume\Event\FolderEvent;
use Phlexible\Component\Volume\Event\MoveFileEvent;
use Phlexible\Component\Volume\Event\MoveFolderEvent;
use Phlexible\Component\Volume\Event\RenameFileEvent;
use Phlexible\Component\Volume\Event\RenameFolderEvent;
use Phlexible\Component\Volume\Event\ReplaceFileEvent;
use Phlexible\Component\Volume\Exception\IOException;
use Phlexible\Component\Volume\FileSource\FileSourceInterface;
use Phlexible\Component\Volume\FileSource\FilesystemFileSource;
use Phlexible\Component\Volume\Model\FileInterface;
use Phlexible\Component\Volume\Model\FolderInterface;
use Phlexible\Component\Volume\Model\FolderIterator;
use Phlexible\Component\Volume\Model\VolumeManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Expression\Expression;

/**
 * Volume
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Volume implements VolumeInterface, \IteratorAggregate
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var int
     */
    private $quota;

    /**
     * @var VolumeManagerInterface
     */
    private $volumeManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param string                   $id
     * @param string                   $name
     * @param string                   $rootDir
     * @param int                      $quota
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        $id,
        $name,
        $rootDir,
        $quota,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->rootDir = $rootDir;
        $this->quota = $quota;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return FolderIterator
     */
    public function getIterator()
    {
        return new FolderIterator($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuota()
    {
        return $this->quota;
    }

    /**
     * {@inheritdoc}
     */
    public function getVolumeManager()
    {
        return $this->volumeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setVolumeManager(VolumeManagerInterface $volumeManager)
    {
        $this->volumeManager = $volumeManager;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFeature($feature)
    {
        return $this->volumeManager->hasFeature($feature);
    }

    /**
     * {@inheritdoc}
     */
    public function findRootFolder()
    {
        return $this->volumeManager->findFolderBy(array('volumeId' => $this->id, 'parentId' => null));
    }

    /**
     * {@inheritdoc}
     */
    public function findFolder($id)
    {
        return $this->volumeManager->findFolder($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findFolderByFileId($fileId)
    {
        $file = $this->findFile($fileId);

        return $this->findFolder($file->getFolderId());
    }

    /**
     * {@inheritdoc}
     */
    public function findFolderByPath($path)
    {
        return $this->volumeManager->findFolderBy(array('volumeId' => $this->id, 'path' => $path));
    }

    /**
     * {@inheritdoc}
     */
    public function findFoldersByParentFolder(FolderInterface $parentFolder)
    {
        return $this->volumeManager->findFolderBy(array('volumeId' => $this->id, 'path' => $parentFolder->getId()));
    }

    /**
     * {@inheritdoc}
     */
    public function countFoldersByParentFolder(FolderInterface $parentFolder)
    {
        return $this->volumeManager->countFoldersBy(array('volumeId' => $this->id, 'parentId' => $parentFolder->getId()));
    }

    /**
     * {@inheritdoc}
     */
    public function findFile($id, $version = 1)
    {
        return $this->volumeManager->findFileBy(array('id' => $id, 'version' => $version));
    }

    /**
     * {@inheritdoc}
     */
    public function findFiles(array $criteria, $order = null, $limit = null, $start = null)
    {
        return $this->volumeManager->findFilesBy($criteria, $order, $limit, $start);
    }

    /**
     * {@inheritdoc}
     */
    public function countFiles(array $criteria)
    {
        return $this->volumeManager->countFilesBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findFileByFolderAndName(FolderInterface $folder, $name)
    {
        return $this->volumeManager->findFileBy(array('folder' => $folder->getId(), 'name' => $name));
    }

    /**
     * {@inheritdoc}
     */
    public function findFileVersions($id)
    {
        return $this->volumeManager->findFileBy(array('id' => $id));
    }

    /**
     * {@inheritdoc}
     */
    public function findFilesByFolder(
        FolderInterface $folder,
        $order = null,
        $limit = null,
        $start = null,
        $includeHidden = false)
    {
        $criteria = array(
            'folder' => $folder->getId(),
        );
        if (!$includeHidden) {
            $criteria['hidden'] = false;
        }

        return $this->volumeManager->findFilesBy($criteria, $order, $limit, $start);
    }

    /**
     * {@inheritdoc}
     */
    public function countFilesByFolder(FolderInterface $folder, $includeHidden = false)
    {
        $criteria = array(
            'folder' => $folder->getId(),
        );
        if (!$includeHidden) {
            $criteria['hidden'] = false;
        }

        return $this->volumeManager->countFilesBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findLatestFiles($limit = 20)
    {
        return $this->volumeManager->findFilesBy(array(), array('createdAt' => 'DESC', $limit));
    }

    /**
     * {@inheritdoc}
     */
    public function findFilesByExpression(Expression $expression, $order = null, $limit = null, $start = null)
    {
        return $this->volumeManager->findFilesByExpression($expression, $order, $limit, $start);
    }

    /**
     * {@inheritdoc}
     */
    public function countFilesByExpression(Expression $expression)
    {
        return $this->volumeManager->countFilesByExpression($expression);
    }

    /**
     * {@inheritdoc}
     */
    public function getPhysicalPath(FileInterface $file)
    {
        $rootDir = rtrim($this->getRootDir(), '/');
        $physicalPath = $rootDir . '/' . $file->getHash();

        return $physicalPath;
    }

    /**
     * /**
     * {@inheritdoc}
     */
    public function createFile(
        FolderInterface $targetFolder,
        FileSourceInterface $fileSource,
        array $attributes,
        $user)
    {
        $hash = $this->volumeManager->getHashCalculator()->fromFileSource($fileSource);

        // prepare folder's name and id
        $fileClass = $this->volumeManager->getFileClass();
        $file = new $fileClass();
        /* @var $file FileInterface */
        $file
            ->setVolume($this)
            ->setId(Uuid::generate())
            ->setFolder($targetFolder)
            ->setName($fileSource->getName())
            ->setCreatedAt(new \DateTime())
            ->setCreateUser($user)
            ->setModifiedAt($file->getCreatedAt())
            ->setModifyUser($file->getCreateUser())
            ->setMimeType($fileSource->getMimeType())
            ->setSize($fileSource->getSize())
            ->setHash($hash)
            ->setAttributes($attributes);

        $event = new CreateFileEvent($file, $fileSource);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_CREATE_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Create file {$file->getName()} failed.");
        }

        $this->volumeManager->createFile($file, $fileSource);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::CREATE_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function replaceFile(
        FileInterface $file,
        FileSourceInterface $fileSource,
        array $attributes,
        $user)
    {
        $hash = $this->volumeManager->getHashCalculator()->fromFileSource($fileSource);

        $file
            ->setName($fileSource->getName())
            ->setSize($fileSource->getSize())
            ->setHash($hash)
            ->setAttributes($attributes)
            ->setModifiedAt(new \DateTime())
            ->setModifyUser($user);

        $event = new ReplaceFileEvent($file, $fileSource);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_REPLACE_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Create file {$file->getName()} failed.");
        }

        $this->volumeManager->replaceFile($file, $fileSource);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::REPLACE_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function renameFile(FileInterface $file, $name, $user)
    {
        if ($file->getName() === $name) {
            return $file;
        }

        $oldName = $file->getName();
        $file
            ->setName($name)
            ->setModifiedAt(new \DateTime())
            ->setModifyUser($user);

        $this->volumeManager->validateRenameFile($file, $file->getFolder());

        $event = new RenameFileEvent($file, $oldName);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_RENAME_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Rename file {$file->getName()} cancelled.");
        }

        $this->volumeManager->updateFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::RENAME_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function moveFile(FileInterface $file, FolderInterface $targetFolder, $user)
    {
        if ($file->getFolder()->getId() === $targetFolder->getId()) {
            return $file;
        }

        $file
            ->setFolder($targetFolder)
            ->setModifiedAt(new \DateTime())
            ->setModifyUser($user);

        $this->volumeManager->validateMoveFile($file, $targetFolder);

        $event = new MoveFileEvent($file, $targetFolder);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_MOVE_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Move file {$file->getName()} cancelled.");
        }

        $this->volumeManager->updateFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::MOVE_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function copyFile(FileInterface $originalFile, FolderInterface $targetFolder, $user)
    {
        $file = clone $originalFile;
        $file
            ->setId(Uuid::generate())
            ->setCreatedAt(new \DateTime())
            ->setCreateUser($user)
            ->setModifiedAt($file->getCreatedAt())
            ->setModifyUser($file->getCreateUser())
            ->setFolder($targetFolder);

        $this->volumeManager->validateCopyFile($file, $targetFolder);

        $event = new CopyFileEvent($file, $originalFile, $targetFolder);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_COPY_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Copy file {$file->getName()} cancelled.");
        }

        $this->volumeManager->updateFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::COPY_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function checkDeleteFile(FileInterface $file)
    {
        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::CHECK_DELETE_FILE, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile(FileInterface $file, $user)
    {
        $event = new FileEvent($file);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_DELETE_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Delete file {$file->getName()} cancelled.");
        }

        $this->volumeManager->deleteFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::DELETE_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function showFile(FileInterface $file, $user)
    {
        $file->setHidden(false);

        $event = new FileEvent($file);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_SHOW_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Show file {$file->getName()} cancelled.");
        }

        $this->volumeManager->updateFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::SHOW_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function hideFile(FileInterface $file, $user)
    {
        $file->setHidden(true);

        $event = new FileEvent($file);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_HIDE_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Hide file {$file->getName()} cancelled.");
        }

        $this->volumeManager->updateFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::HIDE_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileAttributes(FileInterface $file, array $attributes, $user)
    {
        $file
            ->setModifiedAt(new \DateTime())
            ->setModifyUser($user)
            ->setAttributes($attributes);

        $event = new FileEvent($file);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_SET_FILE_ATTRIBUTES, $event)->isPropagationStopped()) {
            throw new IOException("Delete file {$file->getName()} cancelled.");
        }

        $this->volumeManager->updateFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::SET_FILE_ATTRIBUTES, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function createFolder(FolderInterface $targetFolder = null, $name, array $attributes, $user)
    {
        $folderPath = '';
        if ($targetFolder) {
            $folderPath = $name;
            if ($targetFolder->getPath()) {
                $folderPath = rtrim($targetFolder->getPath(), '/') . '/' . $folderPath;
            }
        }

        // prepare folder's name and id
        $folderClass = $this->volumeManager->getFolderClass();
        $folder = new $folderClass();
        /* @var $folder FolderInterface */
        $folder
            ->setVolume($this)
            ->setId(Uuid::generate())
            ->setName($name)
            ->setParentId($targetFolder ? $targetFolder->getId() : null)
            ->setPath($folderPath)
            ->setAttributes($attributes)
            ->setCreatedAt(new \DateTime())
            ->setCreateUser($folder->getCreateUser())
            ->setModifiedAt($folder->getCreatedAt())
            ->setModifyUser($folder->getCreateUser());

        $this->volumeManager->validateCreateFolder($folder);

        $event = new FolderEvent($folder);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_CREATE_FOLDER, $event)->isPropagationStopped()) {
            throw new IOException("Create folder {$folder->getName()} cancelled.");
        }

        $this->volumeManager->updateFolder($folder);

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(VolumeEvents::CREATE_FOLDER, $event);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function renameFolder(FolderInterface $folder, $name, $user)
    {
        $oldPath = $folder->getPath();
        $parentFolder = $this->findFolder($folder->getParentId());
        $newPath = $name;
        if ($parentFolder->getPath()) {
            $newPath = rtrim($parentFolder->getPath(), '/') . '/' . $newPath;
        }

        $folder
            ->setName($name)
            ->setPath($newPath)
            ->setModifiedAt(new \DateTime())
            ->setModifyUser($user);

        $this->volumeManager->validateRenameFolder($folder);

        $event = new RenameFolderEvent($folder, $oldPath);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_RENAME_FOLDER, $event)->isPropagationStopped()) {
            throw new IOException("Rename folder {$folder->getName()} cancelled.");
        }

        $this->volumeManager->renameFolder($folder, $oldPath);

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(VolumeEvents::RENAME_FOLDER, $event);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function moveFolder(FolderInterface $folder, FolderInterface $targetFolder, $user)
    {
        if ($folder->getParentId() === $targetFolder->getId()) {
            return null;
        }

        if ($folder->getId() === $targetFolder->getId()) {
            return null;
        }

        $oldPath = $folder->getPath();
        $newPath = $folder->getName();
        if ($targetFolder->getPath()) {
            $newPath = rtrim($targetFolder->getPath(), '/') . '/' . $newPath;
        }

        $folder
            ->setParentId($targetFolder->getId())
            ->setPath($newPath)
            ->setModifiedAt(new \DateTime())
            ->setModifyUser($user);

        $this->volumeManager->validateMoveFolder($folder);

        $event = new MoveFolderEvent($folder, $targetFolder);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_MOVE_FOLDER, $event)->isPropagationStopped()) {
            throw new IOException("Move folder {$folder->getName()} cancelled.");
        }

        $this->volumeManager->moveFolder($folder, $oldPath);

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(VolumeEvents::MOVE_FOLDER, $event);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function copyFolder(FolderInterface $folder, FolderInterface $targetFolder, $user)
    {
        $this->volumeManager->validateCopyFolder($folder, $targetFolder);

        $event = new CopyFolderEvent($folder, $targetFolder);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_COPY_FOLDER, $event)->isPropagationStopped()) {
            throw new IOException("Move folder {$folder->getName()} cancelled.");
        }

        $copiedFolder = $this->createFolder($targetFolder, $folder->getName() . '_copy_' . uniqid(), $folder->getAttributes(), $user);

        foreach ($this->findFoldersByParentFolder($folder) as $subFolder) {
            $this->copyFolder($subFolder, $copiedFolder, $user);
        }

        foreach ($this->findFilesByFolder($folder) as $file) {
            $fileSource = new FilesystemFileSource($file->getPhysicalPath(), $file->getMimeType(), $file->getSize());
            $this->createFile($copiedFolder, $fileSource, $file->getAttributes(), $user);
        }

        $event = new FolderEvent($copiedFolder);
        $this->eventDispatcher->dispatch(VolumeEvents::COPY_FOLDER, $event);

        return $copiedFolder;
    }

    /**
     * {@inheritdoc}
     */
    public function checkDeleteFolder(FolderInterface $folder)
    {
        foreach ($this->findFoldersByParentFolder($folder) as $subFolder) {
            $this->checkDeleteFolder($subFolder);
        }

        foreach ($this->findFilesByFolder($folder) as $file) {
            $this->checkDeleteFile($file);
        }

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(VolumeEvents::CHECK_DELETE_FOLDER, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFolder(FolderInterface $folder, $user)
    {
        $this->checkDeleteFolder($folder);

        return $this->doDeleteFolder($folder, $user);
    }

    /**
     * @param FolderInterface $folder
     * @param string          $user
     *
     * @return FolderInterface
     */
    private function doDeleteFolder(FolderInterface $folder, $user)
    {
        foreach ($this->findFoldersByParentFolder($folder) as $subFolder) {
            $this->deleteFolder($subFolder, $user);
        }

        foreach ($this->findFilesByFolder($folder) as $file) {
            $this->deleteFile($file, $user);
        }

        $event = new FolderEvent($folder);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_DELETE_FOLDER, $event)->isPropagationStopped()) {
            throw new IOException("Delete folder {$folder->getName()} cancelled.");
        }

        $this->volumeManager->deleteFolder($folder, $user);

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(VolumeEvents::DELETE_FOLDER, $event);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function setFolderAttributes(FolderInterface $folder, array $attributes, $user)
    {
        $folder
            ->setAttributes($attributes)
            ->setModifiedAt(new \DateTime())
            ->setModifyUser($user);

        $event = new FolderEvent($folder);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_SET_FOLDER_ATTRIBUTES, $event)->isPropagationStopped()) {
            throw new IOException("Move folder {$folder->getName()} cancelled.");
        }

        $this->volumeManager->updateFolder($folder);

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(VolumeEvents::SET_FOLDER_ATTRIBUTES, $event);

        return $folder;
    }
}
