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
 * Delegating specifier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingSpecifier implements SpecifierInterface
{
    /**
     * @var SpecifierResolver
     */
    private $specifierResolver;

    /**
     * @param SpecifierResolver $specifierResolver
     */
    public function __construct(SpecifierResolver $specifierResolver)
    {
        $this->specifierResolver = $specifierResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(TemplateInterface $template)
    {
        if (!$this->specifierResolver->resolve($template)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension(TemplateInterface $template)
    {
        $specifier = $this->specifierResolver->resolve($template);

        if (!$specifier) {
            return null;
        }

        return $specifier->getExtension($template);
    }

    /**
     * {@inheritdoc}
     */
    public function specify(TemplateInterface $template)
    {
        $specifier = $this->specifierResolver->resolve($template);

        if (!$specifier) {
            return null;
        }

        return $specifier->specify($template);
    }
}
