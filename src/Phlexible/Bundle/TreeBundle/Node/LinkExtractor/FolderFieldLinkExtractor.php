<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Node\LinkExtractor;

/**
 * Folder field link extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FolderFieldLinkExtractor implements LinkExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function extract($value)
    {
        if ($value['type'] !== 'folder') {
            return array();
        }

        $value = $value['value'];

        return array(
            array('type' => 'folder', 'target' => $value)
        );
    }
}
