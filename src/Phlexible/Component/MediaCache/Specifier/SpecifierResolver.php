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

/**
 * Specifier resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SpecifierResolver
{
    /**
     * @var SpecifierInterface[]
     */
    private $specifiers = array();

    /**
     * @param SpecifierInterface[] $specifiers
     */
    public function __construct(array $specifiers = array())
    {
        foreach ($specifiers as $specifier) {
            $this->addSpecifier($specifier);
        }
    }

    /**
     * @param SpecifierInterface $specifier
     *
     * @return $this
     */
    public function addSpecifier(SpecifierInterface $specifier)
    {
        $this->specifiers[] = $specifier;

        return $this;
    }

    /**
     * Determine and return worker
     *
     * @param TemplateInterface $template
     *
     * @return SpecifierInterface
     */
    public function resolve(TemplateInterface $template)
    {
        foreach ($this->specifiers as $specifier) {
            if ($specifier->accept($template)) {
                return $specifier;
            }
        }

        return null;
    }
}
