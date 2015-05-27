<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\File\Loader;

use Phlexible\Component\MediaTemplate\Exception\InvalidClassException;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

/**
 * XML loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlLoader implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getExtension()
    {
        return 'xml';
    }

    /**
     * {@inheritdoc}
     */
    public function load($file)
    {
        $xml = simplexml_load_file($file);

        $xmlAttributes = $xml->attributes();
        $key = (string) $xmlAttributes['key'];
        $type = (string) $xmlAttributes['type'];
        $class = (string) $xmlAttributes['class'];
        $cache = (bool) (string) $xmlAttributes['cache'];
        $system = (bool) (string) $xmlAttributes['system'];
        $revision = (int) $xmlAttributes['revision'];

        if (!class_exists($class)) {
            throw new InvalidClassException("Invalid template class $class");
        }

        /* @var $template TemplateInterface */
        $template = new $class();
        $template
            ->setKey($key)
            ->setType($type)
            ->setCache($cache)
            ->setSystem($system)
            ->setRevision($revision)
            ->setCreatedAt(\DateTime::createFromFormat('U', filectime($file)))
            ->setModifiedAt(\DateTime::createFromFormat('U', filemtime($file)));

        foreach ($xml as $parameterNode) {
            $parameterNodeAttributes = $parameterNode->attributes();
            $key = (string) $parameterNodeAttributes['key'];
            $value = (string) $parameterNode;
            $method = 'set' . $this->toCamelCase($key);

            $template->$method($value);
        }

        return $template;
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
