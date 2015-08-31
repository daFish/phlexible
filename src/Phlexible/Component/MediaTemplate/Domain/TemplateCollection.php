<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\Domain;

use JMS\Serializer\Annotation as Serializer;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

/**
 * Template collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Serializer\XmlRoot(name="mediaTemplates")
 * @Serializer\ExclusionPolicy("all")
 */
class TemplateCollection
{
    /**
     * @var TemplateInterface[]
     * @Serializer\Expose()
     * @Serializer\XmlList(inline=true, entry="mediaTemplate")
     */
    public $mediaTemplates;

    /**
     * @var TemplateInterface[]
     * @Serializer\Expose()
     * @Serializer\Type("integer")
     * @Serializer\XmlAttribute()
     */
    public $total;

    /**
     * @param TemplateInterface[] $mediaTemplates
     * @param int                 $total
     */
    public function __construct(array $mediaTemplates, $total)
    {
        $this->mediaTemplates = array_values($mediaTemplates);
        $this->total = $total;
    }
}
