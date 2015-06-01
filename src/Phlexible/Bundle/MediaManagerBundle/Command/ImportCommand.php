<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Command;

use Phlexible\Component\Volume\FileSource\FilesystemFileSource;
use Phlexible\Component\Volume\Model\FolderInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Import command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ImportCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-manager:import')
            ->setDefinition(
                [
                    new InputArgument('source', InputArgument::REQUIRED, 'Source file'),
                    new InputArgument('dir', InputArgument::OPTIONAL, 'Target directory'),
                    new InputOption('volume', null, InputOption::VALUE_REQUIRED, 'Target volume'),
                    new InputOption('delete', null, InputOption::VALUE_NONE, 'Delete source file after import'),
                ]
            )
            ->setDescription('Import file');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $delete = $input->getOption('delete');

        $volume = $input->getOption('volume');
        $volumeManager = $this->getContainer()->get('phlexible_media_manager.volume_manager');

        if ($volume) {
            $volume = $volumeManager->getById($volume);
        } else {
            $volume = $volumeManager->get('default');
        }

        $targetDir = $input->getArgument('dir');
        if ($targetDir) {
            if (substr($targetDir, -1) != '/') {
                $targetDir .= '/';
            }

            try {
                $targetFolder = $volume->findFolderByPath($targetDir);
            } catch (\Exception $e) {
                $output->writeln('Folder "' . $targetDir . '" not found.');

                return 1;
            }
        } else {
            $targetFolder = $volume->findRootFolder();
        }

        if (is_dir($source)) {
            $this->importDir($output, $source, $targetFolder);
        } else {
            $this->importFile($output, $source, $targetFolder, $delete);
        }

        return 0;
    }

    private function importFile(OutputInterface $output, $sourceFile, FolderInterface $targetFolder, $delete = false)
    {
        try {
            $file = new File($sourceFile);
            $fileSource = new FilesystemFileSource($sourceFile, $file->getMimeType(), filesize($sourceFile));

            $targetFolder->getVolume()->createFile(
                $targetFolder,
                $fileSource,
                array(),
                '465d7b7b-5371-11e4-b400-001e677a6817'
            );

            $output->write($sourceFile . ' imported');

            if ($delete) {
                if (unlink($sourceFile)) {
                    $output->write(' and removed');
                } else {
                    $output->write(', but removing failed');
                }
            }
            $output->writeln('');
        } catch (\Exception $e) {
            $output->writeln('Could not import file:');
            $output->writeln($e->getMessage());
            $output->writeln($e->getTraceAsString());
        }

        return $output;
    }

    private function importDir(OutputInterface $output, $sourceDir, FolderInterface $targetFolder)
    {
        try {
            $baseDir = new \DirectoryIterator($sourceDir);

            foreach ($baseDir as $file) {
                if ($file->isDot()) {
                    continue;
                } elseif (!is_readable($file->getPathName())) {
                    continue;
                } elseif ($file->isDir()) {
                    $dirName = (string) $file->getFileName();
                    $pathName = (string) $file->getPathName();

                    $newFolder = $targetFolder->getVolume()->createFolder(
                        $targetFolder,
                        $dirName,
                        array(),
                        '465d7b7b-5371-11e4-b400-001e677a6817'
                    );

                    $this->importDir($output, $pathName, $newFolder);
                } elseif ($file->isFile()) {
                    $sourceFile = (string) $file->getPathName();

                    $this->importFile($output, $sourceFile, $targetFolder);
                }
            }

            $output->writeln($sourceDir . ' imported');
        } catch (\Exception $e) {
            $output->writeln('Could not import directory:');
            $output->writeln($e->getMessage());
            $output->writeln($e->getTraceAsString());
        }

        return $output;
    }

}
