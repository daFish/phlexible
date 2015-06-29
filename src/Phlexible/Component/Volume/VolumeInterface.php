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

use Phlexible\Component\Volume\FileSource\FileSourceInterface;
use Phlexible\Component\Volume\Model\FileInterface;
use Phlexible\Component\Volume\Model\FolderInterface;
use Phlexible\Component\Volume\Model\VolumeManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Expression\Expression;

/**
 * Volume interface
 * Represents a complete set of classes used to get a virtual set of folders and files
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface VolumeInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getRootDir();

    /**
     * @return int
     */
    public function getQuota();

    /**
     * @return VolumeManagerInterface
     */
    public function getVolumeManager();

    /**
     * @param VolumeManagerInterface $volumeManager
     */
    public function setVolumeManager(VolumeManagerInterface $volumeManager);

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher();

    /**
     * @return FolderInterface
     */
    public function findRootFolder();

    /**
     * @param string $id
     *
     * @return FolderInterface
     */
    public function findFolder($id);

    /**
     * @param int $fileId
     *
     * @return FolderInterface
     */
    public function findFolderByFileId($fileId);

    /**
     * @param string $path
     *
     * @return FolderInterface
     */
    public function findFolderByPath($path);

    /**
     * @param FolderInterface $parentFolder
     *
     * @return FolderInterface[]
     */
    public function findFoldersByParentFolder(FolderInterface $parentFolder);

    /**
     * @param FolderInterface $parentFolder
     *
     * @return int
     */
    public function countFoldersByParentFolder(FolderInterface $parentFolder);

    /**
     * @param int $id
     * @param int $version
     *
     * @return FileInterface
     */
    public function findFile($id, $version = 1);

    /**
     * @param array      $criteria
     * @param array|null $order
     * @param int|null   $limit
     * @param int|null   $start
     *
     * @return FileInterface[]
     */
    public function findFiles(array $criteria, $order = null, $limit = null, $start = null);

    /**
     * @param array $criteria
     *
     * @return int
     */
    public function countFiles(array $criteria);

    /**
     * @param FolderInterface $folder
     * @param string          $name
     *
     * @return FileInterface
     */
    public function findFileByFolderAndName(FolderInterface $folder, $name);

    /**
     * @param int $id
     *
     * @return FileInterface[]
     */
    public function findFileVersions($id);

    /**
     * @param FolderInterface $folder
     * @param string          $order
     * @param int             $limit
     * @param int             $start
     * @param bool            $includeHidden
     *
     * @return FileInterface[]
     */
    public function findFilesByFolder(
        FolderInterface $folder,
        $order = null,
        $limit = null,
        $start = null,
        $includeHidden = false);

    /**
     * @param FolderInterface $folder
     * @param bool            $includeHidden
     *
     * @return int
     */
    public function countFilesByFolder(FolderInterface $folder, $includeHidden = false);

    /**
     * @param int $limit
     *
     * @return FileInterface[]
     */
    public function findLatestFiles($limit = 20);

    /**
     * @param Expression $expression
     *
     * @return FileInterface[]
     */
    public function findFilesByExpression(Expression $expression);

    /**
     * @param Expression $expression
     *
     * @return int
     */
    public function countFilesByExpression(Expression $expression);

    /**
     * @param FileInterface $file
     *
     * @return string
     */
    public function getPhysicalPath(FileInterface $file);

    /**
     * @param FolderInterface $targetFolder
     * @param string          $name
     * @param array           $attributes
     * @param string          $user
     *
     * @return FolderInterface
     */
    public function createFolder(FolderInterface $targetFolder, $name, array $attributes, $user);

    /**
     * @param FolderInterface $folder
     * @param string          $name
     * @param string          $user
     *
     * @return FolderInterface
     */
    public function renameFolder(FolderInterface $folder, $name, $user);

    /**
     * @param FolderInterface $folder
     * @param FolderInterface $targetFolder
     * @param string          $user
     *
     * @return FolderInterface
     */
    public function moveFolder(FolderInterface $folder, FolderInterface $targetFolder, $user);

    /**
     * @param FolderInterface $folder
     * @param FolderInterface $targetFolder
     * @param string          $user
     *
     * @return FolderInterface
     */
    public function copyFolder(FolderInterface $folder, FolderInterface $targetFolder, $user);

    /**
     * @param FolderInterface $folder
     */
    public function checkDeleteFolder(FolderInterface $folder);

    /**
     * @param FolderInterface $folder
     * @param string          $user
     *
     * @return FolderInterface
     */
    public function deleteFolder(FolderInterface $folder, $user);

    /**
     * @param FolderInterface $folder
     * @param array           $attributes
     * @param string          $user
     *
     * @return FolderInterface
     */
    public function setFolderAttributes(FolderInterface $folder, array $attributes, $user);

    /**
     * @param FolderInterface     $targetFolder
     * @param FileSourceInterface $fileSource
     * @param array               $attributes
     * @param string              $user
     *
     * @return FileInterface
     */
    public function createFile(
        FolderInterface $targetFolder,
        FileSourceInterface $fileSource,
        array $attributes,
        $user);

    /**
     * @param FileInterface       $file
     * @param FileSourceInterface $fileSource
     * @param array               $attributes
     * @param string              $user
     *
     * @return FileInterface
     */
    public function replaceFile(
        FileInterface $file,
        FileSourceInterface $fileSource,
        array $attributes,
        $user);

    /**
     * @param FileInterface $file
     * @param string        $name
     * @param string        $user
     *
     * @return FileInterface
     */
    public function renameFile(FileInterface $file, $name, $user);

    /**
     * @param FileInterface   $file
     * @param FolderInterface $targetFolder
     * @param string          $user
     *
     * @return FileInterface
     */
    public function moveFile(FileInterface $file, FolderInterface $targetFolder, $user);

    /**
     * @param FileInterface   $file
     * @param FolderInterface $targetFolder
     * @param string          $user
     *
     * @return FileInterface
     */
    public function copyFile(FileInterface $file, FolderInterface $targetFolder, $user);

    /**
     * @param FileInterface $file
     */
    public function checkDeleteFile(FileInterface $file);

    /**
     * @param FileInterface $file
     * @param string        $user
     *
     * @return FileInterface
     */
    public function deleteFile(FileInterface $file, $user);

    /**
     * @param FileInterface $file
     * @param string        $user
     *
     * @return FileInterface
     */
    public function showFile(FileInterface $file, $user);

    /**
     * @param FileInterface $file
     * @param string        $user
     *
     * @return FileInterface
     */
    public function hideFile(FileInterface $file, $user);

    /**
     * @param FileInterface $file
     * @param array         $attributes
     * @param string        $user
     *
     * @return FileInterface
     */
    public function setFileAttributes(FileInterface $file, array $attributes, $user);
}
