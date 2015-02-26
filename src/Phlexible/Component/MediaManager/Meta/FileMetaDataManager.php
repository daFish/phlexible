<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaManager\Meta;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MetaSet\Doctrine\MetaDataManager;
use Phlexible\Component\MetaSet\Model\MetaData;
use Phlexible\Component\MetaSet\Model\MetaDataInterface;
use Phlexible\Component\MetaSet\Model\MetaSet;

/**
 * File meta data manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileMetaDataManager extends MetaDataManager
{
    /**
     * @param MetaSet               $metaSet
     * @param ExtendedFileInterface $file
     *
     * @return MetaData|MetaDataInterface
     */
    public function createFileMetaData(MetaSet $metaSet, ExtendedFileInterface $file)
    {
        $metaData = $this->createMetaData($metaSet);
        $metaData
            ->setIdentifiers($this->getIdentifiersFromFile($file));

        return $metaData;
    }

    /**
     * @param MetaSet               $metaSet
     * @param ExtendedFileInterface $file
     *
     * @return null|MetaDataInterface
     */
    public function findByMetaSetAndFile(MetaSet $metaSet, ExtendedFileInterface $file)
    {
        return $this->findByMetaSetAndIdentifiers($metaSet, $this->getIdentifiersFromFile($file));
    }

    /**
     * @param string $value
     *
     * @return MetaDataInterface[]
     */
    public function findByValue($value)
    {
        $connection = $this->getConnection();

        $qb = $connection->createQueryBuilder();
        $qb
            ->select('m.*')
            ->from($this->getTableName(), 'm')
            ->where($qb->expr()->like('m.value', $qb->expr()->literal("%$value%")));

        $rows = $connection->fetchAll($qb->getSQL());

        $metaDatas = [];

        foreach ($rows as $row) {
            $identifiers = [
                'file_id' => $row['file_id'],
                'file_version' => $row['file_version'],
            ];

            $id = '';
            foreach ($identifiers as $value) {
                $id .= $value . '_';
            }
            $id .= $row['set_id'];

            if (!isset($metaDatas[$id])) {
                $metaData = new MetaData();
                $metaData
                    ->setIdentifiers($identifiers)
                    ->setMetaSet(null);
                $metaDatas[$id] = $metaData;
            } else {
                $metaData = $metaDatas[$id];
            }

            $metaData->set($row['field_id'], $row['value'], $row['language']);
        }

        return $metaDatas;
    }

    /**
     * @param ExtendedFileInterface $file
     *
     * @return array
     */
    private function getIdentifiersFromFile(ExtendedFileInterface $file)
    {
        return [
            'file_id'      => $file->getId(),
            'file_version' => $file->getVersion(),
        ];
    }
}
