<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Twig\Extension;

use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Node\Model\NodeInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Twig url extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UrlExtension extends \Twig_Extension
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('path', array($this, 'path')),
            new \Twig_SimpleFunction('url', array($this, 'url')),
        );
    }

    /**
     * @param string $name
     * @param array  $parameters
     * @param bool   $relative
     *
     * @return string
     */
    public function path($name, array $parameters = array(), $relative = false)
    {
        if ($name instanceof NodeInterface) {
            return $this->router->generate($name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
        } elseif ($name instanceof NodeContext) {
            return $this->router->generate($name->getNode(), $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
        } elseif ($name instanceof ElementStructureValue) {
            if ($name->getType() === 'link') {
                $link = $name->getValue();
                if ($link['type'] === 'internal' || $link['type'] === 'intrasiteroot') {
                    return $this->router->generate(
                        (int) $link['tid'],
                        $parameters,
                        $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH
                    );
                } elseif ($link['type'] === 'external') {
                    return $link['url'];
                } elseif ($link['type'] === 'mailto') {
                    return 'mailto:' . $link['recipient'];
                }
            }
        } elseif (is_array($name) && isset($name['type']) && in_array($name['type'], array('internal', 'intrasiteroot', 'external', 'mailto'))) {
            $link = $name;
            if ($link['type'] === 'internal' || $link['type'] === 'intrasiteroot') {
                return $this->router->generate(
                    (int) $link['tid'],
                    $parameters,
                    $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH
                );
            } elseif ($link['type'] === 'external') {
                return $link['url'];
            } elseif ($link['type'] === 'mailto') {
                return 'mailto:' . $link['recipient'];
            }
        } elseif (strlen($name) && (is_int($name) || (int) $name)) {
            return $this->router->generate((int) $name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
        } elseif (is_string($name)) {
            return $this->router->generate($name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
        }

        return '';
    }

    /**
     * @param string $name
     * @param array  $parameters
     * @param bool   $schemeRelative
     *
     * @return string
     */
    public function url($name, array $parameters = array(), $schemeRelative = false)
    {
        if ($name instanceof NodeInterface) {
            return $this->router->generate($name, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
        } elseif ($name instanceof NodeContext) {
            return $this->router->generate($name->getNode(), $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
        } elseif ($name instanceof ElementStructureValue) {
            if ($name->getType() === 'link') {
                $link = $name->getValue();
                if ($link['type'] === 'internal' || $link['type'] === 'intrasiteroot') {
                    return $this->router->generate((int) $link['tid'], $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
                } elseif ($link['type'] === 'external') {
                    return $link['url'];
                } elseif ($link['type'] === 'mailto') {
                    return 'mailto:' . $link['recipient'];
                }
            }
        } elseif (is_array($name) && isset($name['type']) && in_array($name['type'], array('internal', 'intrasiteroot', 'external', 'mailto'))) {
            $link = $name;
            if ($link['type'] === 'internal' || $link['type'] === 'intrasiteroot') {
                return $this->router->generate((int) $link['tid'], $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
            } elseif ($link['type'] === 'external') {
                return $link['url'];
            } elseif ($link['type'] === 'mailto') {
                return 'mailto:' . $link['recipient'];
            }
        } elseif (strlen($name) && (is_int($name) || (int) $name)) {
            return $this->router->generate((int) $name, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
        } elseif (is_string($name)) {
            return $this->router->generate($name, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_url';
    }
}
