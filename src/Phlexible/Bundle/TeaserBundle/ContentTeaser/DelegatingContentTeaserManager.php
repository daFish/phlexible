<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\ContentTeaser;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Mediator\DelegatingTeaserMediator;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\NodeInterface;

// TODO: interface

/**
 * Teaser manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingContentTeaserManager
{
    /**
     * @var TeaserManagerInterface
     */
    private $teaserManager;

    /**
     * @var DelegatingTeaserMediator
     */
    private $mediator;

    /**
     * @param TeaserManagerInterface $teaserManager
     * @param DelegatingTeaserMediator               $mediator
     */
    public function __construct(TeaserManagerInterface $teaserManager, DelegatingTeaserMediator $mediator)
    {
        $this->teaserManager = $teaserManager;
        $this->mediator = $mediator;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->createContentTeaserFromTeaser($this->teaserManager->find($id));
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->teaserManager->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria)
    {
        return $this->createContentTeaserFromTeaser($this->teaserManager->findOneBy($criteria));
    }

    /**
     * {@inheritdoc}
     */
    public function findForLayoutAreaAndTreeNodePath($layoutarea, array $treeNodePath, $includeLocalHidden = true)
    {
        return $this->createContentTeasersFromTeasers($this->teaserManager->findCascadingForLayoutAreaAndNode($layoutarea, $treeNodePath, $includeLocalHidden));
    }

    /**
     * {@inheritdoc}
     */
    public function findForLayoutAreaAndTreeNode($layoutarea, NodeInterface $treeNode)
    {
        return $this->createContentTeasersFromTeasers($this->teaserManager->findForLayoutAreaAndNodeContext($layoutarea, $treeNode));
    }

    /**
     * {@inheritdoc}
     */
    public function isInstance(ContentTeaser $teaser)
    {
        return $this->teaserManager->isInstance($teaser);
    }

    /**
     * {@inheritdoc}
     */
    public function isInstanceMaster(ContentTeaser $teaser)
    {
        return $this->teaserManager->isInstanceMaster($teaser);
    }

    /**
     * {@inheritdoc}
     */
    public function getInstances(ContentTeaser $teaser)
    {
        return $this->teaserManager->getInstances($teaser);
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished(ContentTeaser $teaser, $language)
    {
        return $this->teaserManager->isPublished($teaser, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedLanguages(ContentTeaser $teaser)
    {
        return $this->teaserManager->getPublishedLanguages($teaser);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersion(ContentTeaser $teaser, $language)
    {
        return $this->teaserManager->getPublishedVersion($teaser, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersions(ContentTeaser $teaser)
    {
        return $this->teaserManager->getPublishedVersions($teaser);
    }

    /**
     * {@inheritdoc}
     */
    public function isAsync(Teaser $teaser, $language)
    {
        return $this->teaserManager->isAsync($teaser, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function findOnlineByTeaser(ContentTeaser $teaser)
    {
        return $this->teaserManager->findTeaserState($teaser);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneOnlineByTeaserAndLanguage(ContentTeaser $teaser, $language)
    {
        return $this->teaserManager->findOneStateByTeaserAndLanguage($teaser, $language);
    }

    /**
     * @param Teaser[] $teasers
     *
     * @return ContentTeaser[]
     */
    public function createContentTeasersFromTeasers(array $teasers)
    {
        $contentTeasers = array();
        foreach ($teasers as $teaser) {
            $contentTeasers[] = $this->createContentTeaserFromTeaser($teaser);
        }

        return $contentTeasers;
    }

    /**
     * @param Teaser $teaser
     *
     * @return ContentTeaser
     */
    public function createContentTeaserFromTeaser(Teaser $teaser)
    {
        $contentTeaser = new ContentTeaser();
        $contentTeaser
            ->setId($teaser->getId())
            ->setLayoutareaId($teaser->getLayoutareaId())
            ->setNodeId($teaser->getNodeId())
            ->setEid($teaser->getEid())
            ->setTypeId($teaser->getTypeId())
            ->setType($teaser->getType())
            ->setSort($teaser->getSort())
            ->setCache($teaser->getCache())
            ->setAttributes($teaser->getAttributes())
            ->setCreatedAt($teaser->getCreatedAt())
            ->setCreateUserId($teaser->getCreateUserId());

        $language = 'de';
        $contentTeaser->setTitle($this->mediator->getTitle($teaser, 'navigation', $language));
        $contentTeaser->setUniqueId($this->mediator->getUniqueId($teaser));

        return $contentTeaser;
    }

}
