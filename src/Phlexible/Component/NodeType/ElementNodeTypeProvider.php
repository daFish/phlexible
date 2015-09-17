<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\NodeType;

use Phlexible\Bundle\ElementBundle\Model\ElementSourceManagerInterface;
use Phlexible\Component\NodeType\Model\NodeTypeProviderInterface;

/**
 * Node type manager.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementNodeTypeProvider implements NodeTypeProviderInterface
{
    /**
     * @var ElementSourceManagerInterface
     */
    private $elementSourceManager;

    /**
     * @param ElementSourceManagerInterface $elementSourceManager
     */
    public function __construct(
        ElementSourceManagerInterface $elementSourceManager
    ) {
        $this->elementSourceManager = $elementSourceManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        $typeMap = array(
            'full' => 'page',
            'structure' => 'structure',
            'part' => 'part',
        );

        $typeNames = array();
        foreach ($this->elementSourceManager->findBy(array(), array('name' => 'ASC')) as $elementSource) {
            if (isset($typeMap[$elementSource->getType()])) {
                $typeNames[$elementSource->getName()] = $typeMap[$elementSource->getType()];
            }
        }

        return $typeNames;
    }
}
