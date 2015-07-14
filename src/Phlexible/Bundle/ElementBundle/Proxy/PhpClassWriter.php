<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy;

use CG\Core\DefaultGeneratorStrategy;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Php class writer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhpClassWriter
{
    /**
     * @var string
     */
    private $outputDir;

    /**
     * @param string $outputDir
     */
    public function __construct($outputDir)
    {
        $this->outputDir = $outputDir;
    }

    /**
     * @param PhpClassMap $classMap
     *
     * @return array
     */
    public function write(PhpClassMap $classMap)
    {
        $filesystem = new Filesystem();
        $strategy = new DefaultGeneratorStrategy();

        $interfaces = array();
        $classes = array();
        $elementtypeIds = array();
        $dsIds = array();
        foreach ($classMap->all() as $class) {
            $relativeName = str_replace('\\', '/', str_replace(PhpClassGenerator::NS_PREFIX, '', $class->getName())) . '.php';
            $filename = $this->outputDir . '/' . $relativeName;
            $body = $strategy->generate($class);
            $filesystem->dumpFile($filename, "<?php\n\n$body");
            if ($class->getAttributeOrElse('interface', false)) {
                $interfaces[$class->getName()] = $relativeName;
            } else {
                $classes[$class->getName()] = $relativeName;
            }
            if ($elementtypeId = $class->getAttributeOrElse('elementtypeId', false)) {
                $elementtypeIds[$elementtypeId] = $class->getName();
            }
            if ($dsId = $class->getAttributeOrElse('dsId', false)) {
                $dsIds[$dsId] = $class->getName();
            }
        }

        $interfaceMap = var_export($interfaces, true);
        $classMap = var_export($classes, true);
        $elementtypeIdMap = var_export($elementtypeIds, true);
        $dsIdMap = var_export($dsIds, true);
        $content = <<<EOF
<?php

return new \\Phlexible\\Bundle\\ElementBundle\\Proxy\\ClassManager(
    '{$this->outputDir}',
    $interfaceMap,
    $classMap,
    $elementtypeIdMap,
    $dsIdMap
);
EOF;

        $filesystem->dumpFile(
            $this->outputDir . '/ClassManager.php',
            $content
        );

        return $this->outputDir . '/ClassManager.php';
    }
}
