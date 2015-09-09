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
use Webmozart\Expression\Comparison\Contains;
use Webmozart\Expression\Logic\Disjunction;
use Webmozart\Expression\Selector\Key;

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
        $expr = new Disjunction(array(
            new Key('firstname', new Contains($query)),
            new Key('lastname', new Contains($query)),
            new Key('username', new Contains($query)),
            new Key('email', new Contains($query)),
        ));

        $users = $this->userManager->findByExpression($expr);

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
