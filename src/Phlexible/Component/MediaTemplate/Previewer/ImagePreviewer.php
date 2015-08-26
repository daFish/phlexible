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

use Phlexible\Component\MediaCache\Specifier\ImageSpecifier;
use Phlexible\Component\MediaTemplate\Domain\ImageTemplate;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Temp\MediaConverter\Transmuter;

/**
 * Image previewer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ImagePreviewer implements PreviewerInterface
{
    /**
     * @var ImageSpecifier
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
     * @param ImageSpecifier $specifier
     * @param Transmuter     $transmuter
     * @param string         $cacheDir
     */
    public function __construct(ImageSpecifier $specifier, Transmuter $transmuter, $cacheDir)
    {
        $this->specifier = $specifier;
        $this->transmuter = $transmuter;
        $this->cacheDir = $cacheDir;
    }

    /**
     * {@inheritdoc}
     */
    public function create($filePath,  array $params)
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->cacheDir)) {
            $filesystem->mkdir($this->cacheDir);
        }

        $template = new ImageTemplate();
        $templateKey = 'unknown';
        foreach ($params as $key => $value) {
            if ($key === 'xmethod') {
                $key = 'method';
            } elseif ($key === 'backgroundcolor' && !preg_match('/^\#[0-9A-Za-z]{6}$/', $value)) {
                $value = '';
            } elseif ($key === 'template') {
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
        $cacheFilename = $this->cacheDir . 'preview_image.' . $extension;
        $this->transmuter->transmute($filePath, $spec, $cacheFilename);

        $file = new File($cacheFilename);

        $size = getimagesize($cacheFilename);

        $debug = json_encode($template->toArray(), JSON_PRETTY_PRINT);

        $data = array(
            'path'     => $cacheFilename,
            'file'     => basename($cacheFilename),
            'size'     => filesize($cacheFilename),
            'template' => $templateKey,
            'format'   => $extension,
            'mimetype' => $file->getMimeType(),
            'debug'    => $debug,
            'width'    => $size[0],
            'height'   => $size[1],
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
