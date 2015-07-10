<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementStructure\LinkExtractor;

use Phlexible\Bundle\ElementBundle\Entity\ElementLink;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;
use Phlexible\Component\Elementtype\Field\AbstractField;

/**
 * Media link extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaLinkExtractor implements LinkExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(ElementStructureValue $value, AbstractField $field)
    {
        return in_array($value->getType(), array('file', 'folder'));
    }

    /**
     * {@inheritdoc}
     */
    public function extract(ElementStructureValue $value, AbstractField $field)
    {
        if (!$value->getValue()) {
            return array();
        }

        $link = new ElementLink();
        $link
            ->setType($value->getType())
            ->setLanguage($value->getLanguage())
            ->setField($value->getName())
            ->setTarget($value->getValue());

        return array($link);
    }
}
