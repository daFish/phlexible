<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Controller\Data;

use Phlexible\Bundle\ElementBundle\ElementEvents;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Event\SaveElementEvent;
use Phlexible\Bundle\ElementBundle\Exception\InvalidArgumentException;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\TreeBundle\Model\TreeManagerInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Elementtype\Domain\ElementtypeStructure;
use Phlexible\Component\Elementtype\Field\FieldRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Data saver.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DataSaver
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var FieldRegistry
     */
    private $fieldRegistry;

    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    private $availableLanguages;

    /**
     * @param ElementService           $elementService
     * @param FieldRegistry            $fieldRegistry
     * @param TreeManagerInterface     $treeManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param string                   $availableLanguages
     */
    public function __construct(
        ElementService $elementService,
        FieldRegistry $fieldRegistry,
        TreeManagerInterface $treeManager,
        EventDispatcherInterface $eventDispatcher,
        $availableLanguages)
    {
        $this->elementService = $elementService;
        $this->fieldRegistry = $fieldRegistry;
        $this->treeManager = $treeManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->availableLanguages = explode(',', $availableLanguages);
    }

    /**
     * Save element data.
     *
     * @param Request       $request
     * @param UserInterface $user
     *
     * @return ElementVersion
     */
    public function save(Request $request, UserInterface $user)
    {
        $eid = $request->get('eid');
        $language = $request->get('language');
        $nodeId = $request->get('tid');
        $isPublish = $request->get('isPublish');
        $values = $request->get('values');
        $publishComment = $request->get('publishComment');

        if ($values) {
            $values = json_decode($values, true);
        }

        $element = $this->elementService->findElement($eid);
        $elementtype = $this->elementService->findElementtype($element);
        $elementtypeStructure = $elementtype->getStructure();

        $oldElementVersion = $this->elementService->findElementVersion($element, $element->getLatestVersion());
        $oldElementStructure = $this->elementService->findElementStructure($oldElementVersion, 'de');
        $oldVersion = $oldElementVersion->getVersion();
        $isMaster = $element->getMasterLanguage() === $language;

        $node = $this->treeManager->getByNodeId($nodeId)->get($nodeId);

        $comment = null;
        if ($request->get('comment')) {
            $comment = $request->get('comment');
        }

        if ($language === $element->getMasterLanguage()) {
            $elementStructure = new ElementStructure();
            if ($oldElementStructure->getDataId()) {
                $elementStructure
                    ->setDataId($oldElementStructure->getDataId())
                    ->setDsId($oldElementStructure->getDsId())
                    ->setType('root')
                    ->setName($oldElementStructure->getName());
            } else {
                $elementStructure
                    ->setDataId(Uuid::generate())
                    ->setDsId($elementtypeStructure->getRootDsId())
                    ->setType('root')
                    ->setName($elementtypeStructure->getRootNode()->getName());
            }
            $map = $this->applyStructure($elementStructure, $elementtypeStructure, $values, $language, $oldElementStructure);
            $this->applyOldValues($elementStructure, $oldElementStructure, $language);
            $this->applyValues($elementStructure, $elementtypeStructure, $values, $language, $map);
        } else {
            $elementStructure = clone $oldElementStructure;
            $this->applyValues($elementStructure, $elementtypeStructure, $values, $language);
        }

        $elementVersion = $this->elementService->createElementVersion($element, $elementStructure, $language, $user->getId(), $comment);

        $event = new SaveElementEvent($element, $language, $oldVersion);
        $this->eventDispatcher->dispatch(ElementEvents::SAVE_ELEMENT, $event);

        $publishSlaves = array();
        if ($isPublish) {
            $publishSlaves = $this->checkPublishSlaves($elementVersion, $node, $language);
            $this->publishNode($elementVersion, $node, $language, $user->getId(), $publishComment, $publishSlaves);
        }

        return array($elementVersion, $node, $publishSlaves);
    }

    /**
     * @param ElementStructure     $rootElementStructure
     * @param ElementtypeStructure $elementtypeStructure
     * @param array                $values
     *
     * @return ElementStructure
     */
    private function applyStructure(ElementStructure $rootElementStructure, ElementtypeStructure $elementtypeStructure, array $values)
    {
        $this->structures[null] = $rootElementStructure;
        $map = array(null => $rootElementStructure->getDataId());

        foreach ($values as $key => $value) {
            $parts = explode('__', $key);
            $identifier = $parts[0];
            $repeatableIdentifier = null;
            if (isset($parts[1])) {
                $repeatableIdentifier = $parts[1];
            }

            if (preg_match('/^group-([-a-f0-9]{36})-id-([a-fA-F0-9-]+)$/', $identifier, $match)) {
                // existing repeatable group
                $parent = $this->structures[$repeatableIdentifier];
                $dsId = $match[1];
                $dataId = $match[2];
                $node = $elementtypeStructure->getNode($dsId);
                $map[$identifier] = $dataId;
                $this->structures[$identifier] = $elementStructure = new ElementStructure();
                $elementStructure
                    ->setDataId($dataId)
                    ->setDsId($dsId)
                    ->setParentName($parent->getName())
                    ->setName($node->getName());
                $parent->addStructure($elementStructure);
            } elseif (preg_match('/^group-([-a-f0-9]{36})-new-.+$/', $identifier, $match)) {
                // new repeatable group
                $parent = $this->structures[$repeatableIdentifier];
                $dsId = $match[1];
                $dataId = Uuid::generate();
                $node = $elementtypeStructure->getNode($dsId);
                $map[$identifier] = $dataId;
                $this->structures[$identifier] = $elementStructure = new ElementStructure();
                $elementStructure
                    ->setDataId($dataId)
                    ->setDsId($dsId)
                    ->setParentName($parent->getName())
                    ->setName($node->getName());
                $parent->addStructure($elementStructure);
            }
        }

        return $map;
    }

    /**
     * @param ElementStructure $rootElementStructure
     * @param ElementStructure $oldRootElementStructure
     * @param string           $skipLanguage
     */
    private function applyOldValues(ElementStructure $rootElementStructure, ElementStructure $oldRootElementStructure, $skipLanguage)
    {
        foreach ($oldRootElementStructure->getValues() as $value) {
            if ($value->getLanguage() === $skipLanguage) {
                continue;
            }
            $rootElementStructure->setValue($value);
        }

        $rii = new \RecursiveIteratorIterator($rootElementStructure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii as $structure) {
            /* @var $structure ElementStructure */
            $oldStructure = $this->findStructureByDataId($oldRootElementStructure, $structure->getDataId());
            if (!$oldStructure) {
                continue;
            }
            foreach ($oldStructure->getValues() as $value) {
                if ($value->getLanguage() === $skipLanguage) {
                    continue;
                }
                $structure->setValue($value);
            }
        }
    }

    /**
     * @param ElementStructure     $rootElementStructure
     * @param ElementtypeStructure $elementtypeStructure
     * @param array                $values
     * @param string               $language
     * @param array                $map
     *
     * @throws InvalidArgumentException
     *
     * @return ElementStructure
     */
    private function applyValues(ElementStructure $rootElementStructure, ElementtypeStructure $elementtypeStructure, array $values, $language, array $map = null)
    {
        $rootElementStructure->removeLanguage($language);

        foreach ($values as $key => $value) {
            $parts = explode('__', $key);
            $identifier = $parts[0];
            $repeatableIdentifier = null;
            if (isset($parts[1])) {
                $repeatableIdentifier = $parts[1];
            }

            if (preg_match('/^field-([-a-f0-9]{36})-id-([0-9]+)$/', $identifier, $match)) {
                // existing value
                $dsId = $match[1];
                $node = $elementtypeStructure->getNode($dsId);
                $options = null;
                $field = $this->fieldRegistry->getField($node->getType());
                $value = $field->fromRaw($value);
                $elementStructureValue = new ElementStructureValue(null, $dsId, $language, $node->getType(), $field->getDataType(), $node->getName(), $value, $options);
                if ($map) {
                    $mapId = $map[$repeatableIdentifier];
                } else {
                    $mapId = $rootElementStructure->getDataId();
                    if ($repeatableIdentifier) {
                        $mapId = mb_substr($repeatableIdentifier, -36, null, 'UTF-8');
                    }
                }
                $elementStructure = $this->findStructureByDataId($rootElementStructure, $mapId);
                if (!$elementStructure) {
                    throw new InvalidArgumentException("Element structure $mapId not found. Repeatable identifier: $repeatableIdentifier. Map: ".print_r($map, true));
                }
                $elementStructure->setValue($elementStructureValue);
            } elseif (preg_match('/^field-([-a-f0-9]{36})-new-.+$/', $identifier, $match)) {
                // new value
                $dsId = $match[1];
                $node = $elementtypeStructure->getNode($dsId);
                $field = $this->fieldRegistry->getField($node->getType());
                $value = $field->fromRaw($value);
                $options = null;
                $elementStructureValue = new ElementStructureValue(null, $dsId, $language, $node->getType(), $field->getDataType(), $node->getName(), $value, $options);
                if ($map) {
                    $mapId = $map[$repeatableIdentifier];
                } else {
                    $mapId = $rootElementStructure->getDataId();
                    if ($repeatableIdentifier) {
                        $mapId = mb_substr($repeatableIdentifier, -36, null, 'UTF-8');
                    }
                }
                $elementStructure = $this->findStructureByDataId($rootElementStructure, $mapId);
                if (!$elementStructure) {
                    throw new InvalidArgumentException("Element structure $mapId not found. Repeatable identifier: $repeatableIdentifier. Map: ".print_r($map, true));
                }
                $elementStructure->setValue($elementStructureValue);
            }
        }

        return $rootElementStructure;
    }

    /**
     * @param ElementStructure $structure
     * @param string           $dataId
     *
     * @return ElementStructure|null
     */
    private function findStructureByDataId(ElementStructure $structure, $dataId)
    {
        if ($structure->getDataId() === $dataId) {
            return $structure;
        }

        foreach ($structure->getStructures() as $childStructure) {
            $result = $this->findStructureByDataId($childStructure, $dataId);
            if ($result) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @param ElementVersion $elementVersion
     * @param NodeContext    $node
     * @param string         $language
     *
     * @return array
     */
    private function checkPublishSlaves(ElementVersion $elementVersion, NodeContext $node, $language)
    {
        $publishSlaves = array('elements' => array(), 'languages' => array());

        if ($elementVersion->getElement()->getMasterLanguage() !== $language) {
            return $publishSlaves;
        }

        foreach ($this->availableLanguages as $slaveLanguage) {
            if ($language === $slaveLanguage) {
                continue;
            }

            if ($node->getTree()->isPublished($node, $slaveLanguage)) {
                if (!$node->getTree()->isAsync($node, $slaveLanguage)) {
                    $publishSlaves['languages'][] = $slaveLanguage;
                } else {
                    $publishSlaves['elements'][] = array($node->getId(), $slaveLanguage, 0, 'async', 1);
                }
            }
            // TODO: needed?
            /*
            } else {
                if ($this->container->getParameter('phlexible_element.publish.cross_language_publish_offline')) {
                    $publishSlaves[] = array($node->getId(), $slaveLanguage, $newVersion, '', 0);
                }
            */
        }

        return $publishSlaves;
    }

    /**
     * @param ElementVersion $elementVersion
     * @param NodeContext    $node
     * @param string         $language
     * @param string         $userId
     * @param string|null    $comment
     * @param array          $publishSlaves
     */
    private function publishNode(ElementVersion $elementVersion, NodeContext $node, $language, $userId, $comment = null, array $publishSlaves = array())
    {
        // publish node
        $node->getTree()->publish(
            $node,
            $elementVersion->getVersion(),
            $language,
            $userId,
            $comment
        );

        if (!empty($publishSlaves['languages'])) {
            foreach ($publishSlaves['languages'] as $slaveLanguage) {
                // publish slave node
                $this->treeManager->publish(
                    $node,
                    $elementVersion->getVersion(),
                    $slaveLanguage,
                    $userId,
                    $comment
                );

                // TODO: gnarf
                // workaround to fix missing catch results for non master language elements
                /*
                Makeweb_Elements_Element_History::insert(
                    Makeweb_Elements_Element_History::ACTION_SAVE,
                    $eid,
                    $newVersion,
                    $slaveLanguage
                );
                */
            }
        }
    }
}
