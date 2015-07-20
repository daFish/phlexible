<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Dump command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DumpCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('element:dump')
            ->setDescription('Dump element structure.')
            ->addArgument('eid', InputArgument::REQUIRED, 'Element ID')
            ->addOption('version', null, InputOption::VALUE_REQUIRED, 'Element version')
            ->addOption('language', null, InputOption::VALUE_REQUIRED, 'Element language')
            ->addOption('values', null, InputOption::VALUE_NONE, 'Show values');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $elementService = $this->getContainer()->get('phlexible_element.element_service');

        $eid = $input->getArgument('eid');

        $element = $elementService->findElement($eid);

        if (!$element) {
            $output->writeln("Element $eid not found.");

            return 1;
        }

        $version = $input->getOption('version');
        if (!$version) {
            $version = $element->getLatestVersion();
        }

        $elementVersion = $elementService->findElementVersion($element, $version);

        $output->write("<fg=red>Element $eid - Version {$elementVersion->getVersion()}");
        if ($version && $version != $element->getLatestVersion()) {
            $output->write(" - Latest Version {$element->getLatestVersion()}");
        }
        $output->writeln(" - Title {$elementVersion->getBackendTitle('de')}</fg=red>");

        $output->writeln($elementVersion->getContent());

        return 0;
    }
}

