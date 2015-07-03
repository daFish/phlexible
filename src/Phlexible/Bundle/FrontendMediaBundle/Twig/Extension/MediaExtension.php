<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Twig\Extension;

use Phlexible\Component\MediaManager\Meta\FileMetaDataManager;
use Phlexible\Component\MediaManager\Meta\FileMetaSetResolver;
use Phlexible\Component\Volume\Model\VolumeManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Twig media extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaExtension extends \Twig_Extension
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var VolumeManagerInterface
     */
    private $volumeManager;

    /**
     * @var FileMetaSetResolver
     */
    private $metaSetResolver;

    /**
     * @var FileMetaDataManager
     */
    private $metaDataManager;

    /**
     * @param RouterInterface        $router
     * @param VolumeManagerInterface $volumeManager
     * @param FileMetaSetResolver    $metaSetResolver
     * @param FileMetaDataManager    $metaDataManager
     */
    public function __construct(
        RouterInterface $router,
        VolumeManagerInterface $volumeManager,
        FileMetaSetResolver $metaSetResolver,
        FileMetaDataManager $metaDataManager
    )
    {
        $this->router = $router;
        $this->volumeManager = $volumeManager;
        $this->metaSetResolver = $metaSetResolver;
        $this->metaDataManager = $metaDataManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('image_url', array($this, 'imageUrl')),
            new \Twig_SimpleFunction('image_path', array($this, 'imagePath')),
            new \Twig_SimpleFunction('icon_url', array($this, 'iconUrl')),
            new \Twig_SimpleFunction('icon_path', array($this, 'iconPath')),
            new \Twig_SimpleFunction('thumbnail_url', array($this, 'thumbnailUrl')),
            new \Twig_SimpleFunction('thumbnail_path', array($this, 'thumbnailPath')),
            new \Twig_SimpleFunction('download_url', array($this, 'downloadUrl')),
            new \Twig_SimpleFunction('download_path', array($this, 'downloadPath')),
            new \Twig_SimpleFunction('fileinfo', array($this, 'fileinfo')),
        );
    }

    /**
     * @param string $image
     *
     * @return string
     */
    public function imageUrl($image)
    {
        if (!$image) {
            return '';
        }

        $parts = explode(';', $image);
        $fileId = $parts[0];
        $fileVersion = 1;
        if (isset($parts[1])) {
            $fileVersion = $parts[1];
        }

        // deliver original file
        $src = $this->router->generate('frontendmedia_inline', array('fileId' => $fileId), UrlGeneratorInterface::ABSOLUTE_URL);

        return $src;
    }

    /**
     * @param string $image
     *
     * @return string
     */
    public function imagePath($image)
    {
        if (!$image) {
            return '';
        }

        $parts = explode(';', $image);
        $fileId = $parts[0];
        $fileVersion = 1;
        if (isset($parts[1])) {
            $fileVersion = $parts[1];
        }

        // deliver original file
        $src = $this->router->generate('frontendmedia_inline', array('fileId' => $fileId), UrlGeneratorInterface::ABSOLUTE_PATH);

        return $src;
    }

    /**
     * @param string $image
     * @param int    $size
     *
     * @return string
     */
    public function iconUrl($image, $size = 16)
    {
        if (!$image) {
            return '';
        }

        $parts = explode(';', $image);
        $fileId = $parts[0];
        $fileVersion = 1;
        if (isset($parts[1])) {
            $fileVersion = $parts[1];
        }

        $src = $this->router->generate('frontendmedia_icon', array('fileId' => $fileId, 'size' => $size), UrlGeneratorInterface::ABSOLUTE_URL);

        return $src;
    }

    /**
     * @param string $image
     * @param int    $size
     *
     * @return string
     */
    public function iconPath($image, $size = 16)
    {
        if (!$image) {
            return '';
        }

        $parts = explode(';', $image);
        $fileId = $parts[0];
        $fileVersion = 1;
        if (isset($parts[1])) {
            $fileVersion = $parts[1];
        }

        $src = $this->router->generate('frontendmedia_icon', array('fileId' => $fileId, 'size' => $size), UrlGeneratorInterface::ABSOLUTE_PATH);

        return $src;
    }

    /**
     * @param string $image
     * @param string $template
     *
     * @return string
     */
    public function thumbnailUrl($image, $template)
    {
        if (!$image || !$template) {
            return '';
        }

        $parts = explode(';', $image);
        $fileId = $parts[0];
        $fileVersion = 1;
        if (isset($parts[1])) {
            $fileVersion = $parts[1];
        }

        $src = $this->router->generate('frontendmedia_thumbnail', array('fileId' => $fileId, 'template' => $template), UrlGeneratorInterface::ABSOLUTE_URL);

        return $src;
    }

    /**
     * @param string $image
     * @param string $template
     *
     * @return string
     */
    public function thumbnailPath($image, $template)
    {
        if (!$image || !$template) {
            return '';
        }

        $parts = explode(';', $image);
        $fileId = $parts[0];
        $fileVersion = 1;
        if (isset($parts[1])) {
            $fileVersion = $parts[1];
        }

        $src = $this->router->generate('frontendmedia_thumbnail', array('fileId' => $fileId, 'template' => $template), UrlGeneratorInterface::ABSOLUTE_PATH);

        return $src;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function downloadUrl($file)
    {
        if (!$file) {
            return '';
        }

        $parts = explode(';', $file);
        $fileId = $parts[0];
        $fileVersion = 1;
        if (isset($parts[1])) {
            $fileVersion = $parts[1];
        }

        $src = $this->router->generate('frontendmedia_download', array('fileId' => $fileId), UrlGeneratorInterface::ABSOLUTE_URL);

        return $src;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function downloadPath($file)
    {
        if (!$file) {
            return '';
        }

        $parts = explode(';', $file);
        $fileId = $parts[0];
        $fileVersion = 1;
        if (isset($parts[1])) {
            $fileVersion = $parts[1];
        }

        $src = $this->router->generate('frontendmedia_download', array('fileId' => $fileId), UrlGeneratorInterface::ABSOLUTE_PATH);

        return $src;
    }

    /**
     * @param string $file
     *
     * @return array
     */
    public function fileinfo($file)
    {
        if (!$file) {
            return array();
        }

        $parts = explode(';', $file);
        $fileId = $parts[0];
        $fileVersion = 1;
        if (isset($parts[1])) {
            $fileVersion = $parts[1];
        }

        $volume = $this->volumeManager->getByFileId($fileId, $fileVersion);
        $file = $volume->findFile($fileId, $fileVersion);

        $info = array(
            'name'          => $file->getName(),
            'mimetype'      => $file->getMimeType(),
            'mediaCategory' => $file->getMediaCategory(),
            'mediaType'     => $file->getMediaType(),
            'size'          => $file->getSize(),
            'attributes'    => $file->getAttributes(),
            'createdAt'     => $file->getCreatedAt(),
            'modifiedAt'    => $file->getModifiedAt(),
            'meta'          => array(),
        );

        $metasets = $this->metaSetResolver->resolve($file);
        foreach ($metasets as $metaset) {
            $metadata = $this->metaDataManager->findByMetaSetAndFile($metaset, $file);
            $data = array();
            foreach ($metaset->getFields() as $field) {
                $value = '';
                if ($metadata) {
                    $value = $metadata->get($field->getName(), 'de');
                }
                $data[$field->getName()] = $value;
            }
            $info['meta'][$metaset->getName()] = $data;
        }

        return $info;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_media';
    }
}
