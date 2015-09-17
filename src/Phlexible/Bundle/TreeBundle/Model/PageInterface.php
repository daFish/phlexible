<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Model;

/**
 * Page interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface PageInterface
{
    /**
     * @return bool
     */
    public function getInNavigation();

    /**
     * @param bool $inNavigation
     *
     * @return $this
     */
    public function setInNavigation($inNavigation);

    /**
     * @return array
     */
    public function getCache();

    /**
     * @param array $cache
     *
     * @return $this
     */
    public function setCache($cache);

    /**
     * @return string
     */
    public function getController();

    /**
     * @param string $controller
     *
     * @return $this
     */
    public function setController($controller);

    /**
     * @return string
     */
    public function getTemplate();

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate($template);

    /**
     * @return array
     */
    public function getRoutes();

    /**
     * @param array $routes
     *
     * @return $this
     */
    public function setRoutes(array $routes);

    /**
     * @return bool
     */
    public function getNeedAuthentication();

    /**
     * @param bool $needsAuthentication
     *
     * @return $this
     */
    public function setNeedAuthentication($needsAuthentication);

    /**
     * @return string
     */
    public function getSecurityExpression();
}
