<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Element meta
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="element_meta")
 */
class ElementMeta
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36, options={"fixed"=true})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="set_id", type="string", length=36, options={"fixed"=true})
     */
    private $setId;

    /**
     * @var string
     * @ORM\Column(type="string", length=2, options={"fixed"=true})
     */
    private $language;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $value;

    /**
     * @var string
     * @ORM\Column(name="field_id", type="string", length=36, options={"fixed"=true})
     */
    private $fieldId;

    /**
     * @var Element
     * @ORM\ManyToOne(targetEntity="Element")
     * @ORM\JoinColumn(name="eid", referencedColumnName="eid", onDelete="CASCADE")
     */
    private $element;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $version;
}
