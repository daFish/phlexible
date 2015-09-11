<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MetaSet\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Component\MetaSet\Domain\MetaData;
use Phlexible\Component\MetaSet\Domain\MetaSet;
use Phlexible\Component\MetaSet\Model\MetaDataInterface;
use Phlexible\Component\MetaSet\Model\MetaDataManagerInterface;

/**
 * Meta data manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaDataManager implements MetaDataManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @param EntityManager $entityManager
     * @param string        $tableName
     */
    public function __construct(EntityManager $entityManager, $tableName)
    {
        $this->entityManager = $entityManager;
        $this->tableName = $tableName;
    }

    /**
     * @return Connection
     */
    protected function getConnection()
    {
        return $this->entityManager->getConnection();
    }

    /**
     * @return string
     */
    protected function getTableName()
    {
        return $this->tableName;
    }

    /**
     * {@inheritdoc}
     */
    public function findByMetaSetAndIdentifiers(MetaSet $metaSet, array $identifiers)
    {
        $metaDatas = $this->doFindByMetaSetAndIdentifiers($metaSet, $identifiers);

        if (!count($metaDatas)) {
            return null;
        }

        return current($metaDatas);
    }

    /**
     * {@inheritdoc}
     */
    public function findByMetaSet(MetaSet $metaSet)
    {
        return $this->doFindByMetaSetAndIdentifiers($metaSet);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->doFindByMetaSetAndIdentifiers();
    }

    /**
     * {@inheritdoc}
     */
    public function createMetaData(MetaSet $metaSet)
    {
        $metaData = new MetaData();
        $metaData->setMetaSet($metaSet);

        return $metaData;
    }

    /**
     * @param MetaDataInterface $metaData
     */
    public function updateMetaData(MetaDataInterface $metaData)
    {
        $baseData = array(
            'set_id' => $metaData->getMetaSet()->getId(),
        );
        foreach ($metaData->getIdentifiers() as $field => $value) {
            $baseData[$field] = $value;
        }

        $connection = $this->getConnection();

        foreach ($metaData->getLanguages() as $language) {
            foreach ($metaData->getMetaSet()->getFields() as $field) {
                // TODO: remove?
                if (!$metaData->get($field->getName(), $language)) {
                    continue;
                }

                $value = $metaData->get($field->getName(), $language);

                // TODO: event, datasource-bundle
                /*
                if ('suggest' === $field->getType()) {
                    $dataSourceId = $field->getOptions();
                    $dataSource = $this->dataSourceManager->find($dataSourceId);
                    foreach (explode(',', $value) as $singleValue) {
                        $dataSource->addValueForLanguage($language, $singleValue, true);
                    }
                    $this->dataSourceManager->updateDataSource($dataSource);
                }
                */

                $insertData = $baseData;

                $insertData['id'] = Uuid::generate();
                $insertData['field_id'] = $field->getId();
                $insertData['value'] = $value;
                $insertData['language'] = $language;

                $connection->insert($this->getTableName(), $insertData);
            }
        }

        // TODO: job!
        //$this->_queueDataSourceCleanup();
    }

    /**
     * @param MetaSet $metaSet
     * @param array   $identifiers
     *
     * @return MetaData[]
     */
    private function doFindByMetaSetAndIdentifiers(MetaSet $metaSet = null, array $identifiers = array())
    {
        $connection = $this->getConnection();

        $qb = $connection->createQueryBuilder();
        $qb
            ->select('m.*')
            ->from($this->getTableName(), 'm');

        if ($metaSet) {
            $qb->where($qb->expr()->eq('m.set_id', $qb->expr()->literal($metaSet->getId())));
        }

        foreach ($identifiers as $field => $value) {
            $qb->andWhere($qb->expr()->eq("m.$field", $qb->expr()->literal($value)));
        }

        $rows = $qb->execute()->fetchAll();

        $metaDatas = array();

        foreach ($rows as $row) {
            $id = '';
            foreach ($identifiers as $value) {
                $id .= $value . '_';
            }
            $id .= $row['set_id'];

            if (!isset($metaDatas[$id])) {
                $metaData = new MetaData();
                $metaData
                    ->setIdentifiers($identifiers)
                    ->setMetaSet($metaSet);
                $metaDatas[$id] = $metaData;
            } else {
                $metaData = $metaDatas[$id];
            }

            $field = $metaSet->getFieldById($row['field_id']);
            if ($field) {
                $metaData->set($field->getName(), $row['value'], $row['language']);
            }
        }

        return $metaDatas;
    }

}
