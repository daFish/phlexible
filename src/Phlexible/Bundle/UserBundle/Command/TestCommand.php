<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\Command;

use Phlexible\Bundle\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TestCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('user:test')
            ->setDefinition(
                [
                    new InputArgument('username', InputArgument::REQUIRED, 'Username'),
                    new InputArgument('password', InputArgument::REQUIRED, 'Password'),
                ]
            )
            ->setDescription('Create user.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $userRepository = $this->getContainer()->get('phlexible_user.user_manager');
        $user = $userRepository->findByUsername($username);

        $e = new \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder();
        $enc = $e->encodePassword($password, $user->getSalt());

        $output->writeln($enc);

        return 0;
    }
}
