<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy;

use Phlexible\Bundle\ElementBundle\Model\ElementSourceManagerInterface;
use Phlexible\Bundle\ElementBundle\Proxy\Generator\ProxyGenerator;

/**
 * Class manager factory
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ClassManagerFactory
{
    /**
     * @var ProxyGenerator
     */
    private $generator;

    /**
     * @var ElementSourceManagerInterface
     */
    private $elementSourceManager;

    /**
     * @param ProxyGenerator             $generator
     * @param ElementSourceManagerInterface $elementSourceManager
     */
    public function __construct(ProxyGenerator $generator, ElementSourceManagerInterface $elementSourceManager)
    {
        $this->generator = $generator;
        $this->elementSourceManager = $elementSourceManager;
    }

    /**
     * @return ClassManager
     */
    public function factory()
    {
        $classManagerFile = $this->generator->getManagerFile();

        if (!file_exists($classManagerFile)) {
            $elementtypes = array();
            foreach ($this->elementSourceManager->findBy(array()) as $elementSource) {
                $elementtypes[] = $this->elementSourceManager->findElementtype($elementSource->getElementtypeId());
            }

            $classManagerFile = $this->generator->generate($elementtypes);
        }

        $classManager = include $classManagerFile;

        return $classManager;
    }
}
