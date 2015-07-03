<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\File;

use Phlexible\Component\MediaTemplate\Model\TemplateCollection;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

/**
 * Template repository interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TemplateRepositoryInterface
{
    /**
     * @return TemplateCollection
     */
    public function loadTemplates();

    /**
     * @param TemplateInterface $template
     * @param string|null       $type
     */
    public function writeTemplate(TemplateInterface $template, $type = null);
}
