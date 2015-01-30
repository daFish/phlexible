<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\ProblemBundle\Serializer\Normalizer;

use Phlexible\Bundle\ProblemBundle\Entity\Problem;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

/**
 * Problem normalizer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ProblemNormalizer extends GetSetMethodNormalizer
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
            'lastCheckedAt' => $dateCallback,
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
        return $data instanceof Problem;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === 'Job';
    }

}
