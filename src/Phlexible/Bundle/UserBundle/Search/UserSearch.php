<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\Search;

use Phlexible\Bundle\SearchBundle\Search\SearchResult;
use Phlexible\Bundle\SearchBundle\SearchProvider\SearchProviderInterface;
use Phlexible\Bundle\UserBundle\Entity\User;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;

/**
 * User search
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UserSearch implements SearchProviderInterface
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
     * {@inheritdoc}
     */
    public function getRole()
    {
        return 'ROLE_USERS';
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchKey()
    {
        return 'u';
    }

    /**
     * {@inheritdoc}
     */
    public function search($query)
    {
        $criteria = $this->userManager->createCriteria();
        $criteria->where(
            $criteria->expr()->orX(
                $criteria->expr()->contains('firstname', $query),
                $criteria->expr()->contains('lastname', $query),
                $criteria->expr()->contains('username', $query),
                $criteria->expr()->contains('email', $query)
            )
        );
        $users = $this->userManager->query($criteria);

        $createUser = new User();
        $createUser->setUsername('(unknown)');

        $results = array();
        foreach ($users as $user) {
            $results[] = new SearchResult(
                $user->getId(),
                $user->getDisplayName(),
                $createUser->getDisplayName(),
                $user->getCreatedAt(),
                '/bundles/phlexibleuser/icons/user.png',
                'Users Search',
                array(
                    'handler'    => 'users',
                    'parameters' => array(
                        'userId' => $user->getId(),
                        'query'  => $user->getUsername()
                    ),
                )
            );
        }

        return $results;
    }
}
