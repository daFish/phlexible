<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Bundle\TreeBundle\Model\PageInterface;
use Phlexible\Component\Node\Domain\Node;

/**
 * Page node
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 */
class PageNode extends Node implements PageInterface
{
    /**
     * @var bool
     * @ORM\Column(name="in_navigation", type="boolean", options={"default"=0})
     */
    private $inNavigation = false;

    /**
     * {@inheritdoc}
     */
    public function getInNavigation()
    {
        return $this->inNavigation;
    }

    /**
     * {@inheritdoc}
     */
    public function setInNavigation($inNavigation)
    {
        $this->inNavigation = $inNavigation;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCache()
    {
        return $this->getAttribute('cache', array());
    }

    /**
     * {@inheritdoc}
     */
    public function setCache($cache)
    {
        if ($cache) {
            $this->setAttribute('cache', $cache);
        } else {
            $this->removeAttribute('cache');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getController()
    {
        return $this->getAttribute('controller');
    }

    /**
     * {@inheritdoc}
     */
    public function setController($controller)
    {
        if ($controller) {
            $this->setAttribute('controller', $controller);
        } else {
            $this->removeAttribute('controller');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return $this->getAttribute('template');
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplate($template)
    {
        if ($template) {
            $this->setAttribute('template', $template);
        } else {
            $this->removeAttribute('template');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutes()
    {
        return $this->getAttribute('routes', array());
    }

    /**
     * {@inheritdoc}
     */
    public function setRoutes(array $routes = null)
    {
        if ($routes) {
            $this->setAttribute('routes', $routes);
        } else {
            $this->removeAttribute('routes');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNeedAuthentication()
    {
        return $this->getAttribute('needAuthentication', false);
    }

    /**
     * {@inheritdoc}
     */
    public function setNeedAuthentication($needsAuthentication)
    {
        if ($needsAuthentication) {
            $this->setAttribute('needAuthentication', true);
        } else {
            $this->removeAttribute('needAuthentication');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecurityExpression()
    {
        $security = $this->getAttribute('security');
        if (!$security) {
            return 'true';
        }

        if (!empty($security['expression'])) {
            $expression = $security['expression'];
        } else {
            $expressions = array();
            if (!empty($security['authenticationRequired'])) {
                $expressions[] = 'is_fully_authenticated()';
            }
            if (!empty($security['roles'])) {
                $security['roles'] = (array) $security['roles'];
                foreach ($security['roles'] as $role) {
                    $expressions[] = "has_role('$role')";
                }
            }
            if (!empty($security['query_acl'])) {
                $expressions[] = "is_granted('VIEW', node)";
            }

            $expression = implode(' and ', $expressions);
        }

        return $expression ?: 'true';
    }
}
