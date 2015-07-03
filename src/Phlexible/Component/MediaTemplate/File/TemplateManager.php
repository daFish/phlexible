<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\File;

use Phlexible\Component\MediaTemplate\Exception\NotFoundException;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateManagerInterface;

/**
 * Media template manager
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
     * Find template
     *
     * @param string $key
     *
     * @return TemplateInterface
     * @throws NotFoundException
     */
    public function find($key)
    {
        $template = $this->repository->load($key);

        if ($template !== null) {
            return $template;
        }

        throw new NotFoundException("Media template $key not found.");
    }

    /**
     * @param array $criteria
     *
     * @return TemplateInterface[]
     */
    public function findBy(array $criteria)
    {
        $found = [];
        foreach ($this->repository->loadAll() as $template) {
            foreach ($criteria as $criterium => $value) {
                $method = 'get' . ucfirst(strtolower($criterium));
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
     * Return all templates
     *
     * @return TemplateInterface[]
     */
    public function findAll()
    {
        return $this->repository->loadAll()->all();
    }

    /**
     * Update template
     *
     * @param TemplateInterface $template
     */
    public function updateTemplate(TemplateInterface $template)
    {
        $template->setRevision($template->getRevision() + 1);

        $this->repository->writeTemplate($template, 'xml');
    }
}
