<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\AccessControlBundle\Provider;

use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Phlexible\Component\AccessControl\Provider\ProviderInterface;

/**
 * User provider
 *
 * @author Marco Fischer <mf@brainbits.net>
 */
class UserProvider implements ProviderInterface
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
     * Return object name
     *
     * @param string $objectType
     * @param string $objectId
     *
     * @return string
     */
    public function getName($objectType, $objectId)
    {
        $user = $this->userManager->find($objectId);

        return $user->getDisplayName();
    }

    /**
     * Return users
     *
     * @param string $query
     * @param int    $limit
     * @param int    $offset
     *
     * @return array
     */
    public function getAll($query, $limit, $offset)
    {
        // TODO: user query
        $users = $this->userManager->findBy(array(), array('lastname' => 'ASC'), $limit, $offset);

        $data = array();
        foreach ($users as $user) {
            $name = $user->getDisplayName();

            $data[] = array(
                'type'       => 'user',
                'objectType' => 'uid',
                'objectId'   => $user->getId(),
                'label'      => $name
            );
        }

        return array(
            'total' => $this->userManager->countAll(),
            'data'  => $data,
        );
    }
}
