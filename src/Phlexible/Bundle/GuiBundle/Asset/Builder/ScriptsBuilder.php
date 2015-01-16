<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Builder;

use Doctrine\Common\Collections\ArrayCollection;
use Phlexible\Bundle\GuiBundle\Asset\Cache\ResourceCollectionCache;
use Phlexible\Bundle\GuiBundle\Compressor\CompressorInterface;
use Puli\Discovery\Api\Binding\ResourceBinding;
use Puli\Discovery\Api\ResourceDiscovery;
use Puli\Repository\Api\ResourceCollection;
use Puli\Repository\Api\ResourceRepository;
use Puli\Repository\Resource\DirectoryResource;
use Puli\Repository\Resource\FileResource;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Scripts builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ScriptsBuilder
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var ResourceDiscovery
     */
    private $puliDiscovery;

    /**
     * @var CompressorInterface
     */
    private $compressor;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param ResourceDiscovery   $puliDiscovery
     * @param CompressorInterface $compressor
     * @param string              $cacheDir
     * @param bool                $debug
     */
    public function __construct(
        ResourceDiscovery $puliDiscovery,
        CompressorInterface $compressor,
        $cacheDir,
        $debug)
    {
        $this->puliDiscovery = $puliDiscovery;
        $this->compressor = $compressor;
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    /**
     * Get all javascripts for the given section
     *
     * @return string
     */
    public function build()
    {
        $cache = new ResourceCollectionCache($this->cacheDir . '/gui.js', $this->debug);

        $bindings = $this->findBindings();

        if (!$cache->isFresh($bindings)) {
            $content = $this->buildScripts($bindings);

            $cache->write($content);

            if (!$this->debug) {
                $this->compressor->compressFile((string) $cache);
            }

        }

        return file_get_contents((string) $cache);
    }

    /**
     * @return ResourceBinding[]
     */
    private function findBindings()
    {
        return $this->puliDiscovery->find('phlexible/scripts');
    }

    /**
     * @param ResourceBinding[] $bindings
     *
     * @return string
     */
    private function buildScripts(array $bindings)
    {
        $entryPoints = array();

        foreach ($bindings as $binding) {
            foreach ($binding->getResources() as $resource) {
                if (preg_match('#^/phlexible/[a-z0-9\-_.]+/scripts/[A-Za-z0-9\-_.]+\.js$#', $resource->getPath())) {
                    $entryPoints[$resource->getPath()] = $resource->getFilesystemPath();
                }
            }
        }

        //ldd($entryPoints);
        /*
        $dir = $this->puliRepository->get('/phlexible/scripts');
        foreach ($dir->listChildren() as $dir) {
            //if ($dir->getName() !== 'phlexiblegui') continue;
            foreach ($dir->listChildren() as $resource) {
                if ($resource instanceof FileResource && substr($resource->getName(), -3) === '.js') {
                    $entryPoints[$resource->getPath()] = $resource->getFilesystemPath();
                }
            }
        }
        */

        $files = array();
        foreach ($bindings as $binding) {
            foreach ($binding->getResources() as $resource) {
                /* @var $resource FileResource */

                $body = $resource->getBody();

                $file = new \stdClass();
                $file->path = $resource->getPath();
                $file->file = $resource->getFilesystemPath();
                $file->requires = array();
                $file->provides = array();

                if (preg_match_all('/Ext\.define\(\s*["\'](.+)["\']/', $body, $matches)) {
                    foreach ($matches[1] as $provide) {
                        $file->provides[] = $provide;
                    }
                }

                if (preg_match_all('/alias:\s*["\']widget\.(.+)["\']/', $body, $matches)) {
                    foreach ($matches[1] as $provide) {
                        $file->provides[] = $provide;
                    }
                }

                if (preg_match_all('/Ext\.require\(["\'](.+)["\']\)/', $body, $matches)) {
                    foreach ($matches[1] as $require) {
                        $file->requires[] = $require;
                    }
                }

                if (preg_match_all('/Ext\.create\(\s*["\'](.+)["\']/', $body, $matches)) {
                    foreach ($matches[1] as $require) {
                        $file->requires[] = $require;
                    }
                }

                if (preg_match_all('/extend:\s*["\'](.+)["\']/', $body, $matches)) {
                    foreach ($matches[1] as $require) {
                        $file->requires[] = $require;
                    }
                }

                if (preg_match_all('/xtype:\s*["\'](.+)["\']/', $body, $matches)) {
                    foreach ($matches[1] as $require) {
                        $file->requires[] = $require;
                    }
                }

                if (preg_match_all('/window:\s*["\'](.+)["\']/', $body, $matches)) {
                    foreach ($matches[1] as $require) {
                        $file->requires[] = $require;
                    }
                }

                if (preg_match_all('/model:\s*["\'](.+)["\']/', $body, $matches)) {
                    foreach ($matches[1] as $require) {
                        $file->requires[] = $require;
                    }
                }

                if (preg_match_all('/defaultType:\s*["\'](.+)["\']/', $body, $matches)) {
                    foreach ($matches[1] as $require) {
                        $file->requires[] = $require;
                    }
                }

                if (preg_match_all('/requires:\s*\[(["\'].+["\'])\]/m', $body, $matches)) {
                    foreach ($matches[1] as $require) {
                        $parts = explode(', ', $require);
                        foreach ($parts as $part) {
                            $file->requires[] = trim($part, " \t\n\r\0\"'");
                        }

                    }
                }

                $files[$resource->getPath()] = $file;
            }
        }

        $entryPointFiles = array_intersect_key($files, $entryPoints);

        $symbols = array();
        foreach ($files as $file) {
            foreach ($file->provides as $provide) {
                $symbols[$provide] = $file;
            }
        }

        $results = new ArrayCollection();

        function addToResult($file, ArrayCollection $results, array $symbols)
        {
            if (!empty($file->added)) {
                return;
            }

            $file->added = true;

            $skip = array(
                'buttongroup',
                'tabpanel',
                'box',
                'panel',
                'textfield',
                'checkbox',
                'button',
                'toolbar',
                'component',
                'dataview',
                'form',
                'numberfield',
                'radio',
                'actioncolumn',
                'checkcolumn',
                'fieldcontainer',
                'grid',
                'combo',
                'combobox',
                'checkboxgroup',
                'pagingtoolbar',
                'splitbutton',
                'fieldset',
                'tbtext',
                'propertygrid',
                'cycle',
                'progressbar',
                'slider',
                'breadcrumb',
                'container',
                'label',
                'textarea',
                'datecolumn',
                'displayfield',
            );

            if (!empty($file->requires)) {
                foreach ($file->requires as $require) {
                    if ((substr($require, 0, 4) === 'Ext.' && substr($require, 0, 7) !== 'Ext.ux.') || in_array($require, $skip)) {
                        continue;
                    }
                    if (!isset($symbols[$require])) {
                        throw new \Exception("Symbol '$require' not found for file {$file->file}.");
                    }
                    addToResult($symbols[$require], $results, $symbols);
                }
            }

            $results->set($file->path, $file->file);
        };

        foreach ($entryPointFiles as $file) {
        //foreach ($files as $file) {
            addToResult($file, $results, $symbols);
        }

        $file = $files['/phlexible/phlexiblemediamanager/scripts/ux/plupload.full.min.js'];
        $file->added = true;
        $results->set($file->path, $file->file);

        $unusedPaths = array();
        foreach ($files as $path => $file) {
            if (empty($file->added)) {
                $unusedPaths[] = $path;
            }
        }

        $scripts = '/* Created: ' . date('Y-m-d H:i:s');
        if ($this->debug) {
            $scripts .= PHP_EOL . ' * ' . PHP_EOL . ' * Unused paths:' . PHP_EOL . ' * ' .
                implode(PHP_EOL . ' * ', $unusedPaths) . PHP_EOL;
        }
        $scripts .= ' */';
        foreach ($results as $path => $file) {
            if ($this->debug) {
                $scripts .= PHP_EOL . "/* Resource: $path */" . PHP_EOL;
            }
            $scripts .= file_get_contents($file);
        }

        return $scripts;
    }
}
