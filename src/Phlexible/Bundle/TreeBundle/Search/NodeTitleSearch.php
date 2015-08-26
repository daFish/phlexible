<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Search;

use Doctrine\ORM\EntityManagerInterface;
use Phlexible\Bundle\TreeBundle\Icon\IconResolver;
use Phlexible\Bundle\TreeBundle\Model\TreeManagerInterface;
use Phlexible\Component\Site\Model\SiteManagerInterface;
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
     * @var \Phlexible\Component\Site\Model\SiteManagerInterface
     */
    private $siterootManager;

    /**
     * @var \Phlexible\Bundle\TreeBundle\Icon\IconResolver
     */
    private $iconResolver;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param EntityManagerInterface        $entityManager
     * @param TreeManagerInterface          $treeManager
     * @param \Phlexible\Component\Site\Model\SiteManagerInterface      $siterootManager
     * @param \Phlexible\Bundle\TreeBundle\Icon\IconResolver                  $iconResolver
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
        $repo = $this->entityManager->getRepository('PhlexibleTreeBundle:NodeMappedField');
        $qb = $repo->createQueryBuilder('n');
        $qb
            ->select('n.id', 'n.language')
            ->where($qb->expr()->like('n.backend', "%$query%"))
            ->orWhere($qb->expr()->like('n.page', "%$query%"))
            ->orWhere($qb->expr()->like('n.navigation', "%$query%"));

        $results = array();
        foreach ($qb->getQuery()->getScalarResult() as $row) {
            $nodeId = $row['id'];
            $language = $row['language'];

            $tree = $this->treeManager->getByNodeId($nodeId);
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
