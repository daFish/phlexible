<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaCache\Specifier;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaType\Model\MediaType;

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
    private $specifiers = [];

    /**
     * @param SpecifierInterface[] $specifiers
     */
    public function __construct(array $specifiers = [])
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
