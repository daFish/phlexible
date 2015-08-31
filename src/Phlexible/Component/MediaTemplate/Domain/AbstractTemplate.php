<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\Domain;

use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

/**
 * Abstract template
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Serializer\XmlRoot(name="mediaTemplate")
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\Discriminator(field="type", map={"image": "Phlexible\Component\MediaTemplate\Domain\ImageTemplate", "video": "Phlexible\Component\MediaTemplate\Domain\VideoTemplate", "audio": "Phlexible\Component\MediaTemplate\Domain\AudioTemplate"})
 */
abstract class AbstractTemplate implements TemplateInterface
{
    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Type(name="string")
     * @Serializer\XmlAttribute()
     */
    private $key;

    /**
     * @var bool
     * @Serializer\Expose()
     * @Serializer\Type(name="boolean")
     * @Serializer\XmlAttribute()
     */
    private $cache = false;

    /**
     * @var bool
     * @Serializer\Expose()
     * @Serializer\Type(name="boolean")
     * @Serializer\XmlAttribute()
     */
    private $system = false;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Type(name="string")
     * @Serializer\XmlAttribute()
     */
    private $storage = 'default';

    /**
     * @var int
     * @Serializer\Expose()
     * @Serializer\Type(name="integer")
     * @Serializer\XmlAttribute()
     */
    private $revision = 0;

    /**
     * @var DateTime
     * @Serializer\Expose()
     * @Serializer\Type(name="DateTime")
     * @Serializer\XmlAttribute()
     */
    private $createdAt;

    /**
     * @var DateTime
     * @Serializer\Expose()
     * @Serializer\Type(name="DateTime")
     * @Serializer\XmlAttribute()
     */
    private $modifiedAt;

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * {@inheritdoc}
     */
    public function setCache($cache = true)
    {
        $this->cache = (bool) $cache;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSystem()
    {
        return $this->system;
    }

    /**
     * {@inheritdoc}
     */
    public function setSystem($system = true)
    {
        $this->system = (bool) $system;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * {@inheritdoc}
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * {@inheritdoc}
     */
    public function setRevision($revision)
    {
        $this->revision = $revision;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setModifiedAt(DateTime $modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }
}
