<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\ElementProxy\Generator;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Definition writer.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DefinitionWriter
{
    /**
     * @var string
     */
    private $viewDir;

    /**
     * @var string
     */
    private $outputDir;

    /**
     * @param string $viewDir
     * @param string $outputDir
     */
    public function __construct($viewDir, $outputDir)
    {
        $this->viewDir = $viewDir;
        $this->outputDir = $outputDir;
    }

    /**
     * @return mixed
     */
    public function getManagerFile()
    {
        return $this->outputDir.'/ClassManager.php';
    }

    /**
     * @param array  $classes
     * @param string $namespacePrefix
     *
     * @return string
     */
    public function write(array $classes, $namespacePrefix)
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem($this->viewDir));

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
        $relativeFilename = str_replace('\\', '/', str_replace($namespacePrefix, '', $class->getNamespace())).'/'.$class->getClassname().'.php';
        $this->dumpFile($relativeFilename, $content);

        $manager->addMainClass($class, $relativeFilename);

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
                $relativeFilename = str_replace('\\', '/', str_replace($namespacePrefix, '', $structure->getNamespace())).'/'.$structure->getClassname().'.php';
                $this->dumpFile($relativeFilename, $content);

                $manager->addStructureClass($structure, $relativeFilename);

                $this->writeSubClasses($structure, $manager, $twig, $namespacePrefix);
            }
        }

        foreach ($class->getChildren() as $structure) {
            $content = $twig->render('Structure.php.twig', array('class' => $structure));
            $relativeFilename = str_replace('\\', '/', str_replace($namespacePrefix, '', $structure->getNamespace())).'/'.$structure->getClassname().'.php';
            $this->dumpFile($relativeFilename, $content);

            $manager->addStructureClass($structure, $relativeFilename);

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
        $content = "<?php return new \\Phlexible\\Component\\ElementProxy\\ClassManager(
    '{$this->outputDir}',
    ".var_export($manager->getNames(), true).',
    '.var_export($manager->getIds(), true).',
    '.var_export($manager->getDsIds(), true).'
);';
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

        $filesystem->dumpFile($this->outputDir.'/'.$relativeFilename, $content);

        return $this->outputDir.'/'.$relativeFilename;
    }

    /**
     * Clear proxies.
     */
    private function clear()
    {
        $filesystem = new Filesystem();

        if (!$filesystem->exists($this->outputDir)) {
            return;
        }

        $finder = new Finder();

        foreach ($finder->in($this->outputDir)->directories()->depth(0) as $dir) {
            /* @var $dir SplFileInfo */

            $filesystem->remove($dir->getPathname());
        }
    }
}
