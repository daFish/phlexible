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
     * @return array
     */
    public function diff(array $from, array $to)
    {
        return $this->diffChild($from, $to);
    }

    /**
     * @param array  $from
     * @param array  $to
     *
     * @return array
     */
    private function diffChild(array $from, array $to)
    {
        $fromValues = isset($from['values']) ? $from['values'] : array();
        $toValues = isset($to['values']) ? $to['values'] : array();

        $values = array();

        foreach ($fromValues as $fromValue) {
            $found = false;
            foreach ($toValues as $toValue) {
                if ($fromValue['id'] === $toValue['id']) {
                    $found = true;
                    if ($fromValue['content'] != $toValue['content']) {
                        $fromValue['attributes'] = array(
                            'type'      => 'change',
                            'diff'      => $this->diffValue($fromValue['content'], $toValue['content']),
                            'diffValue' => $toValue['content'],
                        );
                    }
                    $values[] = $fromValue;
                    break;
                }
            }
            if (!$found) {
                $fromValue['attributes'] = array(
                    'type' => 'add',
                );
                $values[] = $fromValue;
            }
        }

        foreach ($toValues as $toValue) {
            $found = false;
            foreach ($fromValues as $fromValue) {
                if ($fromValue['id'] === $toValue['id']) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $toValue['attributes'] = array(
                    'type' => 'remove',
                );
                $values[] = $toValue;
            }
        }

        $fromChildren = isset($from['structures']) ? $from['structures'] : array();
        $toChildren = isset($to['structures']) ? $to['structures'] : array();

        $children = array();

        foreach ($fromChildren as $fromChild) {
            $found = false;
            foreach ($toChildren as $toChild) {
                if ($fromChild['id'] === $toChild['id']) {
                    $found = true;
                    $child = $this->diffChild($fromChild, $toChild);
                    $child['attributes'] = array(
                        'type' => 'change',
                    );
                    $children[] = $child;
                    break;
                }
            }
            if (!$found) {
                $fromChild['attributes'] = array(
                    'type'  => 'add',
                );
                $children[] = $fromChild;
            }
        }

        foreach ($toChildren as $name => $toChild) {
            $found = false;
            foreach ($fromChildren as $fromChild) {
                if ($fromChild['id'] === $toChild['id']) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $toChild['attributes'] = array(
                    'type' => 'remove',
                );
                $children[] = $toChild;
            }
        }

        $from['values'] = $values;
        $from['structures'] = $children;

        return $from;
    }

    /**
     * @param mixed $fromValue
     * @param mixed $toValue
     *
     * @return string
     */
    private function diffValue($fromValue, $toValue)
    {
        if (is_array($fromValue) ||is_array($toValue)) {
            return '';
        }

        $granularity = new Word;
        $diff = new Diff($granularity);

        return $diff->render($toValue, $fromValue);
    }
}
