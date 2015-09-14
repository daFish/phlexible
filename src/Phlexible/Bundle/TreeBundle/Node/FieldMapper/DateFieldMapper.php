<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Node\FieldMapper;

/**
 * Date field mapper
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class DateFieldMapper implements FieldMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function accept($key)
    {
        return in_array($key, array('date', 'time', 'datetime'));
    }

    /**
     * {@inheritdoc}
     */
    public function map(ValueExtractorInterface $valueExtractor, $content, array $mapping, $language)
    {
        $mappings = array();
        foreach ($mapping['fields'] as $mappingField) {
            $mappings[$mappingField['type']] = $valueExtractor->extract($content, $mappingField, $language);
        }
        $replace = array();
        if (isset($mappings['datetime'])) {
            $replace[] = $mappings['datetime'];
        }
        if (isset($mappings['date'])) {
            $replace[] = $mappings['date'];
        }
        if (isset($mappings['time'])) {
            $replace[] = $mappings['time'];
        }
        if (!count($replace)) {
            return null;
        }

        return implode(' ', $replace);
    }
}
