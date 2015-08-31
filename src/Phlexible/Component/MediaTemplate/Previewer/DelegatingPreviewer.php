<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\Previewer;

use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

/**
 * Delegating previewer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingPreviewer implements PreviewerInterface
{
    /**
     * @var PreviewerInterface[]
     */
    private $previewers;

    /**
     * @param PreviewerInterface[] $previewers
     */
    public function __construct(array $previewers = array())
    {
        $this->previewers = $previewers;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(TemplateInterface $template)
    {
        foreach ($this->previewers as $previewer) {
            if ($previewer->accept($template)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function create(TemplateInterface $template, $filePath)
    {
        foreach ($this->previewers as $previewer) {
            if ($previewer->accept($template)) {
                return $previewer->create($template, $filePath);
            }
        }

        return null;
    }
}
