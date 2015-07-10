<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Dump routes command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DumpRoutesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tree:dump-routes')
            ->setDescription('Dump routes.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dumper = $this->getContainer()->get('phlexible_tree.routes_dumper');
        $content = $dumper->dump();

        $output->writeln($content);

        return 0;
    }
}

