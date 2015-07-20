<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Node\FieldMapper;

/**
 * Element value extractor
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class ElementValueExtractor implements ValueExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function extract($content, array $mapping, $language)
    {
        $values = $content->__getValues();

        if (isset($values[$mapping['dsId']])) {
            return $values[$mapping['dsId']];
        }

        return null;
    }
}
