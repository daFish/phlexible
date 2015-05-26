<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaType\File;

use Phlexible\Component\MediaType\Model\MediaTypeCollection;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
use Temp\MimeSniffer\MimeSniffer;

/**
 * Media type manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaTypeManager implements MediaTypeManagerInterface
{
    /**
     * @var PuliLoader
     */
    private $loader;

    /**
     * @var MimeSniffer
     */
    private $mimeSniffer;

    /**
     * @var MediaTypeCollection
     */
    private $mediaTypes;

    /**
     * @param PuliLoader  $loader
     * @param MimeSniffer $mimeSniffer
     */
    public function __construct(PuliLoader $loader, MimeSniffer $mimeSniffer)
    {
        $this->loader = $loader;
        $this->mimeSniffer = $mimeSniffer;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->getCollection()->create();
    }

    /**
     * @return MediaTypeCollection
     */
    public function getCollection()
    {
        if ($this->mediaTypes === null) {
            $this->mediaTypes = $this->loader->loadMediaTypes();
        }

        return $this->mediaTypes;
    }

    /**
     * @return MimeDetector
     */
    public function getMimeDetector()
    {
        return $this->mimeDetector;
    }

    /**
     * {@inheritdoc}
     */
    public function find($key)
    {
        return $this->getCollection()->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->getCollection()->all();
    }

    /**
     * {@inheritdoc}
     */
    public function findByMimetype($mimetype)
    {
        return $this->getCollection()->getByMimetype($mimetype);
    }

    /**
     * {@inheritdoc}
     */
    public function findByFilename($filename)
    {
        $mimetype = $this->mimeSniffer->detect($filename);

        return $this->findByMimetype((string) $mimetype);
    }
}
