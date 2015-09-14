<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Folder command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FolderCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-manager:folder')
            ->setDefinition(
                array(
                    new InputArgument('id', InputArgument::REQUIRED, 'Folder ID.'),
                    new InputOption('with-attributes', null, InputOption::VALUE_NONE, 'Include attributes.'),
                )
            )
            ->setDescription('Show folder info.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $folderId = $input->getArgument('id');

        $volumeManager = $this->getContainer()->get('phlexible_media_manager.volume_manager');
        $folder = $volumeManager->getByFolderId($folderId)->findFolder($folderId);

        $data = array(
            'ID'             => $folder->getId(),
            'Path'           => $folder->getPhysicalPath(),
            'Created At'     => $folder->getCreatedAt()->format('Y-m-d H:i:s'),
            'Create User ID' => $folder->getCreateUserId(),
            'Modified At'    => $folder->getModifiedAt()->format('Y-m-d H:i:s'),
            'Modify User ID' => $folder->getModifyUserId(),
            'Name'           => $folder->getName(),
        );

        $table = new Table($output);
        foreach ($data as $key => $value) {
            $table->addRow(array($key, $value));
        }
        $table->render();

        if ($input->getOption('with-attributes')) {
            $output->writeln('');
            if ($folder->getAttributes()) {
                $output->writeln('Attributes:');
                $table = new Table($output);
                $table->setHeaders(array('Key', 'Value'));
                foreach ($folder->getAttributes() as $key => $value) {
                    $table->addRow(array($key, substr($value, 0, 60)));
                }
                $table->render();
            } else {
                $output->writeln('No attributes.');
            }
        }

        return 0;
    }
}
