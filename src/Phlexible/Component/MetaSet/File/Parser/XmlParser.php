<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet\File\Parser;

use Phlexible\Component\MetaSet\Model\MetaSet;
use Phlexible\Component\MetaSet\Model\MetaSetField;

/**
 * XML parser
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlParser implements ParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse($content)
    {
        $xml = simplexml_load_string($content);

        $xmlAttributes = $xml->attributes();
        $id = (string) $xmlAttributes['id'];
        $name = (string) $xmlAttributes['name'];
        $revision = (int) $xmlAttributes['revision'];
        $createUser = (string) $xmlAttributes['createUser'];
        $createdAt = new \DateTime((string) $xmlAttributes['createdAt']);
        $modifyUser = (string) $xmlAttributes['modifyUser'];
        $modifiedAt = new \DateTime((string) $xmlAttributes['modifiedAt']);

        $metaSet = new MetaSet();
        $metaSet
            ->setId($id)
            ->setName($name)
            ->setRevision($revision)
            ->setCreatedAt($createdAt)
            ->setCreateUser($createUser)
            ->setModifiedAt($modifiedAt)
            ->setModifyUser($modifyUser);

        foreach ($xml->fields->field as $fieldNode) {
            $fieldNodeAttributes = $fieldNode->attributes();
            $id = (string) $fieldNodeAttributes['id'];
            $name = (string) $fieldNodeAttributes['name'];
            $type = (string) $fieldNodeAttributes['type'];
            $options = (string) $fieldNodeAttributes['options'];
            $readonly = (bool) (string) $fieldNodeAttributes['readonly'];
            $required = (bool) (string) $fieldNodeAttributes['required'];
            $synchronized = (bool) (string) $fieldNodeAttributes['synchronized'];
            $field = new MetaSetField();
            $field
                ->setId($id)
                ->setName($name)
                ->setType($type)
                ->setOptions($options)
                ->setReadonly($readonly)
                ->setRequired($required)
                ->setSynchronized($synchronized);
            $metaSet->addField($field);
        }

        return $metaSet;
    }
}
