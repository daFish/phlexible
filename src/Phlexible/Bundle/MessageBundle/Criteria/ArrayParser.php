<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Criteria;

/**
 * Message criteria array parser
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
        $mode = Criteria::MODE_OR;
        if (isset($data['mode'])) {
            $mode = $data['mode'];
        }
        $criteria = new Criteria(array(), $mode);

        if (isset($data['criteria'])) {
            foreach ($data['criteria'] as $criteriumData) {
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

                $criteria->add($criterium);
            }
        }

        return $criteria;
    }
}
