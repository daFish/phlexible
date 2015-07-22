<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
