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
use Phlexible\Bundle\GuiBundle\Compressor\SimpleJavascriptCompressor;

/**
 * Simple Javascript compressor test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SimpleJavascriptCompressorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SimpleJavascriptCompressor
     */
    private $compressor;

    protected function setUp()
    {
        $this->compressor = new SimpleJavascriptCompressor();
    }

    private function createJs()
    {
        return <<<EOF
// remove me
/* remove me */
/*
remove me
*/
/*
 * remove me
 */
/**
 * remove me
 */
var x = {
    allowed1: 'keep me',
    allowed2: '/* keep me */',
    allowed3: '// keep me'
};
EOF;
    }

    public function testCompressString()
    {
        $js = $this->createJs();

        $compressed = $this->compressor->compressString($js);

        $this->assertEquals($this->createJs(), $compressed);
    }

    public function testCompressStream()
    {
        $js = $this->createJs();

        $stream = fopen('php://memory', 'r+');
        fputs($stream, $js);
        rewind($stream);

        $compressed = stream_get_contents($this->compressor->compressStream($stream));

        $this->assertEquals($this->createJs(), $compressed);
    }

    public function testCompressFile()
    {
        if (!class_exists('org\bovigo\vfs\vfsStream')) {
            $this->markTestSkipped('vfsStream not available');
        }

        $js = $this->createJs();

        vfsStream::setup('root', null, array('test.js' => $js));

        $compressed = file_get_contents($this->compressor->compressFile(vfsStream::url('root/test.js')));

        $this->assertEquals($this->createJs(), $compressed);
    }
}
