<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersion\FieldMapper;

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
    public function map(array $structure, $language, array $mapping)
    {
        $mappings = array();
        foreach ($mapping['fields'] as $field) {
            $dsId = $field['dsId'];
            $mappings[$field['type']] = $this->findValue($structure, $dsId, $language);
        }
        $replace = array();
        if (isset($mappings['datetime'])) {
            $replace[] = $mappings['datetime']->getValue();
        }
        if (isset($mappings['date'])) {
            $replace[] = $mappings['date']->getValue();
        }
        if (isset($mappings['time'])) {
            $replace[] = $mappings['time']->getValue();
        }
        if (!count($replace)) {
            return null;
        }

        return implode(' ', $replace);
    }

    /**
     * @param array  $structure
     * @param string $dsId
     * @param string $language
     *
     * @return mixed|null
     */
    private function findValue(array $structure, $dsId, $language)
    {
        if (isset($structure['values'][$dsId])) {
            return $structure['values'][$dsId][$language];
        }

        foreach ($structure['children'] as $childStructure) {
            $value = $this->findValue($childStructure, $dsId, $language);
            if ($value) {
                return $value;
            }
        }

        return null;
    }
}
