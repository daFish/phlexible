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
    private $interfaceMap;

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

        $item = $this->createByElementtypeId($elementVersion->getElement()->getElementtypeId());

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
        foreach ($content['values'] as $key => $value) {
            $item->__setValue($key, $value['de']);
        }

        if ($content['children']) {
            foreach ($content['children'] as $childContent) {
                $childItem = $this->createByDsId($childContent['dsId'], !empty($childContent['id']) ? $childContent['id'] : null);
                $this->fill($childContent, $childItem);
                $adder = 'add' . ucfirst($this->toCamelCase($childContent['parent']));
                $item->$adder($childItem);
            }
        }

        return $item;
    }

    /**
     * @param string $elementtypeId
     *
     * @return mixed
     */
    public function createByElementtypeId($elementtypeId)
    {
        $className = $this->elementtypeIdMap[$elementtypeId];
        $filename = $this->fileMap[$className];
        include_once $this->dir . '/' . $filename;

        return new $className();
    }

    /**
     * @param string $dsId
     * @param string $id
     *
     * @return mixed
     */
    public function createByDsId($dsId, $id = null)
    {
        $className = $this->dsIdMap[$dsId];
        $filename = $this->fileMap[$className];
        include_once $this->dir . '/' . $filename;

        return new $className($id);
    }

    /**
     * @param string $str
     * @param bool   $capitaliseFirstChar
     *
     * @return string
     */
    private function toCamelCase($str, $capitaliseFirstChar = true)
    {
        if ($capitaliseFirstChar) {
            $str[0] = strtoupper($str[0]);
        }
        $func = create_function('$c', 'return strtoupper($c[1]);');

        return preg_replace_callback('/_([a-z])/', $func, $str);
    }
}
