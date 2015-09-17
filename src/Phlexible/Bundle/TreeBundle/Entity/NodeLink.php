<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Node link.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="node_link")
 */
class NodeLink
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="node_id", type="integer")
     */
    private $nodeId;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @var string
     * @ORM\Column(type="string", length=2, options={"fixed"=true})
     */
    private $language;

    /**
     * @var string
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", length=50)
     */
    private $field;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $target;

    /**
     * @param int    $nodeId
     * @param int    $version
     * @param string $language
     * @param string $type
     * @param string $field
     * @param string $target
     */
    public function __construct($nodeId, $version, $language, $type, $field, $target)
    {
        $this->nodeId = $nodeId;
        $this->version = $version;
        $this->language = $language;
        $this->type = $type;
        $this->field = $field;
        $this->target = $target;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }
}
