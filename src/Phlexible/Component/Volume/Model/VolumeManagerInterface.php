<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Volume\Model;

use Phlexible\Component\Volume\Exception\AlreadyExistsException;
use Phlexible\Component\Volume\FileSource\FileSourceInterface;
use Phlexible\Component\Volume\HashCalculator\HashCalculatorInterface;
use Webmozart\Expression\Expression;

/**
 * Volume Manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface VolumeManagerInterface
{
    const FEATURE_VERSIONS = 'versions';

    /**
     * @param string $feature
     *
     * @return bool
     */
    public function hasFeature($feature);

    /**
     * @return string
     */
    public function getFileClass();

    /**
     * @return string
     */
    public function getFolderClass();

    /**
     * @return HashCalculatorInterface
     */
    public function getHashCalculator();

    /**
     * @param string $id
     *
     * @return FolderInterface
     */
    public function findFolder($id);

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return FolderInterface[]
     */
    public function findFoldersBy(array $criteria = array(), $orderBy = null, $limit = null, $offset = null);

    /**
     * @param array $criteria
     *
     * @return int
     */
    public function countFoldersBy(array $criteria = array());

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     *
     * @return FolderInterface|null
     */
    public function findFolderBy(array $criteria = array(), $orderBy = null);

    /**
     * @param Expression $expression
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return FolderInterface[]
     */
    public function findFoldersByExpression(Expression $expression, $orderBy = null, $limit = null, $offset = null);

    /**
     * @param Expression $expression
     *
     * @return int
     */
    public function countFoldersByExpression(Expression $expression);

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return FileInterface[]
     */
    public function findFilesBy(array $criteria = array(), $orderBy = null, $limit = null, $offset = null);

    /**
     * @param array $criteria
     *
     * @return int
     */
    public function countFilesBy(array $criteria = array());

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     *
     * @return FileInterface|null
     */
    public function findFileBy(array $criteria = array(), $orderBy = null);

    /**
     * @param Expression $expression
     * @param array|null $order
     * @param int|null   $limit
     * @param int|null   $start
     *
     * @return FileInterface[]
     */
    public function findFilesByExpression(Expression $expression, $order = null, $limit = null, $start = null);

    /**
     * @param Expression $expression
     *
     * @return int
     */
    public function countFilesByExpression(Expression $expression);

    /**
     * @param FolderInterface $folder
     */
    public function updateFolder(FolderInterface $folder);

    /**
     * @param FolderInterface $folder
     * @param string          $oldPath
     */
    public function renameFolder(FolderInterface $folder, $oldPath);

    /**
     * @param FolderInterface $folder
     * @param string          $oldPath
     */
    public function moveFolder(FolderInterface $folder, $oldPath);

    /**
     * @param FolderInterface $folder
     */
    public function deleteFolder(FolderInterface $folder);

    /**
     * @param FileInterface $file
     */
    public function updateFile(FileInterface $file);

    /**
     * @param FileInterface       $file
     * @param FileSourceInterface $fileSource
     */
    public function createFile(FileInterface $file, FileSourceInterface $fileSource);

    /**
     * @param FileInterface       $file
     * @param FileSourceInterface $fileSource
     */
    public function replaceFile(FileInterface $file, FileSourceInterface $fileSource);

    /**
     * @param FileInterface $file
     *
     * @return FileInterface
     */
    public function deleteFile(FileInterface $file);

    /**
     * @param FolderInterface $folder
     *
     * @throws AlreadyExistsException
     */
    public function validateCreateFolder(FolderInterface $folder);

    /**
     * @param FolderInterface $folder
     *
     * @throws AlreadyExistsException
     */
    public function validateRenameFolder(FolderInterface $folder);

    /**
     * @param FolderInterface $folder
     *
     * @throws AlreadyExistsException
     */
    public function validateMoveFolder(FolderInterface $folder);

    /**
     * @param FolderInterface $folder
     * @param FolderInterface $targetFolder
     *
     * @throws AlreadyExistsException
     */
    public function validateCopyFolder(FolderInterface $folder, FolderInterface $targetFolder);

    /**
     * @param FileInterface   $file
     * @param FolderInterface $folder
     *
     * @throws AlreadyExistsException
     */
    public function validateCreateFile(FileInterface $file, FolderInterface $folder);

    /**
     * @param FileInterface   $file
     * @param FolderInterface $folder
     *
     * @throws AlreadyExistsException
     */
    public function validateRenameFile(FileInterface $file, FolderInterface $folder);

    /**
     * @param FileInterface   $file
     * @param FolderInterface $folder
     *
     * @throws AlreadyExistsException
     */
    public function validateMoveFile(FileInterface $file, FolderInterface $folder);

    /**
     * @param FileInterface   $file
     * @param FolderInterface $folder
     *
     * @throws AlreadyExistsException
     */
    public function validateCopyFile(FileInterface $file, FolderInterface $folder);
}
