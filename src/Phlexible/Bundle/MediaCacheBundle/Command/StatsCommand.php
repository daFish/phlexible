<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaCacheBundle\Command;

use Phlexible\Component\MediaCache\Domain\CacheItem;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Stats command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class StatsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-cache:stats')
            ->setDescription('Show media cache statistics')
            ->addOption('waiting', null, InputOption::VALUE_NONE, 'Show waiting items.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $cacheManager = $container->get('phlexible_media_cache.cache_manager');

        $cntCache = $cacheManager->countAll();
        $cntWaiting = $cacheManager->countBy(array('queueStatus' => CacheItem::QUEUE_WAITING));
        $cntMissing = $cacheManager->countBy(array('cacheStatus' => CacheItem::STATUS_MISSING));
        $cntError = $cacheManager->countBy(array('cacheStatus' => CacheItem::STATUS_ERROR));
        $cntOk = $cacheManager->countBy(array('cacheStatus' => CacheItem::STATUS_OK));
        $cntDelegate = $cacheManager->countBy(array('cacheStatus' => CacheItem::STATUS_DELEGATE));
        $cntInapplicable = $cacheManager->countBy(array('cacheStatus' => CacheItem::STATUS_INAPPLICABLE));

        $output->writeln($cntCache . ' cached items.');
        $output->writeln($cntWaiting . ' waiting items.');
        $output->writeln('');
        $output->writeln("<info>OK:          $cntOk</info>");
        $output->writeln("Delegate:    $cntDelegate");
        $output->writeln("<fg=yellow>Inapplicable $cntInapplicable</fg=yellow>");
        $output->writeln("<fg=red>Missing      $cntMissing</fg=red>");
        $output->writeln("<error>Error        $cntError</error>");

        if ($input->getOption('waiting')) {
            $output->writeln('');
            $this->outputItems($output, $cacheManager->findBy(array('queueStatus' => CacheItem::QUEUE_WAITING)));
        }

        return 0;
    }

    private function outputItems(OutputInterface $output, array $items)
    {
        $table = new Table($output);
        $table->setHeaders(
            array(
                'ID',
                'File ID',
                'V',
                'Template',
                'Rev',
                'Cache Status',
                'Queue Status',
                'Queued',
            )
        );
        foreach ($items as $item) {
            $table->addRow(
                array(
                    $item->getId(),
                    $item->getFileId(),
                    $item->getFileVersion(),
                    $item->getTemplateKey(),
                    $item->getTemplateRevision(),
                    $item->getCacheStatus(),
                    $item->getQueueStatus(),
                    $item->getQueuedAt()->format('Y-m-d H:i:s'),
                )
            );
        }
        $table->render();
    }
}
