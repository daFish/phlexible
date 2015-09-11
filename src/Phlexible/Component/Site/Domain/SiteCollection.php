<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Site\Domain;

use JMS\Serializer\Annotation as Serializer;

/**
 * Site
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Serializer\XmlRoot(name="sites")
 * @Serializer\ExclusionPolicy("all")
 */
class SiteCollection
{
    /**
     * @var Site[]
     * @Serializer\Expose()
     * @Serializer\Type(name="array<Phlexible\Component\Site\Domain\Site>")
     * @Serializer\XmlList(inline=true, entry="site")
     */
    public $sites;

    /**
     * @var int
     * @Serializer\Expose()
     * @Serializer\Type(name="integer")
     * @Serializer\XmlAttribute()
     */
    public $total;

    /**
     * @param array $sites
     * @param int   $total
     */
    public function __construct($sites, $total)
    {
        $this->sites = $sites;
        $this->total = $total;
    }
}
