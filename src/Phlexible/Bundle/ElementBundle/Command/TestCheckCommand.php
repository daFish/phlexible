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
 * Test check command
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
            ->setDescription('test.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $elementService = $this->getContainer()->get('phlexible_element.element_service');

        $element = $elementService->findElement(288);
        $elementVersion = $elementService->findElementVersion($element, 2);

        $classManager = $this->getContainer()->get('phlexible_element.proxy.class_manager');

        $news = $classManager->create($elementVersion);
        dump($news);die;

        $x = $news->getMain()->first();
        dump($news->__toArray());

        return 0;
    }
}

