<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Tree;

/**
 * Live tree context
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LiveTreeContext implements TreeContextInterface
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
        return 'live';
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
