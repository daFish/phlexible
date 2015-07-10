<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Dump tree command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DumpTreeCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tree:dump-tree')
            ->setDescription('Dump tree.')
            ->addArgument('siterootId', InputArgument::OPTIONAL, 'Siteroot ID');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $siterootId = $input->getArgument('siterootId');

        $treeManager = $this->getContainer()->get('phlexible_tree.tree_manager');
        $siterootManager = $this->getContainer()->get('phlexible_siteroot.siteroot_manager');
        $dumper = $this->getContainer()->get('phlexible_tree.tree_dumper');

        if ($siterootId) {
            $tree = $treeManager->getBySiteRootId($siterootId);
            if (!$tree) {
                $output->writeln("<error>Siteroot for siteroot ID $siterootId not found.</error>");

                return 1;
            }
            $trees = array($tree);
        } else {
            $trees = $treeManager->getAll();
        }

        foreach ($trees as $tree) {
            $siteroot = $siterootManager->find($tree->getSiterootId());
            $content = $dumper->dump($tree, $siteroot);

            $filename = "/tmp/{$siteroot->getId()}.xml";
            file_put_contents($filename, $content);
            $output->writeln($siteroot->getTitle() . ' => ' . $filename);
        }

        return 0;
    }
}

