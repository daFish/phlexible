<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Get property command
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class GetPropertyCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('phlx:users:property-get')
            ->setDescription('Get user property.')
            ->setDefinition(array(
                new InputArgument(
                    'username',
                    InputArgument::REQUIRED,
                    'Username.'
                ),
                new InputArgument(
                    'key',
                    InputArgument::OPTIONAL,
                    'Property key.'
                )
            ));
    }

    /**
     * Executes the current command.
     *
     * @param  InputInterface  $input  An InputInterface instance
     * @param  OutputInterface $output An OutputInterface instance
     * @return integer         0 if everything went fine, or an error code
     */
    public function execute(InputInterface $input,
                            OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $key      = $input->getArgument('key');

        $userManager = $this->getContainer()->get('phlx_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        if (!$user) {
            $output->writeln("User $username not found.");
            return 0;
        }

        if ($key) {
            $properties = array($key => $user->getProperty($key));
        } else {
            $properties = $user->getProperties();
        }

        foreach ($properties as $key => $value) {
            $output->writeln("$key => $value");
        }

        return 0;
    }
}
