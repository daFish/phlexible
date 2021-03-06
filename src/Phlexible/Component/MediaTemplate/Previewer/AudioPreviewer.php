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

use Phlexible\Component\MediaCache\Specifier\AudioSpecifier;
use Phlexible\Component\MediaTemplate\Domain\AudioTemplate;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Temp\MediaConverter\Transmuter;

/**
 * Audio preview.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AudioPreviewer implements PreviewerInterface
{
    /**
     * @var AudioSpecifier
     */
    private $specifier;

    /**
     * @var Transmuter
     */
    private $transmuter;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param AudioSpecifier $specifier
     * @param Transmuter     $transmuter
     * @param string         $cacheDir
     */
    public function __construct(AudioSpecifier $specifier, Transmuter $transmuter, $cacheDir)
    {
        $this->specifier = $specifier;
        $this->transmuter = $transmuter;
        $this->cacheDir = $cacheDir;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(TemplateInterface $template)
    {
        return $template instanceof AudioTemplate;
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
        $cacheFilename = $this->cacheDir.'preview_audio.'.$extension;
        $this->transmuter->transmute($filePath, $spec, $cacheFilename);

        $file = new File($cacheFilename);

        $data = array(
            'path' => $cacheFilename,
            'file' => basename($cacheFilename),
            'size' => filesize($cacheFilename),
            'template' => $template->getKey(),
            'format' => $extension,
            'mimetype' => $file->getMimeType(),
            'parameters' => $template->getParameters(),
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
