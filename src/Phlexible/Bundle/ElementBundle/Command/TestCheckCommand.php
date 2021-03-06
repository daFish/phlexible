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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test check command.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TestCheckCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('element:test-check')
            ->setDescription('test.')
            ->addArgument('eid')
            ->addArgument('version', InputArgument::OPTIONAL);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $elementService = $this->getContainer()->get('phlexible_element.element_service');

        $element = $elementService->findElement($input->getArgument('eid'));
        if (!($version = $input->getArgument('version'))) {
            $version = $element->getLatestVersion();
        }
        $elementVersion = $elementService->findElementVersion($element, $version);

        $classManager = $this->getContainer()->get('phlexible_element.proxy.class_manager');

        $news = $classManager->create($elementVersion);
        dump($news);

        return 0;
    }
}
