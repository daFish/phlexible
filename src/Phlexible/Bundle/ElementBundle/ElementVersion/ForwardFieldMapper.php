<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersion;

use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;

/**
 * Forward field mapper
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ForwardFieldMapper implements FieldMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function accept($key)
    {
        return in_array($key, ['forward']);
    }

    /**
     * {@inheritdoc}
     */
    public function map(ElementStructure $elementStructure, $language, array $mapping)
    {
        $dsId = $mapping['fields'][0]['dsId'];
        $title = $this->findValue($elementStructure, $dsId, $language);

        if (!$title || !$title->getValue()) {
            return null;
        }

        $value = $title->getValue();

        return is_array($value) ? json_encode($value) : $value;
    }

    /**
     * @param ElementStructure $elementStructure
     * @param string           $dsId
     * @param string           $language
     *
     * @return null|ElementStructureValue
     */
    private function findValue(ElementStructure $elementStructure, $dsId, $language)
    {
        if ($elementStructure->hasValueByDsId($dsId, $language)) {
            return $elementStructure->getValueByDsId($dsId, $language);
        }

        foreach ($elementStructure->getStructures() as $childStructure) {
            $value = $this->findValue($childStructure, $dsId, $language);
            if ($value) {
                return $value;
            }
        }

        return null;
    }
}
