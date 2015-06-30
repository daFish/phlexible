<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\Portlet;

use Phlexible\Bundle\DashboardBundle\Portlet\Portlet;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;

/**
 * Online portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class OnlinePortlet extends Portlet
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Return portlet data
     *
     * @return array
     */
    public function getData()
    {
        $users = $this->userManager->findLoggedInUsers();

        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'uid'      => $user->getId(),
                'username' => $user->getUsername(),
                'image'    => '/bundles/users/images/male-black-blonde.png',
            ];
        }

        return $data;
    }
}
