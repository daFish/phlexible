<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Model;

/**
 * Page interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface PageInterface
{
    /**
     * @return boolean
     */
    public function getInNavigation();

    /**
     * @param boolean $inNavigation
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
     * @return boolean
     */
    public function getNeedAuthentication();

    /**
     * @param boolean $needsAuthentication
     *
     * @return $this
     */
    public function setNeedAuthentication($needsAuthentication);

    /**
     * @return string
     */
    public function getSecurityExpression();
}
