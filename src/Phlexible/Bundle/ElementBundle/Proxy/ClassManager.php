<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Proxy;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;

/**
 * Class manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ClassManager
{
    /**
     * @var string
     */
    private $baseDir;

    /**
     * @var array
     */
    private $elementtypeIdMap;

    /**
     * @var array
     */
    private $names;

    /**
     * @var array
     */
    private $dsIdMap;

    /**
     * @param string $baseDir
     * @param array  $names
     * @param array  $elementtypeIdMap
     * @param array  $dsIdMap
     */
    public function __construct($baseDir, array $names, array $elementtypeIdMap, array $dsIdMap)
    {
        $this->baseDir = $baseDir;
        $this->names = $names;
        $this->elementtypeIdMap = $elementtypeIdMap;
        $this->dsIdMap = $dsIdMap;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function containsName($name)
    {
        return in_array($name, $this->names);
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function containsId($id)
    {
        return isset($this->elementtypeIdMap[$id]);
    }

    /**
     * @param string $dsId
     *
     * @return bool
     */
    public function containsDsId($dsId)
    {
        return isset($this->dsIdMap[$dsId]);
    }

    /**
     * @param ElementVersion $elementVersion
     *
     * @return mixed
     */
    public function create(ElementVersion $elementVersion)
    {
        $content = $elementVersion->getContent();

        $item = $this->createByElementVersion($elementVersion);

        if ($content) {
            $this->fill($content, $item);
        }

        return $item;
    }

    /**
     * @param array $content
     * @param mixed $item
     *
     * @return mixed
     */
    private function fill($content, $item)
    {
        $values = array();
        foreach ($content['values'] as $key => $value) {
            $values[$key] = $value['de'];
        }
        $item->__setValues($values);

        if ($content['collections']) {
            $children = array();
            foreach ($content['collections'] as $name => $collection) {
                foreach ($collection as $child) {
                    $children[$name][] = $childItem = $this->createByDsId(
                        $child['dsId'],
                        !empty($child['id']) ? $child['id'] : null
                    );
                    $this->fill($child, $childItem);
                }
            }
            $item->__setChildren($children);
        }

        return $item;
    }

    /**
     * @param ElementVersion $elementVersion
     *
     * @return MainStructureInterface
     * @throws \Exception
     */
    private function createByElementVersion(ElementVersion $elementVersion)
    {
        $elementtypeId = $elementVersion->getElement()->getElementtypeId();

        if (!isset($this->names[$elementtypeId])) {
            throw new \Exception("Elementtype ID $elementtypeId not found in map.");
        }

        $className = $this->elementtypeIdMap[$elementtypeId]['classname'];
        $filename = $this->elementtypeIdMap[$elementtypeId]['filename'];

        require_once $this->baseDir . '/' . $filename;

        return new $className($elementVersion->getElement()->getEid(), $elementVersion->getVersion());
    }

    /**
     * @param string $dsId
     * @param string $id
     *
     * @return ChildStructureInterface
     * @throws \Exception
     */
    private function createByDsId($dsId, $id = null)
    {
        if (!isset($this->dsIdMap[$dsId])) {
            throw new \Exception("dsId $dsId not found in map.");
        }

        $className = $this->dsIdMap[$dsId]['classname'];
        $filename = $this->dsIdMap[$dsId]['filename'];

        require_once $this->baseDir . '/' . $filename;

        return new $className($id);
    }
}
