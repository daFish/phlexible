<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
