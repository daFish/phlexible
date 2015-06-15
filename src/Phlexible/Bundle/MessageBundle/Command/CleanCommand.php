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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Clean command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CleanCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('message:clean')
            ->setDefinition(
                [
                    new InputArgument('days', InputArgument::OPTIONAL, 'Keep latest <days> days of messages.', 30),
                ]
            )
            ->setDescription('Delete old messages.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $days = $input->getArgument('days');

        $messageManager = $this->getContainer()->get('phlexible_message.message_manager');

        $date = new \DateTime("-$days days");
        $expr = $messageManager->expr()
            ->lessThan($date->format('Y-m-d H:i:s'), 'createdAt');

        $count = 0;
        while ($messages = $messageManager->findByExpr($expr, ['createdAt' => 'ASC'])) {
            foreach ($messages as $message) {
                $messageManager->deleteMessage($message);
                $count++;
            }
        }

        $output->writeln("Deleted $count messages.");

        return 0;
    }
}
