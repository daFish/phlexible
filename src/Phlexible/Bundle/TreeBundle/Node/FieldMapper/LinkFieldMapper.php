<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Node\FieldMapper;

/**
 * Link field mapper
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class LinkFieldMapper implements FieldMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function accept($key)
    {
        return in_array($key, array('forward'));
    }

    /**
     * {@inheritdoc}
     */
    public function map(ValueExtractorInterface $valueExtractor, $content, array $mapping, $language)
    {
        $title = $valueExtractor->extract($content, $mapping['fields'][0], $language);

        if (!$title) {
            return null;
        }

        $value = $title;

        return is_array($value) ? json_encode($value) : $value;
    }
}
