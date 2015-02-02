<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Criteria;

/**
 * Criteria array parser
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ArrayParser
{
    /**
     * @param array $data
     *
     * @return Criteria
     */
    public function parse(array $data)
    {
        $collection = array();
        if (isset($data['value'])) {
            foreach ($data['value'] as $criteriumData) {
                $type = $criteriumData['type'];
                $value = $criteriumData['value'];
                if ($type === 'collection') {
                    $criterium = $this->parse($criteriumData);
                } else {
                    if (in_array($type, array(Criteria::CRITERIUM_DATE_IS, Criteria::CRITERIUM_END_DATE, Criteria::CRITERIUM_START_DATE))) {
                        $value = new \DateTime($value);
                    }

                    $criterium = new Criterium($type, $value);
                }

                $collection[] = $criterium;
            }
        }

        $mode = Criteria::MODE_OR;
        if (isset($data['mode'])) {
            $mode = $data['mode'];
        }
        return new Criteria($collection, $mode);
    }
}
