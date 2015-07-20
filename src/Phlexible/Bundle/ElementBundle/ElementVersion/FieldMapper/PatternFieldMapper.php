<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersion\FieldMapper;

/**
 * Pattern field mapper
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class PatternFieldMapper implements FieldMapperInterface
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
    public function map(array $structure, $language, array $mapping)
    {
        $pattern = $mapping['pattern'];
        $replace = array();
        foreach ($mapping['fields'] as $field) {
            $dsId = $field['dsId'];
            $value = $this->findValue($structure, $dsId, $language);
            if (!$value) {
                throw new \Exception("Value for dsId $dsId not found.");
            }
            $replace['$' . $field['index']] = $value->getValue();
        }
        $title = str_replace(array_keys($replace), array_values($replace), $pattern);

        if (!$title) {
            return null;
        }

        return $title;
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
