<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * List command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-manager:ls')
            ->setDefinition(
                [
                    new InputArgument('id', InputArgument::OPTIONAL, 'Folder ID.'),
                ]
            )
            ->setDescription('Show folder contents.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $volumeManager = $this->getContainer()->get('phlexible_media_manager.volume_manager');

        $folderId = $input->getArgument('id');

        if ($folderId) {
            $volume = $volumeManager->getByFolderId($folderId);
            $folder = $volume->findFolder($folderId);

            $output->writeln('default:/' . $folder->getPath() . ' (' . $folder->getId() . ')');

            foreach ($volume->findFoldersByParentFolder($folder) as $subFolder) {
                $output->writeln('+  ' . $subFolder->getName() . ' (' . $subFolder->getId() . ')');
            }

            foreach ($volume->findFilesByFolder($folder, array('name' => 'ASC')) as $file) {
                $output->writeln('-  ' . $file->getName() . ' (' . $file->getId() . ')');
            }
        } else {
            foreach ($volumeManager->all() as $volumeName => $volume) {
                $folder = $volume->findRootFolder();
                $output->writeln($volumeName . ':/ (' . $folder->getId() . ')');
            }
        }

        return 0;
    }
}
