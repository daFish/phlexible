<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Builder;

use Phlexible\Bundle\GuiBundle\Compressor\CompressorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Translations builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TranslationsBuilder
{
    /**
     * @var TranslatorInterface|TranslatorBagInterface
     */
    private $translator;

    /**
     * @var CompressorInterface
     */
    private $javascriptCompressor;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param TranslatorInterface $translator
     * @param CompressorInterface $javascriptCompressor
     * @param string              $cacheDir
     * @param bool                $debug
     */
    public function __construct(
        TranslatorInterface $translator,
        CompressorInterface $javascriptCompressor,
        $cacheDir,
        $debug
    ) {
        $this->translator = $translator;
        $this->javascriptCompressor = $javascriptCompressor;
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    /**
     * Get all translations for the given domain
     *
     * @param string $locale
     * @param string $fallbackLocale
     * @param string $domain
     *
     * @return string
     */
    public function build($locale, $fallbackLocale = 'en', $domain = 'gui')
    {
        $fallbackCatalogue = $this->translator->getCatalogue($fallbackLocale);
        $catalogue = $this->translator->getCatalogue($locale);

        $t = array();

        if ($locale !== $fallbackLocale) {
            $all = $fallbackCatalogue->all($domain);
            foreach ($all as $key => $value) {
                $explodedKey = explode('.', $key);
                $key = array_pop($explodedKey);
                $class = implode('.', $explodedKey);
                $t[$class][$key] = $value;
            }
        }

        $all = $catalogue->all('gui');
        foreach ($all as $key => $value) {
            $explodedKey = explode('.', $key);
            $key = array_pop($explodedKey);
            $class = implode('.', $explodedKey);
            $t[$class][$key] = $value;
        }

        $template = 'Ext.define("%s", %s);';

        $content = '';
        foreach ($t as $class => $values) {
            $values = array('override' => $class) + $values;
            $className = sprintf('Ext.locale.%s.%s', $locale, $class);
            $content .= sprintf($template, $className, json_encode($values, JSON_PRETTY_PRINT)) . PHP_EOL;
        }

        $cacheFilename = $this->cacheDir . '/translations-' . $locale . '.js';

        $filesystem = new Filesystem();
        if (!$filesystem->exists(dirname($cacheFilename))) {
            $filesystem->mkdir(dirname($cacheFilename));
        }

        if (!$this->debug) {
            $content = $this->compress($content);
        }

        file_put_contents($cacheFilename, $content);

        return $cacheFilename;
    }

    /**
     * Javascript-aware compress the input string
     *
     * @param string $script
     *
     * @return string
     */
    private function compress($script)
    {
        return $this->javascriptCompressor->compressString($script);
    }
}
