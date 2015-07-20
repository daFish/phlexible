<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
