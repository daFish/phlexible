<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Command;

use CG\Core\DefaultGeneratorStrategy;
use CG\Generator\PhpClass;
use Phlexible\Bundle\ElementBundle\Proxy\Distiller;
use Phlexible\Bundle\ElementBundle\Proxy\PhpClassGenerator;
use Phlexible\Bundle\ElementBundle\Proxy\PhpClassWriter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test2 command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Test2Command extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('element:test-generate')
            ->setDescription('test.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $elementSourceManager = $this->getContainer()->get('phlexible_element.element_source_manager');
        $fieldRegistry = $this->getContainer()->get('phlexible_elementtype.field.registry');

        $generator = new PhpClassGenerator(
            new Distiller($fieldRegistry),
            new PhpClassWriter('./proxy')
        );

        $elementtypes = array();
        foreach ($elementSourceManager->findBy(array()) as $elementSource) {
            $elementtypes[] = $elementSourceManager->findElementtype($elementSource->getElementtypeId());
        }

        $generator->generate($elementtypes);

        return 0;
    }
}

