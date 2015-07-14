<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Command;

use Phlexible\Bundle\ElementBundle\Proxy\ClassManagerFactory;
use Phlexible\Bundle\ElementBundle\Proxy\Distiller;
use Phlexible\Bundle\ElementBundle\Proxy\PhpClassGenerator;
use Phlexible\Bundle\ElementBundle\Proxy\PhpClassWriter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test3 command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Test3Command extends ContainerAwareCommand
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
        $elementSourceManager = $this->getContainer()->get('phlexible_element.element_source_manager');
        $elementService = $this->getContainer()->get('phlexible_element.element_service');$fieldRegistry = $this->getContainer()->get('phlexible_elementtype.field.registry');

        $generator = new PhpClassGenerator(
            new Distiller($fieldRegistry),
            new PhpClassWriter('./proxy')
        );

        $element = $elementService->findElement(67);
        $elementVersion = $elementService->findElementVersion($element, 29);

        $classManagerFactory = new ClassManagerFactory($generator, $elementSourceManager);

        $classManager = $classManagerFactory->factory();
        $news = $classManager->create($elementVersion);

        dump($news);

        return 0;
    }
}

