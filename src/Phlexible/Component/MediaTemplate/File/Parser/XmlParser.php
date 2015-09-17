<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\File\Parser;

use Phlexible\Component\MediaTemplate\Exception\InvalidClassException;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

/**
 * XML loader.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlParser implements ParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse($content)
    {
        $xml = simplexml_load_string($content);

        $xmlAttributes = $xml->attributes();
        $key = (string) $xmlAttributes['key'];
        $class = (string) $xmlAttributes['class'];
        $cache = (bool) (string) $xmlAttributes['cache'];
        $system = (bool) (string) $xmlAttributes['system'];
        $revision = (int) $xmlAttributes['revision'];
        $createdAt = isset($xmlAttributes['createdAt']) ? new \DateTime((string) $xmlAttributes['createdAt']) : null;
        $createdUser = isset($xmlAttributes['createUser']) ? (string) $xmlAttributes['createUser'] : null;
        $modifiedAt = isset($xmlAttributes['modifiedAt']) ? new \DateTime((string) $xmlAttributes['modifiedAt']) : null;
        $modifyUser = isset($xmlAttributes['modifyUser']) ? (string) $xmlAttributes['modifyUser'] : null;

        if (!class_exists($class)) {
            throw new InvalidClassException("Invalid template class $class");
        }

        /* @var $template TemplateInterface */
        $template = new $class();
        $template
            ->setKey($key)
            ->setCache($cache)
            ->setSystem($system)
            ->setRevision($revision);

        if ($createdAt) {
            $template->setCreatedAt($createdAt);
        }
        if ($createdUser) {
            $template->setCreateUser($createdUser);
        }
        if ($modifiedAt) {
            $template->setModifiedAt($modifiedAt);
        }
        if ($modifyUser) {
            $template->setModifyUser($modifyUser);
        }

        foreach ($xml as $parameterNode) {
            $parameterNodeAttributes = $parameterNode->attributes();
            $key = (string) $parameterNodeAttributes['key'];
            $value = (string) $parameterNode;
            $method = 'set'.$this->toCamelCase($key);

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
        $chunks = explode('_', $value);
        $ucfirsted = array_map(function ($s) { return ucfirst($s); }, $chunks);

        return implode('', $ucfirsted);
    }
}
