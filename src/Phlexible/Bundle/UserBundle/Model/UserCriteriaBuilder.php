<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\UserBundle\Model;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\HttpFoundation\Request;

/**
 * User criteria builder
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class UserCriteriaBuilder
{
    /**
     * @param Criteria $criteria
     * @param Request  $request
     */
    public static function applyFromRequest(Criteria $criteria, Request $request)
    {
        $values = $request->query->all();

        $roles = array();
        $groups = array();
        foreach ($values as $key => $value) {
            if (!$value) {
                continue;
            }

            if ($key == 'text') {
                $criteria->andWhere(
                    $criteria->expr()->orX(
                        $criteria->expr()->contains('firstname', '%'.$value.'%'),
                        $criteria->expr()->contains('lastname', '%'.$value.'%'),
                        $criteria->expr()->contains('username', '%'.$value.'%'),
                        $criteria->expr()->contains('email', '%'.$value.'%')
                    )
                );
            } elseif ($key == 'account_disabled') {
                $criteria->andWhere(
                    $criteria->expr()->eq('disabled', true)
                );
            } elseif ($key == 'account_expired') {
                $criteria->andWhere(
                    $criteria->expr()->eq('expired', true)
                );
            } elseif ($key == 'account_has_expire_date') {
                $criteria->andWhere(
                    $criteria->expr()->neq('expireDate', null)
                );
            } elseif ($key === 'roles') {
                $criteria->andWhere(
                    $criteria->expr()->contains('roles', (array) $value)
                );
            } elseif ($key === 'groups') {
                $criteria->andWhere(
                    $criteria->expr()->contains('groups', (array) $value)
                );
            } elseif (substr($key, 0, 5) == 'role_') {
                $roles[] = substr($key, 5);
            } elseif (substr($key, 0, 6) == 'group_') {
                $groups[] = substr($key, 6);
            }
        }

        if (count($roles)) {
            $criteria->andWhere(
                $criteria->expr()->contains('roles', $roles)
            );
        }

        if (count($groups)) {
            $criteria->andWhere(
                $criteria->expr()->contains('groups', $groups)
            );
        }
    }
}
