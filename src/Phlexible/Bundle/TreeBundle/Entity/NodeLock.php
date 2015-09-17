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
 * Lock.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity(repositoryClass="Phlexible\Bundle\TreeBundle\Entity\Repository\NodeLockRepository")
 * @ORM\Table(name="node_lock")
 */
class NodeLock
{
    const TYPE_PERMANENTLY = 'permanently';
    const TYPE_TEMPORARY = 'temporary';

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
     * @var string
     * @ORM\Column(name="user_id", type="string", length=36, options={"fixed"=true})
     */
    private $userId;

    /**
     * @var \DateTime
     * @ORM\Column(name="locked_at", type="datetime")
     */
    private $lockedAt;

    /**
     * @var string
     * @ORM\Column(type="string", length=11, options={"default"="temporary"})
     */
    private $type;

    /**
     * @param int    $nodeId
     * @param string $userId
     * @param string $type
     */
    public function __construct($nodeId, $userId, $type)
    {
        $this->nodeId = $nodeId;
        $this->userId = $userId;
        $this->type = $type;
        $this->lockedAt = new \DateTime();
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
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return \DateTime
     */
    public function getLockedAt()
    {
        return $this->lockedAt;
    }

    /**
     * Touch lock.
     *
     * @return $this
     */
    public function touch()
    {
        $this->lockedAt = new \DateTime();

        return $this;
    }
}
