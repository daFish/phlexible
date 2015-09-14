<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Element\Publish;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeManagerInterface;
use Phlexible\Component\Node\Model\NodeInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Publisher
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Publisher
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    /**
     * @var TeaserManagerInterface
     */
    private $teaserManager;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param ElementService                $elementService
     * @param TreeManagerInterface          $treeManager
     * @param TeaserManagerInterface        $teaserManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        ElementService $elementService,
        TreeManagerInterface $treeManager,
        TeaserManagerInterface $teaserManager,
        AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->elementService = $elementService;
        $this->treeManager = $treeManager;
        $this->teaserManager = $teaserManager;
        $this->authorizationChecker = $authorizationChecker;

        /*
        $this->_versionSelect = $db->select()
            ->from($db->prefix . 'element', 'latest_version')
            ->where('eid = :eid')
            ->limit(1);

        $this->_instanceSelect = $db->select()
            ->from($db->prefix . 'element_tree', array('id'))
            ->where('eid = :eid')
            ->where('id != :skipTid');

        $this->_teasersSelect = $db->select()
            ->from(array('ett' => $db->prefix . 'element_tree_teasers'))
            ->joinLeft(
                array('etto' => $db->prefix . 'element_tree_teasers_online'),
                'ett.id = etto.teaser_id AND etto.language = :language'
            )
            ->where('ett.tree_id = :tid');

        $this->_teaserSelect = $db->select()
            ->from(array('ett' => $db->prefix . 'element_tree_teasers'))
            ->joinLeft(
                array('etto' => $db->prefix . 'element_tree_teasers_online'),
                'ett.id = etto.teaser_id AND etto.language = :language'
            )
            ->where('ett.id = :teaserId');

        $this->_teaserInstanceSelect = $db->select()
            ->from($db->prefix . 'element_tree_teasers', array('id'))
            ->where('teaser_eid = :eid')
            ->where('id != :skipTeaserId');
        */
    }

    /**
     * @param int    $nodeId
     * @param string $language
     * @param int    $version
     * @param bool   $includeElements
     * @param bool   $includeElementInstances
     * @param bool   $includeTeasers
     * @param bool   $includeTeaserInstances
     * @param bool   $recursive
     * @param bool   $onlyOffline
     * @param bool   $onlyAsync
     *
     * @return array
     */
    public function getPreview(
        $nodeId,
        $language,
        $version,
        $includeElements,
        $includeElementInstances,
        $includeTeasers,
        $includeTeaserInstances,
        $recursive,
        $onlyOffline,
        $onlyAsync)
    {
        $tree = $this->treeManager->getByNodeId($nodeId);
        $node = $tree->get($nodeId);

        $result = array();

        if ($includeElements) {
            $result = $this->handleNode(
                $result,
                0,
                implode('/', $tree->getPath($node)),
                $node,
                $version,
                $language,
                $onlyAsync,
                $onlyOffline
            );
        }
        if ($includeTeasers) {
            $result = $this->handleTeasers(
                $result,
                0,
                implode('/', $tree->getPath($node)),
                $node,
                $language,
                $onlyAsync,
                $onlyOffline,
                $includeTeaserInstances
            );
        }

        if ($recursive) {
            $rii = new \RecursiveIteratorIterator($node->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($rii as $childNode) {
                /* @var $childNode NodeInterface */

                set_time_limit(5);

                if ($includeElements) {
                    $result = $this->handleNode(
                        $result,
                        $rii->getDepth() + 1,
                        implode('/', $tree->getPath($childNode)),
                        $childNode,
                        null,
                        $language,
                        $onlyAsync,
                        $onlyOffline
                    );
                }
                if ($includeTeasers) {
                    $result = $this->handleTeasers(
                        $result,
                        $rii->getDepth() + 1,
                        implode('/', $tree->getPath($childNode)),
                        $childNode,
                        $language,
                        $onlyAsync,
                        $onlyOffline,
                        $includeTeaserInstances
                    );
                }
            }
        }

        foreach ($result as $key => $row) {
            if ($includeElementInstances && 'full_element' === $row['type']) {
                $instanceNodes = $this->treeManager->getInstanceNodes($node);

                foreach ($instanceNodes as $instanceNode) {
                    /* @var $instanceNode NodeInterface */

                    $result = $this->handleNode(
                        $result,
                        $row['depth'],
                        $row['path'],
                        $instanceNode,
                        null,
                        $language,
                        $onlyAsync,
                        $onlyOffline,
                        true
                    );
                }
            } elseif ($includeTeaserInstances && 'part_element' === $row['type']) {
                $instanceTeasers = $this->teaserManager->getInstanceTeasers($teaser);

                foreach ($instanceTeasers as $instanceTeaser) {
                    $result = $this->handleTeaser(
                        $result,
                        $row['depth'],
                        $teaser,
                        $language,
                        $onlyAsync,
                        $onlyOffline,
                        true
                    );
                }
            }
        }

        return array_values($result);
    }

    /**
     * @param array         $result
     * @param int           $depth
     * @param string        $path
     * @param NodeInterface $node
     * @param int           $version
     * @param string        $language
     * @param bool          $onlyAsync
     * @param bool          $onlyOffline
     * @param bool          $isInstance
     *
     * @return array
     */
    private function handleNode(
        array $result,
        $depth,
        $path,
        NodeInterface $node,
        $version,
        $language,
        $onlyAsync,
        $onlyOffline,
        $isInstance = false)
    {
        if (array_key_exists('treenode_' . $node->getId(), $result)) {
            return $result;
        }

        $include = true;

        if ($onlyAsync || $onlyOffline) {
            $include = false;

            if ($onlyAsync && $node->getTree()->isAsync($node, $language)) {
                $include = true;
            }
            if ($onlyOffline && !$node->getTree()->isPublished($node, $language)) {
                $include = true;
            }
        }
        if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN', $node)) {
            if (!$this->authorizationChecker->isGranted($node, array('permission' => 'PUBLISH', 'language' => $language))) {
                $include = false;
            }
        }

        if (!$include) {
            return $result;
        }

        $element = $this->elementService->findElement($node->getContentId());
        if (!$version) {
            $version = $element->getLatestVersion();
        }
        $elementVersion = $this->elementService->findElementVersion($element, $version);

        $result['treenode_' . $node->getId()] = array(
            'type'      => 'full_element',
            'instance'  => $isInstance,
            'depth'     => $depth,
            'path'      => $path . '+' . $language,
            'tid'       => $node->getId(),
            'teaser_id' => null,
            'eid'       => $node->getEid(),
            'version'   => $version,
            'language'  => $language,
            'title'     => $elementVersion->getBackendTitle($language),
            'icon'      => '',// TODO: $elementVersion->getIconUrl($node->getIconParams($language)),
        );

        return $result;
    }

    /**
     * @param array         $result
     * @param int           $depth
     * @param string        $path
     * @param NodeInterface $node
     * @param string        $language
     * @param bool          $onlyAsync
     * @param bool          $onlyOffline
     * @param bool          $includeTeaserInstances
     *
     * @return array
     */
    protected function handleTeasers(
        array $result,
        $depth,
        $path,
        NodeInterface $node,
        $language,
        $onlyAsync,
        $onlyOffline,
        $includeTeaserInstances)
    {
        $teasers = $this->teaserManager->findForLayoutAreaAndNodeContext(null, $node);

        foreach ($teasers as $teaser) {
            $result = $this->handleTeaser($result, $depth, $path, $teaser, $language, $onlyAsync, $onlyOffline);
        }

        return $result;
    }

    /**
     * @param array  $result
     * @param int    $depth
     * @param string $path
     * @param Teaser $teaser
     * @param string $language
     * @param bool   $onlyAsync
     * @param bool   $onlyOffline
     * @param bool   $isInstance
     *
     * @return mixed
     */
    protected function handleTeaser(
        $result,
        $depth,
        $path,
        Teaser $teaser,
        $language,
        $onlyAsync,
        $onlyOffline,
        $isInstance = false)
    {
        if ($teaser['type'] !== 'teaser') {
            return $result;
        }

        if (array_key_exists('teaser_' . $teaser['id'], $result)) {
            return $result;
        }

        $version = $this->_db->fetchOne($this->_versionSelect, array('eid' => $teaser['teaser_eid']));

        $isAsync = !!($teaser['version'] && $teaser['version'] != $version);
        $isPublished = !!$teaser['version'];

        $include = true;
        if ($onlyAsync || $onlyOffline) {
            $include = false;

            if ($onlyAsync && $isAsync) {
                $include = true;
            }
            if ($onlyOffline && !$isPublished) {
                $include = true;
            }
        }

        if (!$include) {
            return $result;
        }

        $elementVersion = $this->elementService->get($teaser['teaser_eid'], $version);

        $teaserNode = new Makeweb_Teasers_Node($teaser['id']);

        $result['teaser_' . $teaser['id']] = array(
            'type'      => 'part_element',
            'instance'  => $isInstance,
            'depth'     => $depth,
            'path'      => $path . '+' . $language . '+' . $teaser['id'] . '+' . $language,
            'tid'       => null,
            'teaser_id' => $teaser['id'],
            'eid'       => $teaser['teaser_eid'],
            'version'   => $version,
            'language'  => $language,
            'title'     => $elementVersion->getBackendTitle($language),
            'icon'      => $elementVersion->getIconUrl($teaserNode->getIconParams($language)),
        );

        return $result;
    }
}
