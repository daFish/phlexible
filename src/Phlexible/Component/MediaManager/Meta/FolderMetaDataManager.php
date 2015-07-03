<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaManager\Meta;

use Phlexible\Component\MediaManager\Volume\ExtendedFolderInterface;
use Phlexible\Component\MetaSet\Doctrine\MetaDataManager;
use Phlexible\Component\MetaSet\Model\MetaData;
use Phlexible\Component\MetaSet\Model\MetaDataInterface;
use Phlexible\Component\MetaSet\Model\MetaSet;

/**
 * Folder meta data manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FolderMetaDataManager extends MetaDataManager
{
    /**
     * @param MetaSet                 $metaSet
     * @param ExtendedFolderInterface $folder
     *
     * @return MetaData|MetaDataInterface
     */
    public function createFolderMetaData(MetaSet $metaSet, ExtendedFolderInterface $folder)
    {
        $metaData = $this->createMetaData($metaSet);
        $metaData
            ->setIdentifiers($this->getIdentifiersFromFolder($folder));

        return $metaData;
    }

    /**
     * @param MetaSet                 $metaSet
     * @param ExtendedFolderInterface $folder
     *
     * @return null|MetaDataInterface
     */
    public function findByMetaSetAndFolder(MetaSet $metaSet, ExtendedFolderInterface $folder)
    {
        return $this->findByMetaSetAndIdentifiers($metaSet, $this->getIdentifiersFromFolder($folder));
    }

    /**
     * @param ExtendedFolderInterface $folder
     *
     * @return array
     */
    private function getIdentifiersFromFolder(ExtendedFolderInterface $folder)
    {
        return array(
            'folder_id' => $folder->getId(),
        );
    }
}
