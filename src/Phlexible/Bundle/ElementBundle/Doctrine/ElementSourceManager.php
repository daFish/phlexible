<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\ElementBundle\ElementEvents;
use Phlexible\Bundle\ElementBundle\ElementsMessage;
use Phlexible\Bundle\ElementBundle\Entity\ElementSource;
use Phlexible\Bundle\ElementBundle\Event\ElementSourceEvent;
use Phlexible\Bundle\ElementBundle\Exception\CreateCancelledException;
use Phlexible\Bundle\ElementBundle\Exception\UpdateCancelledException;
use Phlexible\Bundle\ElementBundle\Model\ElementSourceManagerInterface;
use Phlexible\Component\Elementtype\Domain\Elementtype;
use Phlexible\Component\Elementtype\File\Parser\XmlParser;
use Phlexible\Component\Message\Message\MessagePoster;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Element source manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementSourceManager implements ElementSourceManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var XmlParser
     */
    private $parser;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var MessagePoster
     */
    private $messagePoster;

    /**
     * @var EntityRepository
     */
    private $elementSourceRepository;

    /**
     * @param EntityManager $entityManager
     * @param EventDispatcherInterface $dispatcher
     * @param MessagePoster $messagePoster
     */
    public function __construct(
        EntityManager $entityManager,
        EventDispatcherInterface $dispatcher,
        MessagePoster $messagePoster
    )
    {
        $this->entityManager = $entityManager;
        $this->parser = new XmlParser();
        $this->dispatcher = $dispatcher;
        $this->messagePoster = $messagePoster;
    }

    /**
     * @return EntityRepository
     */
    private function getElementSourceRepository()
    {
        if (null === $this->elementSourceRepository) {
            $this->elementSourceRepository = $this->entityManager->getRepository(
                'PhlexibleElementBundle:ElementSource'
            );
        }

        return $this->elementSourceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function findElementSource($elementtypeId)
    {
        return $this->getElementSourceRepository()->findOneBy(
            array('elementtypeId' => $elementtypeId),
            array('elementtypeRevision' => 'DESC')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria = array(), $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getElementSourceRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }
    /**
     * {@inheritdoc}
     */
    public function findByType($type)
    {
        return $this->getElementSourceRepository()->findBy(array('type' => $type));
    }

    /**
     * {@inheritdoc}
     */
    public function findElementtype($elementtypeId)
    {
        $elementSource = $this->findElementSource($elementtypeId);

        if (!$elementSource) {
            return null;
        }

        return $this->parser->parseString($elementSource->getXml());
    }

    /**
     * {@inheritdoc}
     */
    public function findElementtypesByType($type)
    {
        $elementtypes = array();
        foreach ($this->getElementSourceRepository()->findBy(array('type' => $type)) as $elementSource) {
            $elementtypes[] = $this->findElementtypeByElementSource($elementSource);
        }

        return $elementtypes;
    }

    /**
     * {@inheritdoc}
     */
    public function findElementtypeByElementSource(ElementSource $elementSource)
    {
        return $this->parser->parseString($elementSource->getXml());
    }

    /**
     * {@inheritdoc}
     */
    public function findOutdatedElementSources(Elementtype $elementtype)
    {
        $qb = $this->getElementSourceRepository()->createQueryBuilder('es');
        $qb
            ->where($qb->expr()->eq('es.elementtypeId', $qb->expr()->literal($elementtype->getId())))
            ->andWhere($qb->expr()->lt('es.elementtypeRevision', $elementtype->getRevision()));

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByElementtype(Elementtype $elementtype)
    {
        return $this->getElementSourceRepository()->findBy(
            array(
                'elementtypeId' => $elementtype->getId(),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByElementtypeAndRevision(Elementtype $elementtype)
    {
        return $this->getElementSourceRepository()->findOneBy(
            array(
                'elementtypeId'       => $elementtype->getId(),
                'elementtypeRevision' => $elementtype->getRevision()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function updateElementSource(ElementSource $elementSource, $flush = true)
    {
        if (!$elementSource->getId()) {
            $event = new ElementSourceEvent($elementSource);
            if ($this->dispatcher->dispatch(ElementEvents::BEFORE_CREATE_ELEMENT_SOURCE, $event)->isPropagationStopped()) {
                throw new CreateCancelledException('Create canceled by listener.');
            }

            $this->entityManager->persist($elementSource);

            if ($flush) {
                $this->entityManager->flush();
            }

            $event = new ElementSourceEvent($elementSource);
            $this->dispatcher->dispatch(ElementEvents::CREATE_ELEMENT_SOURCE, $event);

            // post message
            $message = ElementsMessage::create('Element source "' . $elementSource->getId() . ' created.');
            $this->messagePoster->post($message);
        } else {
            $event = new ElementSourceEvent($elementSource);
            if ($this->dispatcher->dispatch(ElementEvents::BEFORE_UPDATE_ELEMENT_SOURCE, $event)->isPropagationStopped()) {
                throw new UpdateCancelledException('Update canceled by listener.');
            }

            if ($flush) {
                $this->entityManager->flush();
            }

            $event = new ElementSourceEvent($elementSource);
            $this->dispatcher->dispatch(ElementEvents::UPDATE_ELEMENT_SOURCE, $event);

            // post message
            $message = ElementsMessage::create('Element source "' . $elementSource->getId() . ' updated.');
            $this->messagePoster->post($message);
        }
    }

    /**
     * @param ElementSource $elementSource
     */
    public function deleteElementSource(ElementSource $elementSource)
    {
        $this->entityManager->remove($elementSource);
        $this->entityManager->flush();
    }
}
