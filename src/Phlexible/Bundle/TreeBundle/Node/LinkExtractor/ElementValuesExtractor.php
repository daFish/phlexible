<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Node\LinkExtractor;

/**
 * Element values extractor
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class ElementValuesExtractor implements ValuesExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function extract($content, $language)
    {
        $values = $this->extractRecursive($content, $language);

        return $values;
    }

    private function extractRecursive($content, $language)
    {
        $values = array();
        foreach ($content->__getValueDescriptors() as $dsId => $descriptor) {
            if ($descriptor['value'] !== null) {
                $values[] = array(
                    'field' => $descriptor['name'],
                    'type' => $descriptor['type'],
                    'value' => $descriptor['value']
                );
            }
        }

        foreach ($content->__getChildren() as $name => $nameChildren) {
            foreach ($nameChildren as $child) {
                $values = array_merge($values, $this->extractRecursive($child, $language));
            }
        }

        return $values;
    }
}
