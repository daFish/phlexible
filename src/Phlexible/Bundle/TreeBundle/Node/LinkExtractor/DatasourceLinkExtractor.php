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
 * Datasource link extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DatasourceLinkExtractor implements LinkExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function extract($value)
    {
        if ($value['type'] !== 'suggest') {
            return array();
        }

        //$value = $value['value'];

        return array(
            array('type' => 'datasource', 'target' => '123')
        );
    }
}
