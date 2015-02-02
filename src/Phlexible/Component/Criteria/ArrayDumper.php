<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Criteria;

/**
 * Criteria array dumper
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
        $collection = array();

        foreach ($criteria as $criterium) {
            if ($criterium instanceof Criteria) {
                $collection[] = $this->dump($criterium, 1);
            } elseif ($criterium instanceof Criterium) {
                $type = $criterium->getType();
                $value = $criterium->getValue();
                if ($value instanceof \DateTime) {
                    $value = $value->format('Y-m-d H:i:s');
                }
                $collection[] = array('type' => $type, 'value' => $value);
            }
        }

        return array(
            'mode'  => $criteria->getMode(),
            'type'  => 'collection',
            'value' => $collection,
        );
    }
}
