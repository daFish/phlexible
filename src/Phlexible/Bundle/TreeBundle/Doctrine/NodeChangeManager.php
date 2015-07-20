<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Phlexible\Bundle\TreeBundle\Entity\NodeChange;
use Phlexible\Bundle\TreeBundle\Exception\InvalidArgumentException;
use Phlexible\Bundle\TreeBundle\Model\NodeChangeManagerInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Node change manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeChangeManager implements NodeChangeManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $nodeChangeRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return EntityRepository
     */
    private function getNodeChangeRepository()
    {
        if (null === $this->nodeChangeRepository) {
            $this->nodeChangeRepository = $this->entityManager->getRepository('PhlexibleTreeBundle:NodeChange');
        }

        return $this->nodeChangeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->getNodeChangeRepository()->createQueryBuilder('h');
        $this->applyCriteriaToQueryBuilder($criteria, $qb);

        if ($orderBy) {
            foreach ($orderBy as $field => $dir) {
                $qb->addOrderBy("h.$field", $dir);
            }
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function countBy(array $criteria)
    {
        $qb = $this->getNodeChangeRepository()->createQueryBuilder('h');
        $qb->select('COUNT(h.id)');
        $this->applyCriteriaToQueryBuilder($criteria, $qb);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array        $criteria
     * @param QueryBuilder $qb
     *
     * @throws InvalidArgumentException
     */
    private function applyCriteriaToQueryBuilder(array $criteria, QueryBuilder $qb)
    {
        foreach ($criteria as $key => $value) {
            if (in_array($key, array('nodeId', 'version'))) {
                $qb->andWhere($qb->expr()->eq("h.$key", $value));
            } elseif (in_array($key, array('language', 'action'))) {
                $qb->andWhere($qb->expr()->eq("h.$key", $qb->expr()->literal($value)));
            } elseif (in_array($key, array('comment'))) {
                $qb->andWhere($qb->expr()->like("h.$key", $qb->expr()->literal("%$value%")));
            } else {
                throw new InvalidArgumentException("Unkown field $key");
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function insert($action, NodeContext $node, $userId, $version = null, $language = null, $comment = null)
    {
        $entry = new NodeChange(
            $node->getId(),
            $node->getContentType(),
            $node->getContentId(),
            $language,
            $version,
            $action,
            $comment,
            $userId,
            new \DateTime()
        );

        $this->entityManager->persist($entry);
        $this->entityManager->flush($entry);

        return $this;
    }
}
