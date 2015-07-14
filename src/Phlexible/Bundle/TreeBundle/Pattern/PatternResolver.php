<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Pattern;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;

/**
 * Pattern resolver
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
     * Resolved page title by configured pattern
     *
     * @param string         $patternName
     * @param Siteroot       $siteroot
     * @param ElementVersion $elementVersion
     * @param string         $language
     *
     * @return string
     */
    public function replace($patternName, Siteroot $siteroot, ElementVersion $elementVersion, $language)
    {
        if (!isset($this->patterns[$patternName])) {
            $pattern = '%p';
        } else {
            $pattern = $this->patterns[$patternName];
        }

        return $this->replacePattern($pattern, $siteroot, $elementVersion, $language);
    }

    /**
     * Resolve page title by pattern
     *
     * @param string         $pattern
     * @param Siteroot       $siteroot
     * @param ElementVersion $elementVersion
     * @param string         $language
     *
     * @return string
     */
    public function replacePattern($pattern, Siteroot $siteroot, ElementVersion $elementVersion, $language)
    {
        if (strpos($pattern, '%s') !== false) {
            $pattern = str_replace('%s', $siteroot->getTitle($language), $pattern);
        }
        if (strpos($pattern, '%b') !== false) {
            $pattern = str_replace('%b', $elementVersion->getBackendTitle($language), $pattern);
        }
        if (strpos($pattern, '%p') !== false) {
            $pattern = str_replace('%p', $elementVersion->getPageTitle($language), $pattern);
        }
        if (strpos($pattern, '%n') !== false) {
            $pattern = str_replace('%n', $elementVersion->getNavigationTitle($language), $pattern);
        }
        if (strpos($pattern, '%t') !== false) {
            $pattern = str_replace('%t', $this->projectTitle, $pattern);
        }

        return $pattern;
    }
}
