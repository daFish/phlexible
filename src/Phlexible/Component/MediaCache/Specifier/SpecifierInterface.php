<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaCache\Specifier;

use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Temp\MediaConverter\Format\Specification;

/**
 * Specifier interface
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
     * Determine extension from template
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
