<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersion\Serializer;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;

/**
 * Serializer interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ArraySerializer implements SerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function serialize(ElementVersion $elementVersion, $language)
    {
        return $this->walk($elementVersion->getContent(), $language);
    }

    /**
     * @param array  $structure
     * @param string $language
     * @param string $parentId
     * @param string $parentDsId
     * @param string $parentName
     *
     * @return array
     */
    private function walk(array $structure, $language, $parentId = null, $parentDsId = null, $parentName = null)
    {
        $valueDatas = array();
        if (isset($structure['values'])) {
            foreach ($structure['values'] as $dsId => $value) {
                $valueDatas[] = array(
                    'id' => $structure['id'].'_'.$dsId,
                    'dsId' => $dsId,
                    'name' => $structure['id'].'_'.$dsId,
                    'type' => gettype($value[$language]),
                    'content' => $value[$language],
                    'attributes' => array(),
                );
            }
        }

        $id = isset($structure['id']) ? $structure['id'] : null;
        $dsId = isset($structure['dsId']) ? $structure['dsId'] : null;
        $name = $parentDsId.'_'.$dsId;

        $children = array();
        if (isset($structure['collections'])) {
            foreach ($structure['collections'] as $childStructure) {
                $children[] = $this->walk($childStructure, $language, $id, $dsId, $name);
            }
        }

        return array(
            'id' => $id,
            'dsId' => $dsId,
            'parentId' => $parentId,
            'parentDsId' => $parentDsId,
            'name' => $name,
            'parentName' => $parentName,
            'attributes' => array(),
            'structures' => $children,
            'values' => $valueDatas,
        );

        return $structureData;
    }
}
