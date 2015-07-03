<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\File;

use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Component\Elementtype\ElementtypeEvents;
use Phlexible\Component\Elementtype\Event\ElementtypeEvent;
use Phlexible\Component\Elementtype\Exception\CreateCancelledException;
use Phlexible\Component\Elementtype\Exception\DeleteCancelledException;
use Phlexible\Component\Elementtype\Exception\UpdateCancelledException;
use Phlexible\Component\Elementtype\Model\Elementtype;
use Phlexible\Component\Elementtype\Model\ElementtypeManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Elementtype manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeManager implements ElementtypeManagerInterface
{
    /**
     * @var ElementtypeRepositoryInterface
     */
    private $repository;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param ElementtypeRepositoryInterface $repository
     * @param ValidatorInterface             $validator
     * @param EventDispatcherInterface       $dispatcher
     */
    public function __construct(
        ElementtypeRepositoryInterface $repository,
        ValidatorInterface $validator,
        EventDispatcherInterface $dispatcher)
    {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function find($elementtypeId)
    {
        return $this->repository->load($elementtypeId);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->repository->loadAll();
    }

    /**
     * {@inheritdoc}
     */
    public function validateElementtype(Elementtype $elementtype)
    {
        $violations = $this->validator->validate($elementtype);
        if ($violations->count()) {
            $msg = 'Elementtype is invalid. Violations: ';
            foreach ($violations as $violation) {
                $msg .= $violation->getPropertyPath().': '.$violation->getMessage().': '.json_encode($violation->getInvalidValue()).'';
            }
            throw new ValidatorException($msg);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateElementtype(Elementtype $elementtype)
    {
        if (!$elementtype->getId()) {
            $event = new ElementtypeEvent($elementtype);
            if ($this->dispatcher->dispatch(ElementtypeEvents::BEFORE_CREATE, $event)->isPropagationStopped()) {
                throw new CreateCancelledException('Create canceled by callback.');
            }

            $elementtype->setId(Uuid::generate());

            $this->validateElementtype($elementtype);
            $this->repository->write($elementtype);

            $event = new ElementtypeEvent($elementtype);
            $this->dispatcher->dispatch(ElementtypeEvents::CREATE, $event);
        } else {
            $event = new ElementtypeEvent($elementtype);
            if ($this->dispatcher->dispatch(ElementtypeEvents::BEFORE_UPDATE, $event)->isPropagationStopped()) {
                throw new UpdateCancelledException('Update canceled by callback.');
            }

            $this->validateElementtype($elementtype);
            $this->repository->write($elementtype);

            $event = new ElementtypeEvent($elementtype);
            $this->dispatcher->dispatch(ElementtypeEvents::UPDATE, $event);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteElementtype(Elementtype $elementtype)
    {
        // post before event
        $event = new ElementtypeEvent($elementtype);
        if ($this->dispatcher->dispatch(ElementtypeEvents::BEFORE_DELETE, $event)->isPropagationStopped()) {
            throw new DeleteCancelledException('Delete canceled by listener.');
        }

        $elementtype->setDeleted(true);
        $this->updateElementtype($elementtype);

        // post event
        $event = new ElementtypeEvent($elementtype);
        $this->dispatcher->dispatch(ElementtypeEvents::DELETE, $event);

        return $this;
    }
}
