<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Volume\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Component\Volume\Exception\AlreadyExistsException;
use Phlexible\Component\Volume\Exception\IOException;
use Phlexible\Component\Volume\Exception\NotFoundException;
use Phlexible\Component\Volume\Exception\NotWritableException;
use Phlexible\Component\Volume\FileSource\FileSourceInterface;
use Phlexible\Component\Volume\FileSource\PathSourceInterface;
use Phlexible\Component\Volume\FileSource\StreamSourceInterface;
use Phlexible\Component\Volume\HashCalculator\HashCalculatorInterface;
use Phlexible\Component\Volume\Model\FileInterface;
use Phlexible\Component\Volume\Model\FolderInterface;
use Phlexible\Component\Volume\Model\VolumeManagerInterface;
use Phlexible\Component\Volume\VolumeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\Expression\Expression;

/**
 * Doctrine volume manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class VolumeManager implements VolumeManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $folderClass;

    /**
     * @var string
     */
    private $fileClass;

    /**
     * @var array
     */
    private $volumeConfigs;

    /**
     * @param EntityManager            $entityManager
     * @param HashCalculatorInterface  $hashCalculator
     * @param EventDispatcherInterface $eventDispatcher
     * @param string                   $folderClass
     * @param string                   $fileClass
     * @param array                    $volumeConfigs
     */
    public function __construct(
        EntityManager $entityManager,
        HashCalculatorInterface $hashCalculator,
        EventDispatcherInterface $eventDispatcher,
        $folderClass,
        $fileClass,
        array $volumeConfigs = array()
    )
    {
        $this->entityManager = $entityManager;
        $this->hashCalculator = $hashCalculator;
        $this->eventDispatcher = $eventDispatcher;
        $this->folderClass = $folderClass;
        $this->fileClass = $fileClass;
        $this->volumeConfigs = $volumeConfigs;

        $this->connection = $entityManager->getConnection();
    }

    /**
     * @return EntityRepository
     */
    private function getFileRepository()
    {
        return $this->entityManager->getRepository($this->fileClass);
    }

    /**
     * @return EntityRepository
     */
    private function getFolderRepository()
    {
        return $this->entityManager->getRepository($this->folderClass);
    }

    public function hasFeature($name)
    {
        return true;
    }

    /**
     * @var VolumeInterface[]
     */
    private $volumes = array();

    /**
     * @param string $name
     *
     * @return VolumeInterface
     */
    private function getOrCreateVolume($name)
    {
        if (!isset($this->volumes[$name])) {
            $volumeConfig = $this->volumeConfigs[$name];
            $class = $volumeConfig['class'];

            $volume = new $class(
                $volumeConfig['id'],
                $name,
                $volumeConfig['root_dir'],
                $volumeConfig['quota'],
                $this->eventDispatcher
            );
            $volume->setVolumeManager($this);
            $this->volumes[$name] = $volume;
        }

        return $this->volumes[$name];
    }

    /**
     * @param string $name
     *
     * @return VolumeInterface
     */
    public function get($name)
    {
        return $this->getOrCreateVolume($name);
    }

    /**
     * @param string $id
     *
     * @return VolumeInterface
     */
    public function getById($id)
    {
        foreach ($this->volumeConfigs as $name => $volumeConfig) {
            if ($volumeConfig['id'] === $id) {
                return $this->getOrCreateVolume($name);
            }
        }

        return null;
    }

    /**
     * Return volume by file ID
     *
     * @param string $fileId
     *
     * @return VolumeInterface
     * @throws NotFoundException
     */
    public function getByFileId($fileId)
    {
        $file = $this->findFileBy(array('id' => $fileId));

        if (!$file) {
            throw new NotFoundException("Volume for file ID $fileId not found.");
        }

        return $this->getById($file->getVolumeId());
    }

    /**
     * Return volume by folder ID
     *
     * @param string $folderId
     *
     * @return VolumeInterface
     * @throws NotFoundException
     */
    public function getByFolderId($folderId)
    {
        $file = $this->findFolderBy(array('id' => $folderId));

        if (!$file) {
            throw new NotFoundException("Volume for folder ID $folderId not found.");
        }

        return $this->getById($file->getVolumeId());
    }

    /**
     * Return all volumes
     *
     * @return VolumeInterface[]
     */
    public function all()
    {
        $volumes = array();
        foreach ($this->volumeConfigs as $name => $volumeConfig) {
            $volumes[] = $this->get($name);
        }

        return $volumes;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileClass()
    {
        return $this->fileClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getFolderClass()
    {
        return $this->folderClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getHashCalculator()
    {
        return $this->hashCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function findFolder($id)
    {
        return $this->getFolderRepository()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findFoldersBy(array $criteria = array(), $orderBy = array(), $limit = null, $offset = null)
    {
        return $this->getFolderRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function countFoldersBy(array $criteria = array())
    {
        return $this->getFolderRepository()->countBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findFolderBy(array $criteria = array(), $orderBy = array())
    {
        return $this->getFolderRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findFoldersByExpression(Expression $expression, $orderBy = array(), $limit = null, $offset = null)
    {
        return $this->getFolderRepository()->findByExpression($expression, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function countFoldersByExpression(Expression $expression)
    {
        return $this->getFolderRepository()->countByExpression($expression);
    }

    /**
     * {@inheritdoc}
     */
    public function findFilesBy(array $criteria = array(), $orderBy = array(), $limit = null, $offset = null)
    {
        return $this->getFileRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function countFilesBy(array $criteria = array())
    {
        return $this->getFileRepository()->countBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findFileBy(array $criteria = array(), $orderBy = array())
    {
        return $this->getFileRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findFilesByExpression(Expression $expression, $orderBy = array(), $limit = null, $start = null)
    {
        return $this->getFileRepository()->findByExpression($expression, $orderBy, $limit, $start);
    }

    /**
     * {@inheritdoc}
     */
    public function countFilesByExpression(Expression $expression)
    {
        return $this->getFileRepository()->countByExpression($expression);
    }

    /**
     * {@inheritdoc}
     */
    public function updateFile(FileInterface $file)
    {
        $this->entityManager->persist($file);
        $this->entityManager->flush($file);
    }

    /**
     * {@inheritdoc}
     */
    public function createFile(FileInterface $file, FileSourceInterface $fileSource)
    {
        $path = $file->getVolume()->getPhysicalPath($file);
        //$path = $file->getPhysicalPath();
        $filesystem = new Filesystem();

        if (!file_exists($path)) {
            if ($fileSource instanceof StreamSourceInterface) {
                $stream = $fileSource->getStream();
                rewind($stream);
                $fd = fopen($path, 'w+');
                stream_copy_to_stream($stream, $fd);
                fclose($fd);
                fclose($stream);
                $file->setMimeType($fileSource->getMimeType());
            } elseif ($fileSource instanceof PathSourceInterface) {
                $filesystem->copy($fileSource->getPath(), $path);
                $file->setMimeType($fileSource->getMimeType());
            } else {
                $filesystem->touch($path);
                $file->setMimeType($fileSource->getMimeType());
            }
        }

        try {
            $this->updateFile($file);
        } catch (\Exception $e) {
            $filesystem->remove($path);

            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function replaceFile(FileInterface $file, FileSourceInterface $fileSource)
    {
        $filesystem = new Filesystem();
        $path = $file->getPhysicalPath();

        if (!file_exists($path)) {
            if ($fileSource instanceof StreamSourceInterface) {
                $stream = $fileSource->getStream();
                rewind($stream);
                $fd = fopen($path, 'w+');
                stream_copy_to_stream($stream, $fd);
                fclose($fd);
                fclose($stream);
                $file->setMimeType($fileSource->getMimeType());
            } elseif ($fileSource instanceof PathSourceInterface) {
                $filesystem->copy($fileSource->getPath(), $path);
                $file->setMimeType($fileSource->getMimeType());
            } else {
                $filesystem->touch($path);
                $file->setMimeType($fileSource->getMimeType());
            }
        }

        try {
            $this->updateFile($file);
        } catch (\Exception $e) {
            $filesystem->remove($path);

            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile(FileInterface $file)
    {
        $this->entityManager->remove($file);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function updateFolder(FolderInterface $folder)
    {
        $this->entityManager->persist($folder);
        $this->entityManager->flush($folder);
    }

    /**
     * {@inheritdoc}
     */
    public function renameFolder(FolderInterface $folder, $oldPath)
    {
        $this->updateFolder($folder);

        if ($folder->isRoot()) {
            return;
        }

        $oldPath = rtrim($oldPath, '/') . '/';
        $replacePath = rtrim($folder->getPath(), '/') . '/';

        $qb = $this->getFolderRepository()->createQueryBuilder('fo');
        $qb
            ->where($qb->expr()->eq('fo.volumeId', $qb->expr()->literal($folder->getVolume()->getId())))
            ->andWhere(
                $qb->expr()->eq(
                    $qb->expr()->substring('fo.path', 1, mb_strlen($oldPath)),
                    $qb->expr()->literal($oldPath)
                )
            );

        foreach ($qb->getQuery()->getResult() as $subFolder) {
            /* @var FolderInterface $subFolder */
            $subFolder->setPath(str_replace($oldPath, $replacePath, $subFolder->getPath()));
            $this->updateFolder($subFolder);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function moveFolder(FolderInterface $folder, $oldPath)
    {
        $this->updateFolder($folder);

        $oldPath = rtrim($oldPath, '/') . '/';
        $replacePath = rtrim($folder->getPath(), '/') . '/';

        $qb = $this->getFolderRepository()->createQueryBuilder('fo');
        $qb
            ->where($qb->expr()->eq('fo.volumeId', $qb->expr()->literal($folder->getVolume()->getId())))
            ->andWhere(
                $qb->expr()->eq(
                    $qb->expr()->substring('fo.path', 1, mb_strlen($oldPath)),
                    $qb->expr()->literal($oldPath)
                )
            );

        foreach ($qb->getQuery()->getResult() as $subFolder) {
            /* @var FolderInterface $subFolder */
            $subFolder->setPath(str_replace($oldPath, $replacePath, $subFolder->getPath()));
            $this->updateFolder($subFolder);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFolder(FolderInterface $folder)
    {
        $this->entityManager->remove($folder);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function validateCreateFolder(FolderInterface $folder)
    {
        if ($this->findFolderBy(array('parentId' => $folder->getParentId(), 'name' => $folder->getName()))) {
            throw new AlreadyExistsException("Folder {$folder->getName()} already exists.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateRenameFolder(FolderInterface $folder)
    {
        if ($this->findFolderBy(array('parentId' => $folder->getParentId(), 'name' => $folder->getName()))) {
            throw new AlreadyExistsException("Folder {$folder->getName()} already exists at target folder.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateMoveFolder(FolderInterface $folder)
    {
        if ($this->findFolderBy(array('parentId' => $folder->getParentId(), 'name' => $folder->getName()))) {
            throw new AlreadyExistsException("Folder {$folder->getName()} already exists at target folder.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateCopyFolder(FolderInterface $folder, FolderInterface $targetFolder)
    {
        if ($this->findFolderBy(array('parentId' => $targetFolder->getId(), 'name' => $folder->getName()))) {
            throw new AlreadyExistsException("Folder {$folder->getName()} already exists at target folder.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateCreateFile(FileInterface $file, FolderInterface $folder)
    {
        if ($this->findFileBy(array('folderId' => $folder->getId(), 'name' => $file->getName()))) {
            throw new AlreadyExistsException("File {$file->getName()} already exists at target folder.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateRenameFile(FileInterface $file, FolderInterface $folder)
    {
        $this->validateMoveFile($file, $folder);
    }

    /**
     * {@inheritdoc}
     */
    public function validateMoveFile(FileInterface $file, FolderInterface $folder)
    {
        if ($this->findFileBy(array('folderId' => $folder->getId(), 'name' => $file->getName()))) {
            throw new AlreadyExistsException("File {$file->getName()} already exists at target folder.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateCopyFile(FileInterface $file, FolderInterface $folder)
    {
        $this->validateMoveFile($file, $folder);
    }

    /**
     * @param FolderInterface $folder
     * @param string          $userId
     *
     * @throws NotWritableException
     * @throws IOException
     */
    public function deletePhysicalFolder(FolderInterface $folder, $userId)
    {
        $filesystem = new Filesystem();

        $physicalPath = $folder->getVolume()->getRootDir() . $folder->getPath();

        if ($filesystem->exists($physicalPath) && !is_dir($physicalPath)) {
            throw new IOException('Delete folder failed, not a folder.');
        }

        if ($filesystem->exists($physicalPath) && !is_writable($physicalPath)) {
            throw new NotWritableException('Delete folder failed.');
        }

        foreach ($this->findFoldersBy(array('parentId' => $folder->getId())) as $subFolder) {
            $this->deletePhysicalFolder($subFolder, $userId);
        }

        foreach ($this->findFilesBy(array('folderId' => $folder->getId())) as $file) {
            $this->deleteFile($file);
        }

        // TODO: check for remaining instances pointing to blob
        //$filesystem->remove($physicalPath);

        $folder->setId(null);
    }
}
