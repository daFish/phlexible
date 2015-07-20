<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Node\LinkExtractor;

use Phlexible\Bundle\TreeBundle\Entity\NodeLink;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Link extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LinkExtractor
{
    /**
     * @var ValuesExtractorInterface
     */
    private $valuesExtractor;

    /**
     * @var LinkExtractorInterface[]
     */
    private $extractors = array();

    /**
     * @param ValuesExtractorInterface $valuesExtractor
     * @param LinkExtractorInterface[] $extractors
     */
    public function __construct(ValuesExtractorInterface $valuesExtractor, array $extractors = array())
    {
        $this->valuesExtractor = $valuesExtractor;

        foreach ($extractors as $extractor) {
            $this->addExtractor($extractor);
        }
    }

    /**
     * @param LinkExtractorInterface $extractor
     *
     * @return $this
     */
    public function addExtractor(LinkExtractorInterface $extractor)
    {
        $this->extractors[] = $extractor;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function extract(NodeContext $node, $language, $version)
    {
        $content = $node->getContent($language, $version);
        if (!$content) {
            return null;
        }

        $values = $this->valuesExtractor->extract($content, $language);

        if (!$values) {
            return array();
        }

        $links = array();
        foreach ($this->extractors as $extractor) {
            foreach ($values as $value) {
                foreach ($extractor->extract($value) as $link) {
                    $links[] = new NodeLink($node->getId(), $version, $language, $link['type'], $value['field'], $link['target']);
                }
            }
        }

        return $links;
    }
}
