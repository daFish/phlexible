<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SiterootBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Show command.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ShowCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('site:show')
            ->setDescription('Show site infos.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $siteManager = $this->getContainer()->get('phlexible_siteroot.siteroot_manager');

        foreach ($siteManager->findAll() as $site) {
            $output->write('<info>'.$site->getTitle('en').'</info>');
            $output->writeln(': '.$site->getId().($site->isDefault() ? '<info> (default)</info> ' : ''));

            if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                $output->writeln('  ID: '.$site->getId());
                $output->writeln('  Hostname: '.$site->getHostname());
                if ($site->getEntryPoints()) {
                    $output->writeln('  Entry Points:');
                    foreach ($site->getEntryPoints() as $entryPoint) {
                        $output->writeln('    '.$entryPoint->getHostname().' => '.$entryPoint->getNodeId().' ('.$entryPoint->getLanguage().')');
                    }
                }

                if ($site->getNavigations()) {
                    $output->writeln('  Navigations:');
                    foreach ($site->getNavigations() as $navigation) {
                        $output->writeln('    '.$navigation->getName().' => '.$navigation->getNodeId().($navigation->getMaxDepth() ? ' (maxDepth: '.$navigation->getMaxDepth().')' : ''));
                    }
                }

                if ($site->getNodeAliases()) {
                    $output->writeln('  Node Aliases:');
                    foreach ($site->getNodeAliases() as $nodeAlias) {
                        $name = $nodeAlias->getName();
                        $value = $nodeAlias->getNodeId().($nodeAlias->getLanguage() ? ' ('.$nodeAlias->getLanguage().')' : '');
                        $output->writeln("    $name => $value");
                    }
                }

                if ($site->getNodeConstraints()) {
                    $output->writeln('  Node Constraints:');
                    foreach ($site->getNodeConstraints() as $nodeConstraints) {
                        $output->writeln("    {$nodeConstraints->getName()} => {$nodeConstraints->isAllowed()}");
                    }
                }
            }
        }

        return 0;
    }
}
