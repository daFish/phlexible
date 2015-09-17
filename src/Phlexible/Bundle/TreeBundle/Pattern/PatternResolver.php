<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Pattern;

use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Site\Domain\Site;

/**
 * Pattern resolver.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PatternResolver
{
    /**
     * @var string
     */
    private $projectTitle;

    /**
     * @param array  $patterns
     * @param string $projectTitle
     */
    public function __construct(array $patterns, $projectTitle)
    {
        $this->projectTitle = $projectTitle;
        $this->patterns = $patterns;
    }

    /**
     * Resolved page title by configured pattern.
     *
     * @param string      $patternName
     * @param Site        $siteroot
     * @param NodeContext $node
     * @param string      $language
     *
     * @return string
     */
    public function replace($patternName, Site $siteroot, NodeContext $node, $language)
    {
        if (!isset($this->patterns[$patternName])) {
            $pattern = '%p';
        } else {
            $pattern = $this->patterns[$patternName];
        }

        return $this->replacePattern($pattern, $siteroot, $node, $language);
    }

    /**
     * Resolve page title by pattern.
     *
     * @param string      $pattern
     * @param Site        $siteroot
     * @param NodeContext $node
     * @param string      $language
     *
     * @return string
     */
    public function replacePattern($pattern, Site $siteroot, NodeContext $node, $language)
    {
        if (strpos($pattern, '%s') !== false) {
            $pattern = str_replace('%s', $siteroot->getTitle($language), $pattern);
        }
        if (strpos($pattern, '%b') !== false) {
            $pattern = str_replace('%b', $node->getField('backend', $language), $pattern);
        }
        if (strpos($pattern, '%p') !== false) {
            $pattern = str_replace('%p', $node->getField('page', $language), $pattern);
        }
        if (strpos($pattern, '%n') !== false) {
            $pattern = str_replace('%n', $node->getField('navigation', $language), $pattern);
        }
        if (strpos($pattern, '%t') !== false) {
            $pattern = str_replace('%t', $this->projectTitle, $pattern);
        }

        return $pattern;
    }
}
