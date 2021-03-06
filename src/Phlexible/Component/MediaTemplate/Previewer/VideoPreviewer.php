<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\Previewer;

use FFMpeg\FFProbe;
use Phlexible\Component\MediaCache\Specifier\VideoSpecifier;
use Phlexible\Component\MediaTemplate\Domain\VideoTemplate;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Temp\MediaConverter\Transmuter;

/**
 * Video previewer.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class VideoPreviewer implements PreviewerInterface
{
    /**
     * @var VideoSpecifier
     */
    private $specifier;

    /**
     * @var Transmuter
     */
    private $transmuter;

    /**
     * @var Ffprobe
     */
    private $ffprobe;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param VideoSpecifier $specifier
     * @param Transmuter     $transmuter
     * @param Ffprobe        $ffprobe
     * @param string         $cacheDir
     */
    public function __construct(VideoSpecifier $specifier, Transmuter $transmuter, Ffprobe $ffprobe, $cacheDir)
    {
        $this->specifier = $specifier;
        $this->transmuter = $transmuter;
        $this->ffprobe = $ffprobe;
        $this->cacheDir = $cacheDir;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(TemplateInterface $template)
    {
        return $template instanceof VideoTemplate;
    }

    /**
     * {@inheritdoc}
     */
    public function create(TemplateInterface $template, $filePath)
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->cacheDir)) {
            $filesystem->mkdir($this->cacheDir);
        }

        $spec = $this->specifier->specify($template);
        $extension = $this->specifier->getExtension($template);
        $cacheFilename = $this->cacheDir.'preview_image.'.$extension;
        $this->transmuter->transmute($filePath, $spec, $cacheFilename);

        $file = new File($cacheFilename);

        $videoStream = $this->ffprobe->streams($cacheFilename)->videos()->first();

        $data = array(
            'path' => $cacheFilename,
            'file' => basename($cacheFilename),
            'size' => filesize($cacheFilename),
            'template' => $template->getKey(),
            'format' => $extension,
            'mimetype' => $file->getMimeType(),
            'parameters' => $template->getParameters(),
            'width' => $videoStream->get('width'),
            'height' => $videoStream->get('height'),
        );

        return $data;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function toCamelCase($value)
    {
        $chunks = explode('_', $value);
        $ucfirsted = array_map(function ($s) { return ucfirst($s); }, $chunks);

        return implode('', $ucfirsted);
    }
}
