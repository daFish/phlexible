<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\File\Dumper;

use Phlexible\Bundle\TreeBundle\Model\RouteManagerInterface;

/**
 * XML routes dumper.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlRoutesDumper
{
    /**
     * @var RouteManagerInterface
     */
    private $routeManager;

    /**
     * @param RouteManagerInterface $routeManager
     */
    public function __construct(RouteManagerInterface $routeManager)
    {
        $this->routeManager = $routeManager;
    }

    /**
     * @return string
     */
    public function dump()
    {
        $dom = new \DOMDocument();
        $dom->formatOutput = true;

        $routesNode = $dom->createElement('routes');
        $dom->appendChild($routesNode);

        foreach ($this->routeManager->findAll() as $route) {
            $routeNode = $dom->createElement('route');
            $routesNode->appendChild($routeNode);

            $idAttr = $dom->createAttribute('id');
            $idAttr->value = $route->getId();
            $routeNode->appendChild($idAttr);

            $pathAttr = $dom->createAttribute('path');
            $pathAttr->value = $route->getPath();
            $routeNode->appendChild($pathAttr);

            $conditionAttr = $dom->createAttribute('condition');
            $conditionAttr->value = $route->getCondition();
            $routeNode->appendChild($conditionAttr);

            $languageAttr = $dom->createAttribute('language');
            $languageAttr->value = $route->getLanguage();
            $routeNode->appendChild($languageAttr);

            $nameAttr = $dom->createAttribute('name');
            $nameAttr->value = $route->getName();
            $routeNode->appendChild($nameAttr);

            $hostAttr = $dom->createAttribute('host');
            $hostAttr->value = $route->getHost();
            $routeNode->appendChild($hostAttr);

            $defaultsNode = $dom->createElement('defaults');
            $defaultsNode->textContent = json_encode($route->getDefaults());
            $routeNode->appendChild($defaultsNode);

            $methodsNode = $dom->createElement('methods');
            $methodsNode->textContent = json_encode($route->getMethods());
            $routeNode->appendChild($methodsNode);

            $optionsNode = $dom->createElement('options');
            $optionsNode->textContent = json_encode($route->getOptions());
            $routeNode->appendChild($optionsNode);

            $requirementsNode = $dom->createElement('requirements');
            $requirementsNode->textContent = json_encode($route->getRequirements());
            $routeNode->appendChild($requirementsNode);

            $schemesNode = $dom->createElement('schemes');
            $schemesNode->textContent = json_encode($route->getSchemes());
            $routeNode->appendChild($schemesNode);
        }

        return $dom->saveXML();
    }
}
