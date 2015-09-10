<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\Builder;

use Phlexible\Bundle\GuiBundle\Asset\Builder\TranslationsBuilder;
use Prophecy\Argument;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;

class TranslationsBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuilderCompressesOnDebugFalse()
    {
        $compressor = $this->prophesize('Phlexible\Bundle\GuiBundle\Compressor\CompressorInterface');
        $compressor->compressString(Argument::any())->shouldBeCalled();

        $translator = new Translator('de');

        $builder = new TranslationsBuilder($translator, $compressor->reveal(), sys_get_temp_dir(), false);
        $builder->build('de');
    }

    public function testBuilderDoesNotCompressOnDebugTrue()
    {
        $compressor = $this->prophesize('Phlexible\Bundle\GuiBundle\Compressor\CompressorInterface');
        $compressor->compressString(Argument::any())->shouldNotBeCalled();

        $translator = new Translator('de');

        $builder = new TranslationsBuilder($translator, $compressor->reveal(), sys_get_temp_dir(), true);
        $builder->build('de');
    }

    public function testBuildCreatesFileForDefaultDomain()
    {
        $compressor = $this->prophesize('Phlexible\Bundle\GuiBundle\Compressor\CompressorInterface');

        $translator = new Translator('de');
        $translator->addLoader('loader', new ArrayLoader());
        $translator->addResource('loader', array('Phlexible.gui.Panel.title' => 'testTitle'), 'de', 'gui');

        $builder = new TranslationsBuilder($translator, $compressor->reveal(), sys_get_temp_dir(), true);

        $file = $builder->build('de');

        $this->assertFileExists($file);
        $this->assertSame('Ext.define("Ext.locale.de.Phlexible.gui.Panel", {
    "override": "Phlexible.gui.Panel",
    "title": "testTitle"
});
', file_get_contents($file));
    }

    public function testBuildCreatesFileForCustomDomain()
    {
        $compressor = $this->prophesize('Phlexible\Bundle\GuiBundle\Compressor\CompressorInterface');

        $translator = new Translator('de');
        $translator->addLoader('loader', new ArrayLoader());
        $translator->addResource('loader', array('Phlexible.gui.Panel.title' => 'testTitle'), 'de', 'mydomain');

        $builder = new TranslationsBuilder($translator, $compressor->reveal(), sys_get_temp_dir(), true);

        $file = $builder->build('de', null, 'mydomain');

        $this->assertFileExists($file);
        $this->assertSame('Ext.define("Ext.locale.de.Phlexible.gui.Panel", {
    "override": "Phlexible.gui.Panel",
    "title": "testTitle"
});
', file_get_contents($file));
    }

    public function testBuildUsesFallbackLocale()
    {
        $compressor = $this->prophesize('Phlexible\Bundle\GuiBundle\Compressor\CompressorInterface');

        $translator = new Translator('de');
        $translator->addLoader('loader', new ArrayLoader());
        $translator->addResource('loader', array('Phlexible.gui.Panel.title' => 'testTitle'), 'de', 'gui');

        $builder = new TranslationsBuilder($translator, $compressor->reveal(), sys_get_temp_dir(), true);

        $file = $builder->build('en', 'de');

        $this->assertFileExists($file);
        $this->assertSame('Ext.define("Ext.locale.en.Phlexible.gui.Panel", {
    "override": "Phlexible.gui.Panel",
    "title": "testTitle"
});
', file_get_contents($file));
    }
}
