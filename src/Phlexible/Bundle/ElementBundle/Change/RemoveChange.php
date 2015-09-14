<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Change;

use Phlexible\Bundle\ElementBundle\Entity\ElementSource;
use Phlexible\Component\Elementtype\Domain\Elementtype;
use Phlexible\Component\Elementtype\Usage\Usage;

/**
 * Elementtype remove change
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RemoveChange extends Change
{
    /**
     * @var ElementSource[]
     */
    private $removedElementSources;

    /**
     * @param Elementtype     $elementtype
     * @param Usage[]         $usage
     * @param ElementSource[] $removedElementSources
     */
    public function __construct(Elementtype $elementtype, array $usage, array $removedElementSources)
    {
        parent::__construct($elementtype, $usage);

        $this->removedElementSources = $removedElementSources;
    }

    /**
     * @return ElementSource[]
     */
    public function getRemovedElementSources()
    {
        return $this->removedElementSources;
    }

    /**
     * {@inheritdoc}
     */
    public function getReason()
    {
        return 'Elementtype removed';
    }
}
