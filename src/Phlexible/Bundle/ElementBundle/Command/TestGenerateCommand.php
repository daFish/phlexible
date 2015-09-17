<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test generate command.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TestGenerateCommand extends ContainerAwareCommand
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
