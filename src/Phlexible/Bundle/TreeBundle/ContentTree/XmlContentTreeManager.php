<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

/**
 * XML content tree manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlContentTreeManager implements ContentTreeManagerInterface
{
    /**
     * @var string
     */
    private $xmlDir;

    /**
     * @var XmlContentTree[]
     */
    private $trees;

    /**
     * @param string $xmlDir
     */
    public function __construct($xmlDir)
    {
        $this->xmlDir = $xmlDir;
    }

    /**
     * @return XmlContentTree[]
     */
    public function findAll()
    {
        if ($this->trees === null) {
            $xmlFiles = glob($this->xmlDir . '*.xml');

            foreach ($xmlFiles as $xmlFile) {
                $this->trees[] = new XmlContentTree($xmlFile);
            }
        }

        return $this->trees;
    }

    /**
     * @param int $treeId
     *
     * @return null|XmlContentTree
     */
    public function findByTreeId($treeId)
    {
        $trees = $this->findAll();
        if (!$trees) {
            return null;
        }

        foreach ($trees as $tree) {
            if ($tree->has($treeId)) {
                return $tree;
            }
        }

        return null;
    }
}
