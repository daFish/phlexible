<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Routing\Enhancer;

use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Cmf\Component\Routing\Enhancer\RouteEnhancerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Cache enhancer.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CacheEnhancer implements RouteEnhancerInterface
{
    /**
     * {@inheritdoc}
     */
    public function enhance(array $defaults, Request $request)
    {
        if (!isset($defaults['node']) || !$defaults['node'] instanceof NodeContext) {
            return $defaults;
        }

        $node = $defaults['node'];

        if (!$node->getAttribute('cache')) {
            return $defaults;
        }

        $cache = $node->getAttribute('cache');
        $configuration = new Cache(array());

        if (!empty($cache['ETag'])) {
            $configuration->setETag($cache['ETag']);
        }
        if (!empty($cache['lastModified'])) {
            $configuration->setLastModified($cache['lastModified']);
        }
        if (!empty($cache['expires'])) {
            $configuration->setExpires($cache['expires']);
        }
        if (!empty($cache['public'])) {
            $configuration->setPublic($cache['public']);
        }
        if (!empty($cache['maxage'])) {
            $configuration->setMaxAge($cache['maxage']);
        }
        if (!empty($cache['smaxage'])) {
            $configuration->setSMaxAge($cache['smaxage']);
        }
        if (!empty($cache['vary'])) {
            $configuration->setVary($cache['vary']);
        }

        $defaults['_cache'] = $configuration;

        return $defaults;
    }
}
