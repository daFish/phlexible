<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\File;

use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateManagerInterface;

/**
 * Media template manager.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TemplateManager implements TemplateManagerInterface
{
    /**
     * @var TemplateRepositoryInterface
     */
    private $repository;

    /**
     * @param TemplateRepositoryInterface $repository
     */
    public function __construct(TemplateRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find template.
     *
     * @param string $key
     *
     * @return TemplateInterface
     */
    public function find($key)
    {
        $template = $this->repository->load($key);

        if ($template !== null) {
            return $template;
        }

        return null;
    }

    /**
     * @param array $criteria
     *
     * @return TemplateInterface[]
     */
    public function findBy(array $criteria)
    {
        $found = array();
        foreach ($this->repository->loadAll() as $template) {
            foreach ($criteria as $criterium => $value) {
                $method = 'get'.ucfirst(strtolower($criterium));
                if (!method_exists($template, $method)) {
                    continue;
                }
                if ($template->$method() !== $value) {
                    continue 2;
                }
            }

            $found[] = $template;
        }

        return $found;
    }

    /**
     * Return all templates.
     *
     * @return TemplateInterface[]
     */
    public function findAll()
    {
        return $this->repository->loadAll();
    }

    /**
     * Update template.
     *
     * @param TemplateInterface $template
     */
    public function updateTemplate(TemplateInterface $template)
    {
        $template->setRevision($template->getRevision() + 1);

        $this->repository->writeTemplate($template, 'xml');
    }
}
