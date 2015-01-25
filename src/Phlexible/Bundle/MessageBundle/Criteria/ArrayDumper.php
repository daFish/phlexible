<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Criteria;

/**
 * Message criteria array dumper
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ArrayDumper
{
    /**
     * @param Criteria $criteria
     *
     * @return string
     */
    public function dump(Criteria $criteria)
    {
        $data = array(
            'mode' => $criteria->getMode(),
        );

        foreach ($criteria as $criterium) {
            if ($criterium instanceof Criteria) {
                $data['criteria'][] = array('type' => $criterium->getType(), 'value' => $this->dump($criterium, 1));
            } elseif ($criterium instanceof Criterium) {
                $type = $criterium->getType();
                $value = $criterium->getValue();
                if ($value instanceof \DateTime) {
                    $value = $value->format('Y-m-d H:i:s');
                }
                $data['criteria'][] = array('type' => $type, 'value' => $value);
            }
        }

        return $data;
    }
}
