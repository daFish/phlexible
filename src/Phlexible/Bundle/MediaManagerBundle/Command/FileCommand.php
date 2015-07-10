<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * File command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-manager:file')
            ->setDefinition(
                array(
                    new InputArgument('id', InputArgument::REQUIRED, 'File ID.'),
                    new InputOption('with-attributes', null, InputOption::VALUE_NONE, 'Include attributes.'),
                )
            )
            ->setDescription('Show file info');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileId = $input->getArgument('id');

        $volumeManager = $this->getContainer()->get('phlexible_media_manager.volume_manager');
        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId);

        $data = array(
            'ID'             => $file->getId(),
            'Folder ID'      => $file->getFolderId(),
            'Path'           => $file->getPhysicalPath(),
            'Created At'     => $file->getCreatedAt()->format('Y-m-d H:i:s'),
            'Create User ID' => $file->getCreateUserId(),
            'Modified At'    => $file->getModifiedAt()->format('Y-m-d H:i:s'),
            'Modify User ID' => $file->getModifyUserId(),
            'Hash'           => $file->getHash(),
            'MimeType'       => $file->getMimeType(),
            'Name'           => $file->getName(),
            'Size'           => $file->getSize(),
            'Version'        => $file->getVersion(),
        );

        $table = new Table($output);
        foreach ($data as $key => $value) {
            $table->addRow(array($key, $value));
        }
        $table->render();

        if ($input->getOption('with-attributes')) {
            $output->writeln('');
            if ($file->getAttributes()) {
                $output->writeln('Attributes:');
                $table = new Table($output);
                $table->setHeaders(array('Key', 'Value'));
                foreach ($file->getAttributes() as $key => $value) {
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
