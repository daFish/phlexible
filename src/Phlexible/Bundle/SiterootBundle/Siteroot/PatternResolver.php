<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SiterootBundle\Siteroot;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Symfony\Component\Translation\TranslatorInterface;

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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     * @param string              $projectTitle
     */
    public function __construct(TranslatorInterface $translator, $projectTitle)
    {
        $this->projectTitle = $projectTitle;
        $this->translator = $translator;
    }

    /**
     * Return siteroot title
     *
     * @param Siteroot       $siteroot
     * @param ElementVersion $elementVersion
     * @param string         $language
     * @param string         $pattern
     *
     * @return string
     */
    public function replace(Siteroot $siteroot, ElementVersion $elementVersion, $language, $pattern)
    {
        $replace = array(
            '%s' => $siteroot->getTitle(),
            '%b' => $elementVersion->getBackendTitle($language),
            '%p' => $elementVersion->getPageTitle($language),
            '%n' => $elementVersion->getNavigationTitle($language),
            '%r' => $this->projectTitle,
        );

        return str_replace(array_keys($replace), array_values($replace), $pattern);
    }

    /**
     * @param Siteroot $siteroot
     * @param string   $language
     * @param string   $pattern
     *
     * @return string
     */
    public function replaceExample(Siteroot $siteroot, $language, $pattern = null)
    {
        $replace = array(
            '%s' => $siteroot->getTitle(),
            '%b' => '[' . $this->translator->trans('siteroots.element_backend_title', array(), 'gui', $language) . ']',
            '%p' => '[' . $this->translator->trans('siteroots.element_page_title', array(), 'gui', $language) . ']',
            '%n' => '[' . $this->translator->trans('siteroots.element_navigation_title', array(), 'gui', $language) . ']',
            '%r' => $this->projectTitle,
        );

        return str_replace(array_keys($replace), array_values($replace), $pattern);
    }

    /**
     * @param string $language
     *
     * @return array
     */
    public function getPlaceholders($language)
    {
        return array(
            array(
                'placeholder' => '%s',
                'title'       => $this->translator->trans('siteroots.siteroot_title', array(), 'gui', $language)
            ),
            array(
                'placeholder' => '%b',
                'title'       => $this->translator->trans('siteroots.element_backend_title', array(), 'gui', $language)
            ),
            array(
                'placeholder' => '%p',
                'title'       => $this->translator->trans('siteroots.element_page_title', array(), 'gui', $language)
            ),
            array(
                'placeholder' => '%n',
                'title'       => $this->translator->trans('siteroots.element_navigation_title', array(), 'gui', $language)
            ),
            array(
                'placeholder' => '%r',
                'title'       => $this->translator->trans('siteroots.project_title', array(), 'gui', $language)
            ),
        );
    }
}
