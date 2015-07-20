<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Node\FieldMapper;

/**
 * Simple field mapper
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class SimpleFieldMapper implements FieldMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function accept($key)
    {
        return in_array($key, array('backend', 'page', 'navigation', 'custom1', 'custom2', 'custom3', 'custom4', 'custom5'));
    }

    /**
     * {@inheritdoc}
     */
    public function map(ValueExtractorInterface $valueExtractor, $content, array $mapping, $language)
    {
        $pattern = $mapping['pattern'];
        $replace = array();
        foreach ($mapping['fields'] as $field) {
            $value = $valueExtractor->extract($content, $field, $language);
            $replace['$' . $field['index']] = $value;
        }
        $title = str_replace(array_keys($replace), array_values($replace), $pattern);

        if (!$title) {
            return null;
        }

        return $title;
    }
}
