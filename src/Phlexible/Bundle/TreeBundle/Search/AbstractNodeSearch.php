<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Search;

use Phlexible\Bundle\SearchBundle\Search\SearchResult;
use Phlexible\Bundle\SearchBundle\SearchProvider\SearchProviderInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Site\Domain\Site;

/**
 * Abstract node search
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractNodeSearch implements SearchProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRole()
    {
        return 'ROLE_ELEMENTS';
    }

    /**
     * @param NodeContext $node
     * @param string      $title
     *
     * @return SearchResult
     */
    protected function nodeToResult(NodeContext $node, Site $siteroot, $title, $icon, $language)
    {
        $handlerData = array(
            'handler' => 'element',
            'parameters' => array(
                'id'             => $node->getId(),
                'siteroot_id'    => $siteroot->getId(),
                'title'          => $siteroot->getTitle($language),
                'start_tid_path' => '/' . implode('/', $node->getTree()->getIdPath($node)),
            )
        );

        return new SearchResult(
            $node->getId(),
            $siteroot->getTitle($language) . ' :: ' . $node->getField('backend', $language) . ' (' . $language . ', ' . $node->getId() . ')',
            $node->getCreateUserId(),
            $node->getCreatedAt(),
            $icon,
            $title,
            $handlerData
        );
    }
}
