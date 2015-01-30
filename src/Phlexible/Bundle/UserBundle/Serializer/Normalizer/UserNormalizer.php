<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\UserBundle\Serializer\Normalizer;

use Phlexible\Bundle\UserBundle\Entity\User;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

/**
 * User normalizer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UserNormalizer extends GetSetMethodNormalizer
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
            'modifiedAt' => $dateCallback,
            'expiresAt' => $dateCallback,
            'passwordRequestedAt' => $dateCallback,
            'lastLogin' => $dateCallback,
        );
        $ignoredAttributes = array(
            'groups',
            'password',
            'plainPassword',
            'salt',
            'interfaceLanguage',
            'contentLanguage',
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
        return $data instanceof User;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === 'Phlexible\Bundle\UserBundle\Entity\User';
    }

}
