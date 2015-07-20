<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersion\FieldMapper;

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
    public function map(array $structure, $language, array $mapping)
    {
        $dsId = $mapping['fields'][0]['dsId'];
        $title = $this->findValue($structure, $dsId, $language);

        if (!$title || !$title->getValue()) {
            return null;
        }

        $value = $title->getValue();

        return is_array($value) ? json_encode($value) : $value;
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
