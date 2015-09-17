<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller\Tree;

use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Component\Elementtype\Domain\Elementtype;
use Phlexible\Component\Elementtype\Domain\ElementtypeStructure;
use Phlexible\Component\Elementtype\Domain\ElementtypeStructureNode;
use Phlexible\Component\Elementtype\ElementtypeService;
use Phlexible\Component\Elementtype\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Tree saver.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeSaver
{
    /**
     * @var ElementtypeService
     */
    private $elementtypeService;

    /**
     * @param ElementtypeService $elementtypeService
     */
    public function __construct(ElementtypeService $elementtypeService)
    {
        $this->elementtypeService = $elementtypeService;
    }

    /**
     * Save an Element Type data tree.
     *
     * @param Request       $request
     * @param UserInterface $user
     *
     * @throws InvalidArgumentException
     *
     * @return Elementtype
     */
    public function save(Request $request, UserInterface $user)
    {
        $elementtypeId = $request->get('element_type_id', false);
        $data = json_decode($request->get('data'), true);

        if (!$elementtypeId) {
            throw new InvalidArgumentException('No elementtype ID.');
        }

        $rootData = $data[0];
        $rootType = $rootData['type'];
        $rootProperties = $rootData['properties'];
        $rootConfig = $rootProperties['root'];
        $rootMappings = !empty($rootProperties['mappings']) ? $rootProperties['mappings'] : null;
        $rootDsId = !empty($rootData['ds_id']) ? $rootData['ds_id'] : Uuid::generate();

        if (!isset($rootData['type']) || ($rootData['type'] !== 'root' && $rootData['type'] !== 'referenceroot')) {
            throw new InvalidArgumentException('Invalid root node.');
        }

        if (!isset($rootConfig['name']) || !trim($rootConfig['name'])) {
            throw new InvalidArgumentException('No name.');
        }

        $name = trim($rootConfig['name']);
        $icon = trim($rootConfig['icon']);
        $hideChildren = !empty($rootConfig['hide_children']);
        $defaultTab = strlen($rootConfig['default_tab']) ? $rootConfig['default_tab'] : null;
        $defaultContentTab = strlen($rootConfig['default_content_tab']) ? $rootConfig['default_content_tab'] : null;
        $metasetId = strlen($rootConfig['metaset']) ? $rootConfig['metaset'] : null;
        $template = trim($rootConfig['template']) ?: null;
        $comment = trim($rootConfig['comment']) ?: null;

        $elementtype = $this->elementtypeService->findElementtype($elementtypeId);
        $elementtype
            ->setRevision($elementtype->getRevision() + 1)
            ->setName($name)
            ->setIcon($icon)
            ->setHideChildren($hideChildren)
            ->setDefaultTab($defaultTab)
            ->setDefaultContentTab($defaultContentTab)
            ->setMetaSetId($metasetId)
            ->setTemplate($template)
            ->setMappings($rootMappings)
            ->setComment($comment)
            ->setModifyUser($user->getUsername())
            ->setModifiedAt(new \DateTime());

        $elementtypeStructure = null;
        if (isset($rootData['children'])) {
            $fieldData = $rootData['children'];
            $elementtypeStructure = $this->buildElementtypeStructure($rootType, $rootDsId, $user, $fieldData);
            $elementtype->setStructure($elementtypeStructure);
        }

        $this->elementtypeService->updateElementtype($elementtype, false);

        // update elementtypes that use this elementtype as reference

        /*
        if ($elementtype->getType() === 'reference') {
            $this->updateElementtypesUsingReference($elementtype, $user->getId());
        }
        */

        return $elementtype;
    }

    /**
     * @param string        $rootType
     * @param string        $rootDsId
     * @param UserInterface $user
     * @param array         $data
     *
     * @return \Phlexible\Component\Elementtype\Domain\ElementtypeStructure
     */
    private function buildElementtypeStructure($rootType, $rootDsId, UserInterface $user, array $data)
    {
        $elementtypeStructure = new ElementtypeStructure();

        $sort = 1;

        $rootNode = new ElementtypeStructureNode();
        $rootNode
            ->setDsId($rootDsId)
            ->setType($rootType)
            ->setName('root');
        //    ->setSort($sort++)

        $elementtypeStructure->addNode($rootNode);

        $this->iterateData($elementtypeStructure, $rootNode, $user, $sort, $data);

        return $elementtypeStructure;
    }

    /**
     * @param \Phlexible\Component\Elementtype\Domain\ElementtypeStructure     $elementtypeStructure
     * @param \Phlexible\Component\Elementtype\Domain\ElementtypeStructureNode $rootNode
     * @param UserInterface                                                    $user
     * @param int                                                              $sort
     * @param array                                                            $data
     *
     * @return mixed
     */
    private function iterateData(
        ElementtypeStructure $elementtypeStructure,
        ElementtypeStructureNode $rootNode,
        UserInterface $user,
        $sort,
        array $data)
    {
        foreach ($data as $row) {
            if (!$row['parent_ds_id']) {
                $row['parent_ds_id'] = $rootNode->getDsId();
            }
            $node = new ElementtypeStructureNode();
            $parentNode = $elementtypeStructure->getNode($row['parent_ds_id']);

            $node
                ->setDsId(!empty($row['ds_id']) ? $row['ds_id'] : Uuid::generate())
                ->setParentDsId($parentNode->getDsId())
                ->setParentNode($parentNode);
            //    ->setSort(++$sort)

            if ($row['type'] === 'reference' && isset($row['reference']['new'])) {
                $firstChild = $row['children'][0];

                $referenceRootDsId = Uuid::generate();
                foreach ($row['children'] as $index => $referenceRow) {
                    $row['children'][$index]['parent_ds_id'] = $referenceRootDsId;
                }
                $referenceElementtypeStructure = $this->buildElementtypeStructure(
                    'referenceroot',
                    $referenceRootDsId,
                    $user,
                    $row['children']
                );

                $referenceElementtype = $this->elementtypeService->createElementtype(
                    'reference',
                    'reference_'.$firstChild['properties']['field']['working_title'].'_'.uniqid(),
                    '_fallback.gif',
                    $referenceElementtypeStructure,
                    array(),
                    $user->getDisplayName(),
                    false
                );

                $node
                    ->setType('reference')
                    ->setName('reference_'.$referenceElementtype->getName())
                    ->setReferenceElementtypeId($referenceElementtype->getId());
                    //->setReferenceVersion($referenceElementtypeVersion->getVersion())

                $elementtypeStructure->addNode($node);
            } elseif ($row['type'] === 'reference') {
                $referenceElementtype = $this->elementtypeService->findElementtype($row['reference']['refID']);

                $node
                    ->setType('reference')
                    ->setName('reference_'.$referenceElementtype->getName())
                    ->setReferenceElementtypeId($referenceElementtype->getId());
                //    ->setReferenceVersion($row['reference']['refVersion'])

                $elementtypeStructure->addNode($node);
            } else {
                $properties = $row['properties'];

                $node
                    ->setType($properties['field']['type'])
                    ->setName(trim($properties['field']['working_title']))
                    ->setComment(trim($properties['field']['comment']) ?: null)
                    ->setConfiguration(!empty($properties['configuration']) ? $properties['configuration'] : null)
                    ->setValidation(!empty($properties['validation']) ? $properties['validation'] : null)
                    ->setLabels(!empty($properties['labels']) ? $properties['labels'] : null);

                $elementtypeStructure->addNode($node);

                if (!empty($row['children'])) {
                    $sort = $this->iterateData($elementtypeStructure, $rootNode, $user, $sort, $row['children']);
                }
            }
        }

        return $sort;
    }
}
