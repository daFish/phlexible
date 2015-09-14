<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Node\LinkExtractor;

/**
 * File field link extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileFieldLinkExtractor implements LinkExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function extract($value)
    {
        if ($value['type'] !== 'file') {
            return array();
        }

        $value = $value['value'];

        return array(
            array('type' => 'file', 'target' => $value)
        );
    }
}
