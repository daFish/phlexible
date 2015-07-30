<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\Previewer;

use FFMpeg\FFProbe;
use Phlexible\Component\MediaCache\Specifier\VideoSpecifier;
use Phlexible\Component\MediaTemplate\Domain\VideoTemplate;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Temp\MediaConverter\Transmuter;

/**
 * Video previewer
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
    public function create($filePath, array $params)
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->cacheDir)) {
            $filesystem->mkdir($this->cacheDir);
        }

        $template = new VideoTemplate();
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
        $cacheFilename = $this->cacheDir . 'preview_image.' . $extension;
        $this->transmuter->transmute($filePath, $spec, $cacheFilename);

        $file = new File($cacheFilename);

        $debug = json_encode($template->toArray(), JSON_PRETTY_PRINT);

        $videoStream = $this->ffprobe->streams($cacheFilename)->videos()->first();

        $data = array(
            'path'     => $cacheFilename,
            'file'     => basename($cacheFilename),
            'size'     => filesize($cacheFilename),
            'template' => $templateKey,
            'format'   => $extension,
            'mimetype' => $file->getMimeType(),
            'debug'    => $debug,
            'width'    => $videoStream->get('width'),
            'height'   => $videoStream->get('height'),
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
