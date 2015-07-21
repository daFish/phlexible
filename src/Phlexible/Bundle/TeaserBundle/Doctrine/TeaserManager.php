<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Entity\TeaserState;
use Phlexible\Bundle\TeaserBundle\Event\PublishTeaserEvent;
use Phlexible\Bundle\TeaserBundle\Event\SetTeaserOfflineEvent;
use Phlexible\Bundle\TeaserBundle\Event\TeaserEvent;
use Phlexible\Bundle\TeaserBundle\Mediator\TeaserMediatorInterface;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
use Phlexible\Bundle\TeaserBundle\TeaserEvents;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Teaser manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TeaserManager implements TeaserManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TeaserMediatorInterface
     */
    private $mediator;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var EntityRepository
     */
    private $teaserRepository;

    /**
     * @var EntityRepository
     */
    private $teaserStateRepository;

    /**
     * @param EntityManager            $entityManager
     * @param TeaserMediatorInterface  $mediator
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        EntityManager $entityManager,
        TeaserMediatorInterface $mediator,
        EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->mediator = $mediator;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return EntityRepository
     */
    private function getTeaserRepository()
    {
        if (null === $this->teaserRepository) {
            $this->teaserRepository = $this->entityManager->getRepository('PhlexibleTeaserBundle:Teaser');
        }

        return $this->teaserRepository;
    }

    /**
     * @return EntityRepository
     */
    private function getTeaserStateRepository()
    {
        if (null === $this->teaserStateRepository) {
            $this->teaserStateRepository = $this->entityManager->getRepository('PhlexibleTeaserBundle:TeaserState');
        }

        return $this->teaserStateRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->getTeaserRepository()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getTeaserRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria)
    {
        return $this->getTeaserRepository()->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function isInstance(Teaser $teaser)
    {
        $qb = $this->getTeaserRepository()->createQueryBuilder('t');
        $qb
            ->select('COUNT(t.id)')
            ->where($qb->expr()->eq('t.type', $qb->expr()->literal($teaser->getType())))
            ->andWhere($qb->expr()->eq('t.typeId', $teaser->getTypeId()));

        return $qb->getQuery()->getSingleScalarResult() > 1;
    }

    /**
     * {@inheritdoc}
     */
    public function isInstanceMaster(Teaser $teaser)
    {
        return $teaser->getAttribute('instanceMaster', false);
    }

    /**
     * {@inheritdoc}
     */
    public function getInstances(Teaser $teaser)
    {
        return $this->getTeaserRepository()->findBy(
            array(
                'type'   => $teaser->getType(),
                'typeId' => $teaser->getTypeId(),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished(Teaser $teaser, $language)
    {
        return $this->findOneStateByTeaserAndLanguage($teaser, $language) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedLanguages(Teaser $teaser)
    {
        $language = array();
        foreach ($this->findTeaserState($teaser) as $teaserOnline) {
            $language[] = $teaserOnline->getLanguage();
        }

        return $language;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedAt(Teaser $teaser, $language)
    {
        $teaserState = $this->findOneStateByTeaserAndLanguage($teaser, $language);
        if (!$teaserState) {
            return null;
        }

        return $teaserState->getPublishedAt();
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersion(Teaser $teaser, $language)
    {
        $teaserState = $this->findOneStateByTeaserAndLanguage($teaser, $language);
        if (!$teaserState) {
            return null;
        }

        return $teaserState->getVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersions(Teaser $teaser)
    {
        $versions = array();
        foreach ($this->findTeaserState($teaser) as $teaserState) {
            $versions[$teaserState->getLanguage()] = $teaserState->getVersion();
        }

        return $versions;
    }

    /**
     * {@inheritdoc}
     */
    public function isAsync(Teaser $teaser, $language)
    {
        $teaserState = $this->findOneStateByTeaserAndLanguage($teaser, $language);
        if (!$teaserState) {
            return false;
        }

        $version = $this->mediator->getContentDocument($this, $teaser, $language)->__version();

        if ($version === $teaserState->getVersion()) {
            return false;
        }

        $publishedHash = $teaserState->getHash();
        $currentHash = '123';

        return $publishedHash === $currentHash;
    }

    /**
     * {@inheritdoc}
     */
    public function findTeaserState(Teaser $teaser)
    {
        return $this->getTeaserStateRepository()->findBy(array('teaser' => $teaser->getId()));
    }

    /**
     * {@inheritdoc}
     */
    public function findOneStateByTeaserAndLanguage(Teaser $teaser, $language)
    {
        return $this->getTeaserStateRepository()->findOneBy(array('teaser' => $teaser->getId(), 'language' => $language));
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(Teaser $teaser, $language)
    {
        return $this->mediator->getContentDocument($this, $teaser, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function getField(Teaser $teaser, $field, $language)
    {
        return $this->mediator->getField($this, $teaser, $field, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate(Teaser $teaser)
    {
        return $this->mediator->getTemplate($this, $teaser);
    }

    /**
     * {@inheritdoc}
     */
    public function createTeaser(
        NodeContext $node,
        $eid,
        $layoutareaId,
        $type,
        $typeId,
        $prevId = 0,
        array $stopIds = null,
        array $hideIds = null,
        $masterLanguage = 'en',
        $userId
    ) {
        $teaser = new Teaser();
        $teaser
            ->setNodeId($node->getId())
            ->setEid($eid)
            ->setLayoutareaId($layoutareaId)
            ->setType($type)
            ->setTypeId($typeId)
            ->setSort(0)
            ->setCreatedAt(new \DateTime())
            ->setCreateUserId($userId);

        if ($stopIds) {
            $teaser->setStopIds($stopIds);
        }

        if ($hideIds) {
            $teaser->setHideIds($hideIds);
        }

        $event = new TeaserEvent($teaser);
        if ($this->dispatcher->dispatch(TeaserEvents::BEFORE_CREATE_TEASER, $event)->isPropagationStopped()) {
            return null;
        }

        $this->entityManager->persist($teaser);
        $this->entityManager->flush($teaser);

        // @TODO: sort
        /*
            $sort = 0;
            if ($prevId) {
                $select = $db->select()
                    ->from($db->prefix . 'element_tree_teasers', 'sort + 1')
                    ->where('id = ?', $prevId);

                $sort = $db->fetchOne($select);
            }

            $db->update(
                $db->prefix . 'element_tree_teasers',
                array('sort' => ('sort + 1')),
                array('tree_id = ?' => $treeId, 'sort >= ?' => $sort)
            );
        */

        $event = new TeaserEvent($teaser);
        $this->dispatcher->dispatch(TeaserEvents::CREATE_TEASER, $event);

        return $teaser;
    }

    /**
     * {@inheritdoc}
     */
    public function createTeaserInstance(NodeContext $node, Teaser $teaser, $layoutAreaId, $userId)
    {
        $teaser = clone $teaser;
        $teaser
            ->setId(null)
            ->setNodeId($node->getId())
            ->setLayoutareaId($layoutAreaId)
            ->setCreateUserId($userId)
            ->setCreatedAt(new \DateTime);

        $event = new TeaserEvent($teaser);
        if ($this->dispatcher->dispatch(TeaserEvents::BEFORE_CREATE_TEASER_INSTANCE, $event)->isPropagationStopped()) {
            return null;
        }

        $this->entityManager->persist($teaser);
        $this->entityManager->flush($teaser);

        $event = new TeaserEvent($teaser);
        $this->dispatcher->dispatch(TeaserEvents::CREATE_TEASER_INSTANCE, $event);

        return $teaser;
    }

    /**
     * {@inheritdoc}
     */
    public function updateTeaser(Teaser $teaser, $flush = true)
    {
        $this->entityManager->persist($teaser);
        if ($flush) {
            $this->entityManager->flush($teaser);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTeaser(Teaser $teaser, $userId)
    {
        $event = new TeaserEvent($teaser);
        if ($this->dispatcher->dispatch(TeaserEvents::BEFORE_DELETE_TEASER, $event)->isPropagationStopped()) {
            return;
        }

        $this->entityManager->remove($teaser);
        $this->entityManager->flush();

        $event = new TeaserEvent($teaser);
        $this-> dispatcher->dispatch(TeaserEvents::DELETE_TEASER, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function publishTeaser(Teaser $teaser, $version, $language, $userId, $comment = null)
    {
        $event = new PublishTeaserEvent($teaser, $language, $version);
        if ($this->dispatcher->dispatch(TeaserEvents::BEFORE_PUBLISH_TEASER, $event)->isPropagationStopped()) {
            return null;
        }

        $teaserState = $this->getTeaserStateRepository()->findOneBy(array('teaser' => $teaser, 'language' => $language));
        if (!$teaserState) {
            $teaserState = new TeaserState();
            $teaserState
                ->setTeaser($teaser);
        }

        $teaserState
            ->setLanguage($language)
            ->setVersion($version)
            ->setHash($this->teaserHasher->hashTeaser($teaser, $version, $language))
            ->setPublishedAt(new \DateTime())
            ->setPublishUserId($userId);

        $this->entityManager->persist($teaserState);
        $this->entityManager->flush($teaserState);

        $event = new PublishTeaserEvent($teaser, $language, $version);
        $this->dispatcher->dispatch(TeaserEvents::PUBLISH_TEASER, $event);

        return $teaserState;
    }

    /**
     * {@inheritdoc}
     */
    public function setTeaserOffline(Teaser $teaser, $language, $userId, $comment = null)
    {
        $event = new SetTeaserOfflineEvent($teaser, $language);
        if ($this->dispatcher->dispatch(TeaserEvents::BEFORE_SET_TEASER_OFFLINE, $event)->isPropagationStopped()) {
            return null;
        }

        $teaserState = $this->getTeaserStateRepository()->findOneBy(array('teaser' => $teaser, 'language' => $language));
        if ($teaserState) {
            $this->entityManager->remove($teaserState);
            $this->entityManager->flush();
        }

        $event = new SetTeaserOfflineEvent($teaser, $language);
        $this->dispatcher->dispatch(TeaserEvents::SET_TEASER_OFFLINE, $event);
    }
}
