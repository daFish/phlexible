<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
