<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Tree;

/**
 * Tree context
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class WorkingTreeContext implements TreeContextInterface
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @param string $locale
     */
    public function __construct($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return array
     */
    public function getWorkspace()
    {
        return 'working';
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
