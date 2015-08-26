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
 * Node history
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="node_change")
 */
class NodeChange
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
     * @ORM\Column(name="node_id", type="integer", nullable=true)
     */
    private $nodeId;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var int
     * @ORM\Column(name="type_id", type="integer", nullable=true)
     */
    private $typeId;

    /**
     * @var string
     * @ORM\Column(type="string", length=2, nullable=true, options={"fixed"=true})
     */
    private $language;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $version;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $action;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @var string
     * @ORM\Column(name="create_user_id", type="string", length=36, options={"fixed"=true})
     */
    private $createUserId;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @param int    $nodeId
     * @param string $type
     * @param int    $typeId
     * @param string $language
     * @param int    $version
     * @param string $action
     * @param string $comment
     * @param string $createUserId
     */
    public function __construct($nodeId, $type, $typeId, $language, $version, $action, $comment, $createUserId)
    {
        $this->nodeId = $nodeId;
        $this->type = $type;
        $this->typeId = $typeId;
        $this->language = $language;
        $this->version = $version;
        $this->action = $action;
        $this->comment = $comment;
        $this->createUserId = $createUserId;
        $this->createdAt = new \DateTime();
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
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
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return string
     */
    public function getCreateUserId()
    {
        return $this->createUserId;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
