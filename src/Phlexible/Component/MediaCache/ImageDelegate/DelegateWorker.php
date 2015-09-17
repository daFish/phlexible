<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\ImageDelegate;

use Phlexible\Bundle\MediaTypeBundle\Icon\IconResolver;
use Phlexible\Component\MediaCache\Exception\CreateDelegateFailedException;
use Phlexible\Component\MediaCache\Specifier\ImageSpecifier;
use Phlexible\Component\MediaTemplate\Domain\ImageTemplate;
use Phlexible\Component\MediaTemplate\Model\TemplateManagerInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Temp\MediaClassifier\MediaClassifier;
use Temp\MediaClassifier\Model\MediaType;
use Temp\MediaConverter\Transmuter;

/**
 * Delegate worker.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegateWorker
{
    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * @var MediaClassifier
     */
    private $mediaClassifier;

    /**
     * @var Transmuter
     */
    private $transmuter;

    /**
     * @var IconResolver
     */
    private $iconResolver;

    /**
     * @var FileLocatorInterface
     */
    private $locator;

    /**
     * @var string
     */
    private $delegateDirClean;

    /**
     * @var string
     */
    private $delegateDirWaiting;

    /**
     * @param TemplateManagerInterface $templateManager
     * @param MediaClassifier          $mediaClassifier
     * @param Transmuter               $transmuter
     * @param IconResolver             $iconResolver
     * @param FileLocatorInterface     $locator
     * @param string                   $delegateDir
     */
    public function __construct(
        TemplateManagerInterface $templateManager,
        MediaClassifier $mediaClassifier,
        Transmuter $transmuter,
        IconResolver $iconResolver,
        FileLocatorInterface $locator,
        $delegateDir
    ) {
        $this->templateManager = $templateManager;
        $this->mediaClassifier = $mediaClassifier;
        $this->transmuter = $transmuter;
        $this->iconResolver = $iconResolver;
        $this->locator = $locator;

        $this->delegateDirClean = $delegateDir.'clean/';
        $this->delegateDirWaiting = $delegateDir.'waiting/';
    }

    /**
     * @param bool     $force
     * @param callable $callback
     */
    public function writeAll($force = false, callable $callback = null)
    {
        $templates = $this->templateManager->findBy(array('type' => 'image'));
        $mediaTypes = $this->mediaClassifier->getCollection();

        $cnt = count($templates) * count($mediaTypes);

        if (is_callable($callback)) {
            call_user_func($callback, 'count', $cnt);
        }

        foreach ($templates as $template) {
            foreach ($mediaTypes->all() as $mediaType) {
                $this->write($mediaType, $template, $force);

                if (is_callable($callback)) {
                    call_user_func($callback, 'update', $template->getKey(), $mediaType->getName());
                }
            }
        }
    }

    /**
     * @param \Phlexible\Component\MediaTemplate\Domain\ImageTemplate $template
     *
     * @return string
     */
    public function getCleanDir(ImageTemplate $template)
    {
        return $this->delegateDirClean.$template->getKey().'/';
    }

    /**
     * @param \Phlexible\Component\MediaTemplate\Domain\ImageTemplate $template
     *
     * @return string
     */
    public function getWaitingDir(ImageTemplate $template)
    {
        return $this->delegateDirWaiting.$template->getKey().'/';
    }

    /**
     * @param \Phlexible\Component\MediaTemplate\Domain\ImageTemplate $template
     * @param MediaType                                               $mediaType
     *
     * @return string
     */
    public function getCleanFilename(ImageTemplate $template, MediaType $mediaType)
    {
        return $this->getCleanDir($template).$mediaType->getName().'.gif';
    }

    /**
     * @param \Phlexible\Component\MediaTemplate\Domain\ImageTemplate $template
     * @param MediaType                                               $mediaType
     *
     * @return string
     */
    public function getWaitingFilename(ImageTemplate $template, MediaType $mediaType)
    {
        return $this->getWaitingDir($template).$mediaType->getName().'.gif';
    }

    /**
     * @param MediaType $mediaType
     *
     * @return string
     */
    public function getFilename(MediaType $mediaType)
    {
        return $mediaType->getName().'.gif';
    }

    /**
     * @param MediaType                                               $mediaType
     * @param \Phlexible\Component\MediaTemplate\Domain\ImageTemplate $template
     * @param bool                                                    $force
     *
     * @throws CreateDelegateFailedException
     */
    public function write(MediaType $mediaType, ImageTemplate $template, $force = false)
    {
        $templateModifyTime = $template->getModifiedAt()->format('U');

        $templateWidth = $template->getWidth();
        if (!$templateWidth) {
            $templateWidth = 256;
        }
        $icon = $this->iconResolver->resolve($mediaType, $templateWidth);

        if (!$icon || !file_exists($icon)) {
            return;
        }

        $filePathClean = $this->getCleanFilename($template, $mediaType);
        $filePathWaiting = $this->getWaitingFilename($template, $mediaType);

        $dirClean = dirname($filePathClean);
        $dirWaiting = dirname($filePathWaiting);

        $filesystem = new Filesystem();

        if (!$filesystem->exists($dirClean)) {
            $filesystem->mkdir($dirClean);
        }
        if (!$filesystem->exists($dirWaiting)) {
            $filesystem->mkdir($dirWaiting);
        }

        if ($force || !$filesystem->exists($filePathClean)
                || !filesize($filePathClean)
                || filemtime($filePathClean) < $templateModifyTime) {
            $specifier = new ImageSpecifier();
            $spec = $specifier->specify($template);
            $this->transmuter->transmute($icon, $spec, $filePathClean);

            if (!$filesystem->exists($filePathClean)) {
                throw new CreateDelegateFailedException('"Clean" delegate image not created: '.$filePathClean);
            }
        }

        if ($force || !$filesystem->exists($filePathWaiting)
                || !filesize($filePathWaiting)
                || filemtime($filePathWaiting) < $templateModifyTime) {
            if (substr($filePathClean, 0, 3) === 'jpg') {
                $source = imagecreatefromjpeg($filePathClean);
            } elseif (substr($filePathClean, 0, 3) === 'png') {
                $source = imagecreatefrompng($filePathClean);
            } else {
                $source = imagecreatefromgif($filePathClean);
            }

            $sx = imagesx($source);
            $sy = imagesy($source);
            $target = imagecreatetruecolor($sx, $sy);
            imagealphablending($target, true);
            imagesavealpha($target, true);
            $transparent = imagecolorallocatealpha($target, 255, 255, 255, 127);
            imagefilledrectangle($target, 0, 0, $sx, $sy, $transparent);
            imagecopy($target, $source, 0, 0, 0, 0, $sx, $sy);
            $waiting = imagecreatefrompng(
                $this->locator->locate('@PhlexibleMediaCacheBundle/Resources/public/images/waiting.png')
            );
            $sx -= 16;
            $sy -= 16;
            imagecopy($target, $waiting, $sx, $sy, 0, 0, 16, 16);
            // $red   = imagecolorallocate($target, 235, 235, 235);
            // imagerectangle($target, $sx - 0, $sy - 1, $sx + 15, $sy + 16, $red);
            imagegif($target, $filePathWaiting);
            imagedestroy($source);
            imagedestroy($target);
            imagedestroy($waiting);

            if (!$filesystem->exists($filePathWaiting)) {
                throw new CreateDelegateFailedException('"Waiting" delegate image not created: '.$filePathWaiting);
            }
        }
    }
}
