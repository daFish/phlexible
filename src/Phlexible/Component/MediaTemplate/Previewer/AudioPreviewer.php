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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Temp\MediaConverter\Transmuter;

/**
 * Audio preview
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
    public function create($filePath, array $params)
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->cacheDir)) {
            $filesystem->mkdir($this->cacheDir);
        }

        $template = new AudioTemplate();
        $templateKey = 'unknown';
        foreach ($params as $key => $value) {
            if ($key === 'template') {
                $templateKey = $value;
                continue;
            } elseif ($key === '_dc') {
                continue;
            } elseif ($key === 'debug') {
                continue;
            }

            $method = 'set' . $this->toCamelCase($key);
            $template->$method($value);
        }

        $spec = $this->specifier->specify($template);
        $extension = $this->specifier->getExtension($template);
        $cacheFilename = $this->cacheDir . 'preview_audio.' . $extension;
        $this->transmuter->transmute($filePath, $spec, $cacheFilename);

        $file = new File($cacheFilename);

        $debug = json_encode($template->toArray(), JSON_PRETTY_PRINT);

        $data = array(
            'path'     => $cacheFilename,
            'file'     => basename($cacheFilename),
            'size'     => filesize($cacheFilename),
            'template' => $templateKey,
            'format'   => $extension,
            'mimetype' => $file->getMimeType(),
            'debug'    => $debug,
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
        $chunks    = explode('_', $value);
        $ucfirsted = array_map(function($s) { return ucfirst($s); }, $chunks);

        return implode('', $ucfirsted);
    }
}
