<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\AccessControl\Provider;

/**
 * Provider interface
 *
 * @author Marco Fischer <mf@brainbits.net>
 */
interface ProviderInterface
{
    /**
     * Return object name
     *
     * @param string $objectType
     * @param string $objectId
     *
     * @return string
     */
    public function getName($objectType, $objectId);

    /**
     * Return users
     *
     * @param string $query
     * @param int    $limit
     * @param int    $offset
     *
     * @return array
     */
    public function getAll($query, $limit, $offset);
}
