<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Elementtype\Usage;

/**
 * Usage.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Usage
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $as;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var int
     */
    private $latestVersion;

    /**
     * @param string $type
     * @param string $as
     * @param int    $id
     * @param string $title
     * @param int    $latestVersion
     */
    public function __construct($type, $as, $id, $title, $latestVersion)
    {
        $this->type = $type;
        $this->as = $as;
        $this->id = $id;
        $this->title = $title;
        $this->latestVersion = $latestVersion;
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
    public function getAs()
    {
        return $this->as;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getLatestVersion()
    {
        return $this->latestVersion;
    }
}
