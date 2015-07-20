<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersion\Serializer;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;

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
    public function serialize(ElementVersion $elementVersion, $language)
    {
        return $this->walk($elementVersion->getContent(), $language);
    }

    /**
     * @param array  $structure
     * @param string $language
     *
     * @return array
     */
    private function walk(array $structure, $language, $parentId = null, $parentDsId = null, $parentName = null)
    {
        $valueDatas = array();
        foreach ($structure['values'] as $dsId => $value) {
            $valueDatas[] = array(
                'id'         => $structure['id'] . '_' . $dsId,
                'dsId'       => $dsId,
                'name'       => $structure['id'] . '_' . $dsId,
                'type'       => gettype($value[$language]),
                'content'    => $value[$language],
                'attributes' => array(),
            );
        }

        $id = isset($structure['id']) ? $structure['id'] : null;
        $dsId = isset($structure['dsId']) ? $structure['dsId'] : null;
        $name = $parentDsId . '_' . $dsId;

        $structureDatas = array();
        foreach ($structure['children'] as $childStructure) {
            $structureDatas[] = $this->walk($childStructure, $language, $id, $dsId, $name);
        }

        $structureData = array(
            'id'         => $id,
            'dsId'       => $dsId,
            'parentId'   => $parentId,
            'parentDsId' => $parentDsId,
            'name'       => $name,
            'parentName' => $parentName,
            'attributes' => array(),
            'structures' => $structureDatas,
            'values'     => $valueDatas,
        );

        return $structureData;
    }
}
