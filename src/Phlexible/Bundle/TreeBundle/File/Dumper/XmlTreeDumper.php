<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\File\Dumper;

use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Site\Domain\Site;

/**
 * XML tree dumper.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlTreeDumper
{
    /**
     * @param TreeInterface $tree
     * @param Site          $siteroot
     *
     * @return string
     */
    public function dump(TreeInterface $tree, Site $siteroot)
    {
        $dom = new \DOMDocument();
        $dom->formatOutput = true;

        $nodeNodes = array();

        $treeNode = $nodeNodes[null] = $dom->createElement('tree');
        $dom->appendChild($treeNode);

        $treeIdAttr = $dom->createAttribute('siterootId');
        $treeIdAttr->value = (string) $siteroot->getId();
        $treeNode->appendChild($treeIdAttr);

        $rii = new \RecursiveIteratorIterator($tree->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii as $node) {
            /* @var $node NodeContext */

            $nodeNodes[$node->getId()] = $nodeNode = $dom->createElement('node');
            if ($node->getParent()) {
                $parentId = $node->getParent()->getId();
            } else {
                $parentId = null;
            }
            $nodeNodes[$parentId]->appendChild($nodeNode);

            $idAttr = $dom->createAttribute('id');
            $idAttr->value = $node->getId();
            $nodeNode->appendChild($idAttr);

            if ($parentId) {
                $parentIdAttr = $dom->createAttribute('parentId');
                $parentIdAttr->value = $node->getParent()->getId();
                $nodeNode->appendChild($parentIdAttr);
            }

            $nodeTypeAttr = $dom->createAttribute('nodeType');
            $nodeTypeAttr->value = get_class($node);
            $nodeNode->appendChild($nodeTypeAttr);

            $typeAttr = $dom->createAttribute('type');
            $typeAttr->value = $node->getContentType();
            $nodeNode->appendChild($typeAttr);

            $typeIdAttr = $dom->createAttribute('typeId');
            $typeIdAttr->value = $node->getContentId();
            $nodeNode->appendChild($typeIdAttr);

            $sortAttr = $dom->createAttribute('sort');
            $sortAttr->value = $node->getSort();
            $nodeNode->appendChild($sortAttr);

            $sortModeAttr = $dom->createAttribute('sortMode');
            $sortModeAttr->value = $node->getSortMode();
            $nodeNode->appendChild($sortModeAttr);

            $sortDirAttr = $dom->createAttribute('sortDir');
            $sortDirAttr->value = $node->getSortDir();
            $nodeNode->appendChild($sortDirAttr);

            $inNavigationAttr = $dom->createAttribute('inNavigation');
            $inNavigationAttr->value = $node->getInNavigation();
            $nodeNode->appendChild($inNavigationAttr);

            $createUserIdAttr = $dom->createAttribute('createUserId');
            $createUserIdAttr->value = $node->getCreateUserId();
            $nodeNode->appendChild($createUserIdAttr);

            $createdAtAttr = $dom->createAttribute('createdAt');
            $createdAtAttr->value = $node->getCreatedAt()->format('Y-m-d H:i:s');
            $nodeNode->appendChild($createdAtAttr);

            $attributesNode = $dom->createElement('attributes');
            $nodeNode->appendChild($attributesNode);

            if ($node->getAttributes()) {
                foreach ($node->getAttributes() as $key => $value) {
                    $attributeNode = $dom->createElement('attribute');
                    if (!is_scalar($value)) {
                        $attributeNode->textContent = json_encode($value);
                    } else {
                        $attributeNode->textContent = $value;
                    }
                    $attributesNode->appendChild($attributeNode);

                    $attributeKeyAttr = $dom->createAttribute('key');
                    $attributeKeyAttr->value = $key;
                    $attributeNode->appendChild($attributeKeyAttr);
                }
            }

            $statesNode = $dom->createElement('states');
            $nodeNode->appendChild($statesNode);

            $onlineLanguages = $tree->getOnlineLanguages($node);

            foreach ($onlineLanguages as $onlineLanguage) {
                $stateNode = $dom->createElement('state');
                $statesNode->appendChild($stateNode);

                $versionAttr = $dom->createAttribute('version');
                $versionAttr->value = $tree->getOnlineVersion($node, $onlineLanguage);
                $stateNode->appendChild($versionAttr);

                $languageAttr = $dom->createAttribute('language');
                $languageAttr->value = $onlineLanguage;
                $stateNode->appendChild($languageAttr);

                $hashAttr = $dom->createAttribute('hash');
                $hashAttr->value = $tree->getOnlineHash($onlineLanguage);
                $stateNode->appendChild($hashAttr);

                $pubhlishUserAttr = $dom->createAttribute('publishUser');
                $pubhlishUserAttr->value = $tree->getPublishUserId($onlineLanguage);
                $stateNode->appendChild($pubhlishUserAttr);

                $publishedAtAttr = $dom->createAttribute('publishedAt');
                $publishedAtAttr->value = $tree->getPublishedAt($onlineLanguage)->format('Y-m-d H:i:s');
                $stateNode->appendChild($publishedAtAttr);
            }
        }

        return $dom->saveXML();
    }
}
