<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Search;

use Phlexible\Bundle\TreeBundle\Icon\IconResolver;
use Phlexible\Bundle\TreeBundle\Model\TreeManagerInterface;
use Phlexible\Component\Site\Model\SiteManagerInterface;
use Phlexible\Component\Tree\WorkingTreeContext;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Node ID search.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeIdSearch extends AbstractNodeSearch
{
    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    /**
     * @var SiteManagerInterface
     */
    private $siterootManager;

    /**
     * @var IconResolver
     */
    private $iconResolver;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var string
     */
    private $defaultLanguage;

    /**
     * @param TreeManagerInterface          $treeManager
     * @param SiteManagerInterface          $siterootManager
     * @param IconResolver                  $iconResolver
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param string                        $defaultLanguage
     */
    public function __construct(
        TreeManagerInterface $treeManager,
        SiteManagerInterface $siterootManager,
        IconResolver $iconResolver,
        AuthorizationCheckerInterface $authorizationChecker,
        $defaultLanguage)
    {
        $this->treeManager = $treeManager;
        $this->siterootManager = $siterootManager;
        $this->iconResolver = $iconResolver;
        $this->authorizationChecker = $authorizationChecker;
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole()
    {
        return 'ROLE_ELEMENTS';
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchKey()
    {
        return 'tid';
    }

    /**
     * {@inheritdoc}
     */
    public function search($query)
    {
        if (!is_int($query)) {
            return array();
        }

        $treeContext = new WorkingTreeContext('de');
        $tree = $this->treeManager->getByNodeId($treeContext, (int) $query);

        if (!$tree) {
            return array();
        }

        $node = $tree->get((int) $query);

        if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') && !$this->authorizationChecker->isGranted('VIEW', $node)) {
            return array();
        }

        $siteroot = $this->siterootManager->find($node->getSiterootId());
        $icon = $this->iconResolver->resolveNode($node);

        return array($this->nodeToResult($node, $siteroot, 'Node ID Search', $icon, $this->defaultLanguage));
    }
}
