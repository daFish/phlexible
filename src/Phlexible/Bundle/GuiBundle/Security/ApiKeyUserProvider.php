<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Security;

use FOS\UserBundle\Security\UserProvider;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ApiKeyUserProvider.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ApiKeyUserProvider extends UserProvider
{
    /**
     * {@inheritdoc}
     */
    public function getUsernameForApiKey($apiKey)
    {
        $username = $this->userManager->findUserBy(array('apiKey' => $apiKey));

        return $username;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless. Throwing this exception
        // is proper to make things stateless
        throw new UnsupportedUserException();
    }
}
