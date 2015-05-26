<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaCache\Worker;

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
        foreach ($specifiers as $worker) {
            $this->addSpecifier($worker);
        }

        $this->addSpecifier(new AudioSpecifier());
        $this->addSpecifier(new ImageSpecifier());
        $this->addSpecifier(new VideoSpecifier());
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
     * @param TemplateInterface     $template
     * @param ExtendedFileInterface $file
     * @param MediaType             $mediaType
     *
     * @return WorkerInterface
     */
    public function resolve(TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType)
    {
        foreach ($this->specifiers as $specifier) {
            if ($specifier->accept($template, $file, $mediaType)) {
                return $specifier;
            }
        }

        return null;
    }
}
