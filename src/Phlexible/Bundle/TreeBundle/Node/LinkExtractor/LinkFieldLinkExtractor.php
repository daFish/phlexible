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
class LinkFieldLinkExtractor implements LinkExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function extract($value)
    {
        if ($value['type'] !== 'link') {
            return array();
        }

        $value = $value['value'];
        $type = $value['type'];
        if (in_array($type, array('internal', 'intrasiteroot')) && !empty($value['tid'])) {
            return array(
                array('type' => 'node', 'target' => $value['tid'])
            );
        } elseif ($type === 'external' && !empty($value['url'])) {
            return array(
                array('type' => 'url', 'target' => $value['url'])
            );
        } elseif ($type === 'mailto' && !empty($value['recipient'])) {
            return array(
                array('type' => 'mailto', 'target' => $value['recipient'])
            );
        }

        return array();
    }
}
