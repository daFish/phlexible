<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\NodeType;

use Phlexible\Bundle\ElementBundle\Model\ElementSourceManagerInterface;
use Phlexible\Component\NodeType\Model\NodeTypeProviderInterface;

/**
 * Node type manager
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
            'full'      => 'page',
            'structure' => 'structure',
            'part'      => 'part'
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
