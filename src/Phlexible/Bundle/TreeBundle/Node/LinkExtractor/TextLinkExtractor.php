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
 * Text link extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TextLinkExtractor implements LinkExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function extract($value)
    {
        if (!is_string($value['value']) || !preg_match_all('/\[tid[:=](\d+)\]/', $value['value'], $matches)) {
            return array();
        }

        $links = array();

        foreach ($matches[1] as $nodeId) {
            $links[] = array('type' => 'link-internal', 'target' => $nodeId);
        }

        return $links;
    }
}
