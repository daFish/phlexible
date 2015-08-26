<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaManager\Util;

use Phlexible\Bundle\DataSourceBundle\Entity\DataSourceValueBag;
use Phlexible\Component\MetaSet\Domain\MetaSetField;
use Phlexible\Component\MetaSet\Model\MetaDataInterface;
use Phlexible\Component\MetaSet\Model\MetaDataManagerInterface;
use Phlexible\Component\MetaSet\Model\MetaSetManagerInterface;

/**
 * Utility class for suggest fields.
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class SuggestFieldUtil
{
    /**
     * @var MetaSetManagerInterface
     */
    private $metaSetManager;

    /**
     * @var MetaDataManagerInterface
     */
    private $metaDataManager;

    /**
     * @var string
     */
    private $seperatorChar;

    /**
     * @param MetaSetManagerInterface  $metaSetManager
     * @param MetaDataManagerInterface $metaDataManager
     * @param string                   $seperatorChar
     */
    public function __construct(MetaSetManagerInterface $metaSetManager, MetaDataManagerInterface $metaDataManager, $seperatorChar)
    {
        $this->metaSetManager = $metaSetManager;
        $this->metaDataManager = $metaDataManager;
        $this->seperatorChar = $seperatorChar;
    }

    /**
     * Fetch all data source values used in any media file metaset.
     *
     * @param DataSourceValueBag $valueBag
     *
     * @return array
     */
    public function fetchUsedValues(DataSourceValueBag $valueBag)
    {
        $metaSets = $this->metaSetManager->findAll();

        $fields = array();
        foreach ($metaSets as $metaSet) {
            foreach ($metaSet->getFields() as $field) {
                if ($field->getOptions() === $valueBag->getDatasource()->getId()) {
                    $fields[] = $field;
                }
            }
        }

        $values = array();
        foreach ($fields as $field) {
            /* @var $field MetaSetField */
            foreach ($this->metaDataManager->findByMetaSet($field->getMetaSet()) as $metaData) {
                /* @var $metaData MetaDataInterface */
                $value = $metaData->get($field->getId(), $valueBag->getLanguage());

                $values[] = $value;
            }
        }

        $values = $this->splitSuggestValues($values);

        return $values;
    }

    /**
     * Split list of suggest values into pieces and remove duplicates.
     *
     * @param array $concatenated
     *
     * @return array
     */
    public function splitSuggestValues(array $concatenated)
    {
        $keys = array();
        foreach ($concatenated as $value) {
            $splitted = explode($this->seperatorChar, $value);
            foreach ($splitted as $key) {
                $key = trim($key);

                // skip empty values
                if (strlen($key)) {
                    $keys[] = $key;
                }
            }
        }

        $uniqueKeys = array_unique($keys);

        return $uniqueKeys;
    }
}
