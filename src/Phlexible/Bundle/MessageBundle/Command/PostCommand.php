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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Info command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PostCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('message:post')
            ->setDefinition(
                array(
                    new InputArgument('subject', InputArgument::OPTIONAL, 'Message subject'),
                    new InputOption('body', null, InputOption::VALUE_REQUIRED, 'Message body'),
                    new InputOption('type', null, InputOption::VALUE_REQUIRED, 'Message type', 0),
                    new InputOption('channel', null, InputOption::VALUE_REQUIRED, 'Message channel'),
                    new InputOption('role', null, InputOption::VALUE_REQUIRED, 'Message role'),
                )
            )
            ->setDescription('Post message');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = date('YmdHis');

        $subject = $input->getArgument('subject');
        if (!$subject) {
            $subject = 'message-' . $date;
        }

        $body = $input->getOption('body');
        if (!$body) {
            $body = 'body-' . $date;
        }

        $type = $input->getOption('type');
        $types = array('info', 'error');
        if (in_array($type, $types)) {
            $type = array_search($type, $types);
        } elseif (!in_array($type, array_keys($types))) {
            $type = null;
        }

        $channel = $input->getOption('channel');
        $role = $input->getOption('role');

        $message = Message::create($subject, $body, $type, $channel, $role, 'cli');
        $messageService = $this->getContainer()->get('phlexible_message.message_poster');
        $messageService->post($message);

        $output->writeln('Message posted.');

        return 0;
    }
}
