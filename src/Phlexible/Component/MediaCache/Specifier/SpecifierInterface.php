<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\Specifier;

use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Temp\MediaConverter\Format\Specification;

/**
 * Specifier interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface SpecifierInterface
{
    /**
     * Are the given template and asset supported?
     *
     * @param TemplateInterface $template
     *
     * @return bool
     */
    public function accept(TemplateInterface $template);

    /**
     * Determine extension from template.
     *
     * @param TemplateInterface $template
     *
     * @return string
     */
    public function getExtension(TemplateInterface $template);

    /**
     * @param TemplateInterface $template
     *
     * @return Specification
     */
    public function specify(TemplateInterface $template);
}
