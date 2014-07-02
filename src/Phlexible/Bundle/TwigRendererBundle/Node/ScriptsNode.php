<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TwigRendererBundle\Node;

class ScriptsNode extends \Twig_Node
{
    public function __construct(array $assets, $output, \Twig_NodeInterface $body, $lineno, $tag = 'styles')
    {
        $attributes = array(
            'assets' => $assets,
            'output' => $output
        );

        parent::__construct(array('body' => $body), $attributes, $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param Twig_Compiler A Twig_Compiler instance
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this);

        foreach ($this->attributes['assets'] as $asset) {
            $compiler
                ->write('$context["url"] = "' . $asset . '";' . PHP_EOL)
                ->subcompile($this->getNode('body'))
            ;
        }
    }
}