<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Portlet;

use Phlexible\Bundle\DashboardBundle\Portlet\Portlet;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

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
     * @param TranslatorInterface  $translator
     * @param UserManagerInterface $userManager
     */
    public function __construct(TranslatorInterface $translator, UserManagerInterface $userManager)
    {
        $this
            ->setId('online-portlet')
            ->setTitle($translator->trans('users.who_is_online', [], 'gui'))
            ->setXtype('users-online-portlet')
            ->setIconClass('p-user-portlet-icon');

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
