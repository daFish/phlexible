<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Util;

use Phlexible\Bundle\DataSourceBundle\Entity\DataSourceValueBag;

/**
 * Utility class for suggest fields.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SuggestFieldUtil
{
    /**
     * @var string
     */
    private $seperatorChar;

    /**
     * @param string $seperatorChar
     */
    public function __construct($seperatorChar)
    {
        $this->seperatorChar = $seperatorChar;
    }

    /**
     * Fetch all data source values used in any element versions.
     *
     * @param DataSourceValueBag $valueBag
     *
     * @return array
     */
    public function fetchUsedValues(DataSourceValueBag $valueBag)
    {
        /*
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
            foreach ($this->metaDataManager->findByMetaSet($field->getMetaSet()) as $metaData) {
                $value = $metaData->get($field->getId(), $valueBag->getLanguage());

                $values[] = $value;
            }
        }
        */
        // TODO: aus elementen

        $values = [];

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
    private function splitSuggestValues(array $concatenated)
    {
        $keys = [];
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
