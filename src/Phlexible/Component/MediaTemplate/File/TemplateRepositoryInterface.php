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

/**
 * Template repository interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TemplateRepositoryInterface
{
    /**
     * @return array
     */
    public function loadAll();

    /**
     * @param string $key
     *
     * @return TemplateInterface|null
     */
    public function load($key);

    /**
     * @param TemplateInterface $template
     * @param string|null       $type
     */
    public function writeTemplate(TemplateInterface $template, $type = null);
}
