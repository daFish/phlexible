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
use Phlexible\Component\Node\Domain\Node;

/**
 * Root node
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 */
class RootNode extends Node
{
    /**
     * @var bool
     * @ORM\Column(name="configuration", type="json_array")
     */
    private $configuration;

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }
}
