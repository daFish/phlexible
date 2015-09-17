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
 * Previewer interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface PreviewerInterface
{
    /**
     * @param TemplateInterface $template
     *
     * @return bool
     */
    public function accept(TemplateInterface $template);

    /**
     * @param TemplateInterface $template
     * @param string            $filePath
     *
     * @return array
     */
    public function create(TemplateInterface $template, $filePath);
}
