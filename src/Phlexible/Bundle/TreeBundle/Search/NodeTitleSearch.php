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

use Doctrine\ORM\EntityManagerInterface;
use Phlexible\Bundle\TreeBundle\Icon\IconResolver;
use Phlexible\Bundle\TreeBundle\Model\TreeManagerInterface;
use Phlexible\Component\Site\Model\SiteManagerInterface;
use Phlexible\Component\Tree\WorkingTreeContext;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Node title search
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeTitleSearch extends AbstractNodeSearch
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

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
     * @param EntityManagerInterface        $entityManager
     * @param TreeManagerInterface          $treeManager
     * @param SiteManagerInterface          $siterootManager
     * @param IconResolver                  $iconResolver
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TreeManagerInterface $treeManager,
        SiteManagerInterface $siterootManager,
        IconResolver $iconResolver,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->entityManager = $entityManager;
        $this->treeManager = $treeManager;
        $this->siterootManager = $siterootManager;
        $this->iconResolver = $iconResolver;
        $this->authorizationChecker = $authorizationChecker;
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
        $treeContext = new WorkingTreeContext('de');

        $repo = $this->entityManager->getRepository('PhlexibleTreeBundle:NodeMappedField');
        $qb = $repo->createQueryBuilder('n');
        $qb
            ->select('n.id', 'n.language')
            ->where($qb->expr()->like('n.backend', $qb->expr()->literal("%$query%")))
            ->orWhere($qb->expr()->like('n.page', $qb->expr()->literal("%$query%")))
            ->orWhere($qb->expr()->like('n.navigation', $qb->expr()->literal("%$query%")));

        $results = array();
        foreach ($qb->getQuery()->getScalarResult() as $row) {
            $nodeId = $row['id'];
            $language = $row['language'];

            $tree = $this->treeManager->getByNodeId($treeContext, $nodeId);
            $node = $tree->get($nodeId);

            if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') && !$this->authorizationChecker->isGranted('VIEW', $node)) {
                return array();
            }

            $siteroot = $this->siterootManager->find($node->getSiterootId());
            $icon = $this->iconResolver->resolveNode($node);

            $results[] = $this->nodeToResult($node, $siteroot, 'Node Title Search', $icon, $language);
        }

        return $results;
    }
}
