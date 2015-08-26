<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Command;

use Phlexible\Component\Site\File\Dumper\XmlDumper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Show command
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
            ->setName('siteroot:show')
            ->setDescription('Show siteroot infos.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $siterootManager = $this->getContainer()->get('phlexible_siteroot.siteroot_manager');

        foreach ($siterootManager->findAll() as $site) {
            $output->write('<info>' . $site->getTitle('en') . '</info>');
            $output->writeln(': ' . $site->getId() . ($site->isDefault() ? '<info> (default)</info> ' : ''));

            if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                $output->writeln('  ID: ' . $site->getId());
                $output->writeln('  Hostname: ' . $site->getHostname());
                if ($site->getEntryPoints()) {
                    $output->writeln('  Entry Points:');
                    foreach ($site->getEntryPoints() as $entryPoint) {
                        $output->writeln('    ' . $entryPoint['hostname'] . ' => ' . $entryPoint['nodeId'] . ' (' . $entryPoint['language'] . ')');
                    }
                }

                if ($site->getNavigations()) {
                    $output->writeln('  Navigations:');
                    foreach ($site->getNavigations() as $name => $navigation) {
                        $output->writeln('    ' . $name . ' => ' . $navigation['nodeId']);
                    }
                }

                if ($site->getSpecialTids()) {
                    $output->writeln('  Special TIDs:');
                    foreach ($site->getSpecialTids() as $specialTid) {
                        $name = $specialTid['name'];
                        $value = ($specialTid['language'] ? $specialTid['language'] . ':' : '') . $specialTid['nodeId'];
                        $output->writeln("    $name => $value");
                    }
                }
            }
        }

        return 0;
    }

}

