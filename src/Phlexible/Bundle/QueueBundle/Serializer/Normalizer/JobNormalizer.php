<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\QueueBundle\Serializer\Normalizer;

use Phlexible\Bundle\QueueBundle\Entity\Job;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

/**
 * Job normalizer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class JobNormalizer extends GetSetMethodNormalizer
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $dateCallback = function($dateTime) {
            return $dateTime instanceof \DateTime
                ? $dateTime->format(\DateTime::ISO8601)
                : null;
        };
        $circularReferenceHandler = function() {
            return null;
        };
        $callbacks = array(
            'createdAt' => $dateCallback,
            'executeAfter' => $dateCallback,
            'startedAt' => $dateCallback,
            'finishedAt' => $dateCallback,
        );
        $ignoredAttributes = array(
        );
        $this->setCallbacks($callbacks);
        //$this->setCircularReferenceHandler($circularReferenceHandler);
        //$this->setCircularReferenceLimit(1);
        $this->setIgnoredAttributes($ignoredAttributes);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Job;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === 'Job';
    }

}
