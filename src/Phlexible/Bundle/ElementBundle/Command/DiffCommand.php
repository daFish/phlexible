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

use Phlexible\Bundle\ElementBundle\ElementVersion\Diff\Differ;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Diff command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DiffCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('element:diff')
            ->setDescription('Diff element versions.')
            ->addArgument('eid', InputArgument::REQUIRED, 'Element ID')
            ->addOption('baseVersion', null, InputOption::VALUE_REQUIRED, 'Base version')
            ->addOption('compareVersion', null, InputOption::VALUE_REQUIRED, 'Compare version');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $elementService = $this->getContainer()->get('phlexible_element.element_service');

        $eid = $input->getArgument('eid');
        $version = $input->getOption('baseVersion');
        $compareVersion = $input->getOption('compareVersion');

        $element = $elementService->findElement($eid);

        if (!$version) {
            $version = $element->getLatestVersion();
        }
        if (!$compareVersion) {
            $compareVersion = $version - 1;
        }

        $fromElementVersion = $elementService->findElementVersion($element, $version);
        $toElementVersion = $elementService->findElementVersion($element, $compareVersion);

        $differ = new Differ();
        $diff = $differ->diff($fromElementVersion->getContent(), $toElementVersion->getContent());

        $output->writeln("Diffing EID $eid from version $version to version $compareVersion.");
        dump($diff);
        //$output->writeln($result);

        return 0;
    }
}

