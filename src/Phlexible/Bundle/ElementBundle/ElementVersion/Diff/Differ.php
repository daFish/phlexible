<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersion\Diff;

use cogpowered\FineDiff\Diff;
use cogpowered\FineDiff\Granularity\Word;

/**
 * Differ
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Differ
{
    /**
     * @param array $from
     * @param array $to
     *
     * @return DiffResult
     */
    public function diff(array $from, array $to)
    {
        $diff = $this->diffChild($from, $to);

        return $diff;
    }

    /**
     * @param array $from
     * @param array $to
     *
     * @return array
     */
    private function diffChild(array $from, array $to)
    {
        $diff = array('children' => array(), 'values' => array());

        $fromValues = $from['values'];
        $toValues = $to['values'];

        foreach ($fromValues as $dsId => $fromLanguageValues) {
            foreach ($fromLanguageValues as $language => $fromValue) {
                if (isset($toValues[$dsId][$language])) {
                    $toValue = $toValues[$dsId][$language];

                    if ($fromValue !== $toValue) {
                        $diff['values'][$dsId][$language] = array(
                            'type' => 'change',
                            'diff' => $this->diffValue($fromValue, $toValue),
                            'from' => $fromValue,
                            'to'   => $toValue,
                        );
                    }
                } else {
                    $diff['values'][$dsId][$language] = array(
                        'type'  => 'add',
                        'value' => $fromValue
                    );
                }
            }
        }

        foreach ($toValues as $dsId => $toLanguageValues) {
            foreach ($toLanguageValues as $language => $toValue) {
                if (!isset($fromValues[$dsId])) {
                    $diff['values'][$dsId][$language] = array(
                        'type'  => 'remove',
                        'value' => $toValue
                    );
                }
            }
        }

        foreach ($from['children'] as $name => $fromChild) {
            $found = false;
            foreach ($to['children'] as $toChild) {
                if ($fromChild['id'] === $toChild['id']) {
                    $found = true;
                    $diff['children'][$name][] = $this->diffChild($fromChild, $toChild);
                    break;
                }
            }
            if (!$found) {
                $diff['children'][$name][] = array(
                    'type'  => 'add',
                    'child' => $fromChild
                );
            }
        }

        foreach ($to['children'] as $name => $toChild) {
            $found = false;
            foreach ($from['children'] as $fromChild) {
                if ($fromChild['id'] === $toChild['id']) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $diff['children'][$name][] = array(
                    'type'  => 'remove',
                    'child' => $toChild
                );
            }
        }

        return $diff;
    }

    /**
     * @param mixed $fromValue
     * @param mixed $toValue
     *
     * @return string
     */
    private function diffValue($fromValue, $toValue)
    {
        $granularity = new Word;
        $diff = new Diff($granularity);

        return $diff->render($toValue, $fromValue);
    }
}
