<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Command;

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
        $generator = $this->getContainer()->get('phlexible_element.proxy.class_generator');

        $elementtypes = array();
        foreach ($elementSourceManager->findBy(array()) as $elementSource) {
            $elementtypes[] = $elementSourceManager->findElementtype($elementSource->getElementtypeId());
        }

        $generator->generate($elementtypes);

        return 0;
    }
}

