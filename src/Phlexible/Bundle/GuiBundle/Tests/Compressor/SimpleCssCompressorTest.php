<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Compressor;

use org\bovigo\vfs\vfsStream;
use Phlexible\Bundle\GuiBundle\Compressor\SimpleCssCompressor;

/**
 * Simple css compressor test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SimpleCssCompressorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SimpleCssCompressor
     */
    private $compressor;

    protected function setUp()
    {
        $this->compressor = new SimpleCssCompressor();
    }

    private function createCss()
    {
        return <<<EOF
#some.test {
    background-color: #FFFFFF;
    /* test */
}
EOF;
    }

    public function testCompressString()
    {
        $css = $this->createCss();

        $this->assertEquals('#some.test{background-color: #FFFFFF}', $this->compressor->compressString($css));
    }

    public function testCompressStream()
    {
        $css = $this->createCss();

        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $css);
        rewind($stream);

        $compressed = stream_get_contents($this->compressor->compressStream($stream));

        $this->assertEquals('#some.test{background-color: #FFFFFF}', $compressed);
    }

    public function testCompressFile()
    {
        if (!class_exists('org\bovigo\vfs\vfsStream')) {
            $this->markTestSkipped('vfsStream not available');
        }

        $css = $this->createCss();

        vfsStream::setup('root', null, array('test.css' => $css));

        $compressed = file_get_contents($this->compressor->compressFile(vfsStream::url('root/test.css')));

        $this->assertEquals('#some.test{background-color: #FFFFFF}', $compressed);
    }
}
