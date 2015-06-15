<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\ElementStructure\Serializer;

use Phlexible\Bundle\ElementBundle\Model\ElementStructure;

/**
 * Serializer interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ArraySerializer implements SerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function serialize(ElementStructure $elementStructure, $language)
    {
        return $this->walk($elementStructure, $language);
    }

    /**
     * @param ElementStructure $elementStructure
     * @param string           $language
     *
     * @return array
     */
    private function walk(ElementStructure $elementStructure, $language)
    {
        $valueDatas = [];
        foreach ($elementStructure->getValues($language) as $value) {
            $valueDatas[] = [
                'id'         => $value->getId(),
                'dsId'       => $value->getDsId(),
                'name'       => $value->getName(),
                'type'       => $value->getType(),
                'content'    => $value->getValue(),
                'attributes' => $value->getAttributes(),
            ];
        }

        $structureDatas = [];
        foreach ($elementStructure->getStructures() as $subStructure) {
            $structureDatas[] = $this->walk($subStructure, $language);
        }

        $structureData = [
            //'id'         => $elementStructure->getId(),
            //'dataId'     => $elementStructure->getDataId(),
            'id'         => $elementStructure->getDataId(),
            'dsId'       => $elementStructure->getDsId(),
            'parentId'   => $elementStructure->getParentId(),
            'parentDsId' => $elementStructure->getParentDsId(),
            'name'       => $elementStructure->getName(),
            'parentName' => $elementStructure->getParentName(),
            'attributes' => $elementStructure->getAttributes(),
            'structures' => $structureDatas,
            'values'     => $valueDatas,
        ];

        return $structureData;
    }
}
