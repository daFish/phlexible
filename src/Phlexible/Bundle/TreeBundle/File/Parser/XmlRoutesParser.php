<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\File\Parser;

use Phlexible\Bundle\TreeBundle\Entity\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * XML routes parser
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlRoutesParser
{
    /**
     * @param string $content
     *
     * @return RouteCollection
     */
    public function parse($content)
    {
        $xml = simplexml_load_string($content);

        $routes = new RouteCollection();
        foreach ($xml->route as $routeNode) {
            $attr = $routeNode->attributes();
            $path = (string) $attr['path'];
            $host = (string) $attr['host'];
            $condition = (string) $attr['condition'];
            $language = (string) $attr['language'];
            $name = (string) $attr['name'];
            $id = (int) $attr['id'];
            $defaults = json_decode((string) $routeNode->defaults);
            $requirements = json_decode((string) $routeNode->requirements);
            $schemes = json_decode((string) $routeNode->schemes);
            $methods = json_decode((string) $routeNode->methods);
            $options = json_decode((string) $routeNode->options);

            $route = new Route($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
            //$route->setId($id);
            $route->setName($name);
            $route->setLanguage($language);

            $routes->add($name, $route);
        }

        return $routes;
    }
}
