<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Command;

use Phlexible\Component\Message\Domain\Message;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Tail command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TailCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('message:tail')
            ->setDescription('Show latest messages')
            ->setDefinition(
                array(
                    new InputOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Show latest <number> messages.', 20),
                    new InputOption('follow', 'f', InputOption::VALUE_NONE, 'Follow output'),
                    new InputOption('body', 'b', InputOption::VALUE_NONE, 'Show body'),
                    new InputOption('sleep', null, InputOption::VALUE_REQUIRED, 'Sleep time', 5),
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = $input->getOption('limit');
        $follow = $input->getOption('follow');
        $showBody = $input->getOption('body');
        $sleepTime = $input->getOption('sleep');

        $messageManager = $this->getContainer()->get('phlexible_message.message_manager');
        $types = $messageManager->getTypeNames();

        if ($limit) {
            $messages = $messageManager->findBy(array(), array('createdAt' => 'DESC'), $limit);
            $messages = array_reverse($messages);
            foreach ($messages as $message) {
                $output->writeln(
                    sprintf(
                        "[%s] %s: %s [%s, %s]",
                        $message->getCreatedAt()->format('Y-m-d H:i:s'),
                        $types[$message->getType()],
                        $message->getSubject(),
                        $message->getChannel() ? : '-',
                        $message->getRole() ? : '-'
                    )
                );
                if ($showBody) {
                    $output->writeln(' > ' . $message->getBody());
                }
            }
        }

        if (!$follow) {
            return 0;
        }

        $message = $messageManager->findOneBy(array(), array('createdAt' => 'DESC'));
        $minTime = $message->getCreatedAt();

        while (1) {
            $expr = $messageManager->expr()
                ->andGreaterThan($minTime->format('Y-m-d H:i:s'), 'createdAt');

            $messages = $messageManager->findByExpression($expr, array('createdAt' => 'ASC'), 5);

            foreach ($messages as $message) {
                /* @var $message Message */

                $time = $message->getCreatedAt();

                if ($time <= $minTime) {
                    continue;
                }

                $minTime = $time;

                $output->writeln(
                    sprintf(
                        "[%s] %s: %s [%s] [%s]",
                        $message->getCreatedAt()->format('Y-m-d H:i:s'),
                        $types[$message->getType()],
                        $message->getSubject(),
                        $message->getChannel() ? : '-',
                        $message->getRole() ? : '-'
                    )
                );

                if ($showBody || $message->getType() >= Message::TYPE_ERROR) {
                    $output->writeln(' > ' . $message->getBody());
                }
            }

            sleep($sleepTime);
        }

        return 0;
    }
}
