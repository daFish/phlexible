<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
    private $dir;

    /**
     * @var array
     */
    private $fileMap;

    /**
     * @var array
     */
    private $elementtypeIdMap;

    /**
     * @var array
     */
    private $dsIdMap;

    /**
     * @param string $dir
     * @param array  $interfaceMap
     * @param array  $fileMap
     * @param array  $elementtypeIdMap
     * @param array  $dsIdMap
     */
    public function __construct($dir, array $interfaceMap, array $fileMap, array $elementtypeIdMap, array $dsIdMap)
    {
        $this->dir = $dir;
        $this->fileMap = $fileMap;
        $this->elementtypeIdMap = $elementtypeIdMap;
        $this->dsIdMap = $dsIdMap;

        foreach ($interfaceMap as $interfaceName => $filename) {
            include_once $dir . '/' . $filename;
        }
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

        if ($content['children']) {
            $children = array();
            foreach ($content['children'] as $childContent) {
                $children[$childContent['parent']][] = $childItem = $this->createByDsId($childContent['dsId'], !empty($childContent['id']) ? $childContent['id'] : null);
                $this->fill($childContent, $childItem);
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
    public function createByElementVersion(ElementVersion $elementVersion)
    {
        $elementtypeId = $elementVersion->getElement()->getElementtypeId();

        if (!isset($this->elementtypeIdMap[$elementtypeId])) {
            throw new \Exception("Elementtype ID $elementtypeId not found in map.");
        }

        $className = $this->elementtypeIdMap[$elementtypeId];
        $filename = $this->fileMap[$className];
        include_once $this->dir . '/' . $filename;

        return new $className($elementVersion->getElement()->getEid(), $elementVersion->getVersion());
    }

    /**
     * @param string $dsId
     * @param string $id
     *
     * @return ChildStructureInterface
     * @throws \Exception
     */
    public function createByDsId($dsId, $id = null)
    {
        if (!isset($this->dsIdMap[$dsId])) {
            throw new \Exception("dsId $dsId not found in map.");
        }

        $className = $this->dsIdMap[$dsId];
        $filename = $this->fileMap[$className];
        include_once $this->dir . '/' . $filename;

        return new $className($id);
    }
}
