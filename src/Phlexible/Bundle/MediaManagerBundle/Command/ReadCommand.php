<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Command;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaManager\Volume\ExtendedFolderInterface;
use Phlexible\Component\MediaManager\Volume\ExtendedVolumeInterface;
use Phlexible\Component\Volume\Model\FolderIterator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Read command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ReadCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-manager:read')
            ->addOption('volume', null, InputOption::VALUE_REQUIRED, 'Volume ID')
            ->addOption('folder', null, InputOption::VALUE_REQUIRED, 'Folder ID')
            ->addOption('file', null, InputOption::VALUE_REQUIRED, 'File ID')
            ->setDescription('Re-read all metadata');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $volumeId = $input->getOption('volume');
        $folderId = $input->getOption('folder');
        $fileId = $input->getOption('file');

        $volumeManager = $this->getContainer()->get('phlexible_media_manager.volume_manager');

        if ($volumeId) {
            $volume = $volumeManager->get($volumeId);

            $this->readVolume($output, $volume);
        } elseif ($folderId) {
            $volume = $volumeManager->getByFolderId($folderId);
            $folder = $volume->findFolder($folderId);

            $this->readVolume($output, $volume, $folder);
        } elseif ($fileId) {
            $volume = $volumeManager->getByFileId($fileId);
            $file = $volume->findFile($fileId);

            $this->readFile($output, $volume, $file);
        } else {
            foreach ($volumeManager->all() as $volume) {
                $this->readVolume($output, $volume);
            }
        }

        return 0;
    }

    private function readVolume(OutputInterface $output, ExtendedVolumeInterface $volume, ExtendedFolderInterface $folder = null)
    {
        $target = $volume;
        if (!$folder) {
            $folder = $volume->findRootFolder();
            $target = $folder;
        }

        $rii = new \RecursiveIteratorIterator(new FolderIterator($target), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii as $folder) {
            $output->writeln('+ <fg=green>' . $folder->getName() . '</fg=green>');
            foreach ($volume->findFilesByFolder($folder) as $file) {
                $this->readFile($output, $volume, $file);
            }
        }
    }

    private function readFile(OutputInterface $output, ExtendedVolumeInterface $volume, ExtendedFileInterface $file)
    {
        $output->writeln('  + <fg=green>' . $file->getName() . '</fg=green> ' . $file->getId() . ' ' . $file->getPhysicalPath());
        $output->write('    > ');

        $mediaClassifier = $this->getContainer()->get('phlexible_media.media_classifier');
        $attributeReader = $this->getContainer()->get('phlexible_media.meta_reader');

        if (!file_exists($file->getPhysicalPath())) {
            $output->writeln("<error>File not found</error>");

            return;
        }

        $xfile = new File($file->getPhysicalPath());
        $mimetype = $xfile->getMimeType();
        $output->write('<fg=yellow>' . $mimetype . "</fg=yellow>, ");

        if (!$mimetype) {
            $mimetype = 'application/octet-stream';
        }

        $mediaType = $mediaClassifier->getCollection()->lookup($mimetype);
        if ($mediaType) {
            $output->write((string) $mediaType . ', ');

            if ($attributeReader->supports($file->getPhysicalPath())) {
                $valueBag = $attributeReader->read($file->getPhysicalPath());
                $attributes = $valueBag->toArray();
                $volume->setFileAttributes($file, $attributes, null);

                $output->write(count($attributes) . " attributes.");
            }

        } else {
            $output->write("<error>No media type found</error> ");
        }

        $volume->setFileMediaType($file, (string) $mediaType, null);
        $volume->setFileMimetype($file, $mimetype, null);

        $output->writeln('');
    }
}
