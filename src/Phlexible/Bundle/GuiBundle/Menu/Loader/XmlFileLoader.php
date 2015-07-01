<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Menu\Loader;

use Phlexible\Bundle\GuiBundle\Menu\MenuItem;
use Phlexible\Bundle\GuiBundle\Menu\MenuItemCollection;

/**
 * XML file loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlFileLoader implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function load($file)
    {
        $config = $this->import($file);

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($file)
    {
        return 'xml' === pathinfo($file, PATHINFO_EXTENSION);
    }

    /**
     * @param string $file
     *
     * @throws LoaderException
     * @return MenuItemCollection
     */
    private function import($file)
    {
        $this->validate($file);

        $xml = simplexml_load_file($file);

        $handlers = new MenuItemCollection();

        foreach ($xml as $itemNode) {
            $attributes = $itemNode->attributes();
            $name = (string) $attributes['name'];
            $handle = (string) $attributes['handle'];
            $parent = (string) $attributes['parent'];

            $parameters = array();
            if ($itemNode->parameters) {
                foreach ($itemNode->parameters->parameter as $parameterNode) {
                    $parameterAttributes = $parameterNode->attributes();
                    $parameterKey = (string) $parameterAttributes['key'];
                    $parameterValue = (string) $parameterNode;
                    $parameters[$parameterKey] = $parameterValue;
                }
            }

            $roles = array();
            $satisfy = 'any';
            if ($itemNode->roles) {
                $rolesAttributes = $itemNode->roles->attributes();
                if (!empty($rolesAttributes['satisfy'])) {
                    $satisfy = (string) $rolesAttributes['satisfy'];
                }
                foreach ($itemNode->roles->role as $roleNode) {
                    $roles[] = (string) $roleNode;
                }
            }

            $handlers->set($name, new MenuItem($handle, $parent, $roles, $satisfy, $parameters));
        }

        return $handlers;
    }

    /**
     * @param $xmlFile
     *
     * @throws \Exception
     */
    private function validate($xmlFile)
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->load($xmlFile);

        if (!$dom->schemaValidate(__DIR__ . '/schema/menu.xsd')) {
            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                if ($error->level >= LIBXML_ERR_ERROR) {
                    throw new LoaderException(
                        "Schema error in {$error->file}, line {$error->line}, column {$error->column} " .
                        "[{$error->level}]: {$error->message}"
                    );
                }
            }
        }
    }
}
