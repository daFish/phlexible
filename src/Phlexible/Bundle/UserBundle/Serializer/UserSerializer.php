<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\UserBundle\Serializer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr;
use FOS\UserBundle\Model\UserInterface;
use Phlexible\Bundle\UserBundle\Event\SerializeUserEvent;
use Phlexible\Bundle\UserBundle\UserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * User serializer
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class UserSerializer
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param UserInterface $user
     * @param string        $format
     *
     * @return ArrayCollection
     */
    public function serialize(UserInterface $user, $format = 'json')
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizer = new GetSetMethodNormalizer();
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
        $normalizer->setCallbacks($callbacks);
        //$normalizer->setCircularReferenceHandler($circularReferenceHandler);
        //$normalizer->setCircularReferenceLimit(1);
        $normalizer->setIgnoredAttributes($ignoredAttributes);
        $normalizers = array($normalizer);

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->serialize($user, $format);

        $this->eventDispatcher->dispatch(
            UserEvents::SERIALIZE_USER,
            new SerializeUserEvent($user, $userData)
        );

        return $userData->toArray();
    }
}
