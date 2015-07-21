<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Search;

use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\TreeBundle\Icon\IconResolver;
use Phlexible\Bundle\TreeBundle\Model\TreeManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Node ID search
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
     * @var SiterootManagerInterface
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
     * @param SiterootManagerInterface      $siterootManager
     * @param \Phlexible\Bundle\TreeBundle\Icon\IconResolver                  $iconResolver
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param string                        $defaultLanguage
     */
    public function __construct(
        TreeManagerInterface $treeManager,
        SiterootManagerInterface $siterootManager,
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
        $tree = $this->treeManager->getByNodeId((int) $query);

        if (!$tree) {
            return array();
        }

        $node = $tree->get((int) $query);

        if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') && !$this->authorizationChecker->isGranted('VIEW', $node)) {
            return array();
        }

        $siteroot = $this->siterootManager->find($node->getSiterootId());
        $icon = $this->iconResolver->resolveNode($node, $this->defaultLanguage);

        return array($this->nodeToResult($node, $siteroot, 'Node ID Search', $icon, $this->defaultLanguage));
    }
}
