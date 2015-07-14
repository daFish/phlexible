<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy;

use Phlexible\Bundle\ElementBundle\Model\ElementSourceManagerInterface;

/**
 * Class manager factory
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ClassManagerFactory
{
    /**
     * @var PhpClassGenerator
     */
    private $generator;

    /**
     * @var ElementSourceManagerInterface
     */
    private $elementSourceManager;

    /**
     * @param PhpClassGenerator             $generator
     * @param ElementSourceManagerInterface $elementSourceManager
     */
    public function __construct(PhpClassGenerator $generator, ElementSourceManagerInterface $elementSourceManager)
    {
        $this->generator = $generator;
        $this->elementSourceManager = $elementSourceManager;
    }

    /**
     *
     */
    public function factory()
    {
        if (!file_exists('./proxy/ClassManager.php')) {
            $elementtypes = array();
            foreach ($this->elementSourceManager->findBy(array()) as $elementSource) {
                $elementtypes[] = $this->elementSourceManager->findElementtype($elementSource->getElementtypeId());
            }

            $this->generator->generate($elementtypes);
        }

        $classManager = include_once './proxy/ClassManager.php';

        return $classManager;
    }
}
