<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Builder;

use Phlexible\Bundle\GuiBundle\Compressor\CompressorInterface;
use Phlexible\Bundle\GuiBundle\Translator\CatalogAccessor;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Translations builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TranslationsBuilder
{
    /**
     * @var CatalogAccessor
     */
    private $catalogAccessor;

    /**
     * @var CompressorInterface
     */
    private $javascriptCompressor;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param CatalogAccessor     $catalogAccessor
     * @param CompressorInterface $javascriptCompressor
     * @param string              $cacheDir
     */
    public function __construct(
        CatalogAccessor $catalogAccessor,
        CompressorInterface $javascriptCompressor,
        $cacheDir)
    {
        $this->catalogAccessor = $catalogAccessor;
        $this->javascriptCompressor = $javascriptCompressor;
        $this->cacheDir = $cacheDir;
    }

    /**
     * Get all Translations for the given section
     *
     * @param string $locale
     * @param string $fallbackLanguage
     * @param string $domain
     *
     * @return string
     */
    public function build($locale, $fallbackLocale = 'en', $domain = 'gui')
    {
        $fallbackCatalogue = $this->catalogAccessor->getCatalogues($fallbackLocale);
        $catalogue = $this->catalogAccessor->getCatalogues($locale);

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

        file_put_contents($cacheFilename, $content);

        return $cacheFilename;

        $translations = [];
        $catalogue = $this->catalogAccessor->getCatalogues($language);
        $namespaces = [];
        foreach ($catalogue->all($domain) as $key => $value) {
            $parts = explode('.', $key);
            $component = array_shift($parts);
            $namespace = 'Phlexible.' . strtolower($component) . '.Strings';
            if (count($parts) > 1) {
                $key1 = array_shift($parts);
                $key2 = array_shift($parts);
                $namespaces[$namespace][$key1][$key2] = $value;
            } else {
                $key = array_shift($parts);
                $namespaces[$namespace][$key] = $value;
            }
        }
        foreach ($namespaces as $namespace => $keys) {
            $translations[$namespace] = $keys;
        }

        $cacheFilename = $this->cacheDir . '/translations-' . $language . '.js';

        $filesystem = new Filesystem();
        if (!$filesystem->exists(dirname($cacheFilename))) {
            $filesystem->mkdir(dirname($cacheFilename));
        }

        $content = $this->buildTranslations($translations);
        file_put_contents($cacheFilename, $content);

        return $cacheFilename;
    }

    /**
     * Glue together all scripts and return file/memory stream
     *
     * @param array $languages
     *
     * @return string
     */
    private function buildTranslations(array $languages)
    {
        $namespaces = [];

        $content = '';
        foreach ($languages as $namespace => $page) {
            $parentNamespace = explode('.', $namespace);
            array_pop($parentNamespace);
            $parentNamespace = implode('.', $parentNamespace);

            if (!in_array($parentNamespace, $namespaces)) {
                $content .= 'Ext.namespace("' . $parentNamespace . '");' . PHP_EOL;
                $namespaces[] = $parentNamespace;
            }

            $content .= $namespace . ' = ' . json_encode($page) . ';' . PHP_EOL;
            $content .= $namespace . '.get = function(s){return this[s]};' . PHP_EOL;
        }

        $content = $this->compress($content);

        return $content;
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
