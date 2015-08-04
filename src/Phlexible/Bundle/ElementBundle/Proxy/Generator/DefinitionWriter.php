<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy\Generator;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Definition writer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DefinitionWriter
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
     * @return mixed
     */
    public function getManagerFile()
    {
        return $this->outputDir . '/ClassManager.php';
    }

    /**
     * @param array  $classes
     * @param string $namespacePrefix
     *
     * @return string
     */
    public function write(array $classes, $namespacePrefix)
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem(__DIR__ . '/../../Resources/views/'));

        $this->clear();

        $manager = new ManagerDefinition();

        foreach ($classes as $class) {
            $this->writeMainClass($class, $manager, $twig, $namespacePrefix);
        }

        return $this->writeManager($manager);
    }

    /**
     * @param MainClassDefinition $class
     * @param ManagerDefinition   $manager
     * @param \Twig_Environment   $twig
     * @param string              $namespacePrefix
     */
    private function writeMainClass(MainClassDefinition $class, ManagerDefinition $manager, \Twig_Environment $twig, $namespacePrefix)
    {
        $content = $twig->render('Main.php.twig', array('class' => $class));
        $relativeFilename = str_replace('\\', '/', str_replace($namespacePrefix, '', $class->getNamespace())) . '/' . $class->getClassname() . '.php';
        $this->dumpFile($relativeFilename, $content);

        $manager->addElementtypeId($class->getElementtypeId(), $class->getNamespace() . '\\' . $class->getClassname(), $relativeFilename);

        $this->writeSubClasses($class, $manager, $twig, $namespacePrefix);
    }

    /**
     * @param ClassDefinition   $class
     * @param ManagerDefinition $manager
     * @param \Twig_Environment $twig
     * @param string            $namespacePrefix
     */
    private function writeSubClasses(ClassDefinition $class, ManagerDefinition $manager, \Twig_Environment $twig, $namespacePrefix)
    {
        foreach ($class->getCollections() as $collection) {
            foreach ($collection->getClasses() as $structure) {
                $content = $twig->render('Structure.php.twig', array('class' => $structure));
                $relativeFilename = str_replace('\\', '/', str_replace($namespacePrefix, '', $structure->getNamespace())) . '/' . $structure->getClassname() . '.php';
                $this->dumpFile($relativeFilename, $content);

                $manager->addDsId($structure->getDsId(), $structure->getNamespace() . '\\' . $structure->getClassname(), $relativeFilename);

                $this->writeSubClasses($structure, $manager, $twig, $namespacePrefix);
            }
        }

        foreach ($class->getChildren() as $structure) {
            $content = $twig->render('Structure.php.twig', array('class' => $structure));
            $relativeFilename = str_replace('\\', '/', str_replace($namespacePrefix, '', $structure->getNamespace())) . '/' . $structure->getClassname() . '.php';
            $this->dumpFile($relativeFilename, $content);

            $manager->addDsId($structure->getDsId(), $structure->getNamespace() . '\\' . $structure->getClassname(), $relativeFilename);

            $this->writeSubClasses($structure, $manager, $twig, $namespacePrefix);
        }
    }

    /**
     * @param ManagerDefinition $manager
     *
     * @return string
     */
    private function writeManager(ManagerDefinition $manager)
    {
        $content = "<?php return new \\Phlexible\\Bundle\\ElementBundle\\Proxy\\ClassManager(
    '{$this->outputDir}',
    " . var_export($manager->getElementtypeIds(), true) . ",
    " . var_export($manager->getDsIds(), true) . "
);";
        $relativeFilename = 'ClassManager.php';

        return $this->dumpFile($relativeFilename, $content);
    }

    /**
     * @param string $relativeFilename
     * @param string $content
     *
     * @return string
     */
    private function dumpFile($relativeFilename, $content)
    {
        $filesystem = new Filesystem();

        $filesystem->dumpFile($this->outputDir . '/' . $relativeFilename, $content);

        return $this->outputDir . '/' . $relativeFilename;
    }

    private function clear()
    {
        $filesystem = new Filesystem();

        if (!$filesystem->exists($this->outputDir)) {
            return;
        }

        $finder = new Finder();

        foreach ($finder->in($this->outputDir)->directories()->depth(0) as $dir) {
            /* @var $dir \Symfony\Component\Finder\SplFileInfo */

            $filesystem->remove($dir->getPathname());
        }
    }
}
