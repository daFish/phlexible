<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Site\File;

use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Component\Site\Domain\Site;
use Phlexible\Component\Site\Event\SiteEvent;
use Phlexible\Component\Site\Exception\CreateCancelledException;
use Phlexible\Component\Site\Exception\DeleteCancelledException;
use Phlexible\Component\Site\Exception\UpdateCancelledException;
use Phlexible\Component\Site\Model\SiteManagerInterface;
use Phlexible\Component\Site\SiteEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Elementtype manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiteManager implements SiteManagerInterface
{
    /**
     * @var SiteRepositoryInterface
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
     * @param SiteRepositoryInterface  $repository
     * @param ValidatorInterface       $validator
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        SiteRepositoryInterface $repository,
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
    public function validateSite(Site $site)
    {
        $violations = $this->validator->validate($site);
        if ($violations->count()) {
            $msg = 'Site is invalid. Violations: ';
            foreach ($violations as $violation) {
                $msg .= $violation->getPropertyPath().': '.$violation->getMessage().': '.json_encode($violation->getInvalidValue()).'';
            }
            throw new ValidatorException($msg);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateSite(Site $site)
    {
        if (!$site->getId()) {
            $event = new SiteEvent($site);
            if ($this->dispatcher->dispatch(SiteEvents::BEFORE_CREATE_SITE, $event)->isPropagationStopped()) {
                throw new CreateCancelledException('Create canceled by callback.');
            }

            $rc = new \ReflectionClass($site);
            $rp = $rc->getProperty('id');
            $rp->setAccessible(true);
            $rp->setValue($site, Uuid::generate());

            $this->validateSite($site);
            $this->repository->write($site);

            $event = new SiteEvent($site);
            $this->dispatcher->dispatch(SiteEvents::CREATE_SITE, $event);
        } else {
            $event = new SiteEvent($site);
            if ($this->dispatcher->dispatch(SiteEvents::BEFORE_UPDATE_SITE, $event)->isPropagationStopped()) {
                throw new UpdateCancelledException('Update canceled by callback.');
            }

            $this->validateSite($site);
            $this->repository->write($site);

            $event = new SiteEvent($site);
            $this->dispatcher->dispatch(SiteEvents::UPDATE_SITE, $event);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSite(Site $site)
    {
        // post before event
        $event = new SiteEvent($site);
        if ($this->dispatcher->dispatch(SiteEvents::BEFORE_DELETE_SITE, $event)->isPropagationStopped()) {
            throw new DeleteCancelledException('Delete canceled by listener.');
        }

        $this->deleteSite($site);

        // post event
        $event = new SiteEvent($site);
        $this->dispatcher->dispatch(SiteEvents::DELETE_SITE, $event);

        return $this;
    }
}
