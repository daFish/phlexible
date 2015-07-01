<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SiterootBundle\ProblemChecker;

use Phlexible\Bundle\ProblemBundle\Entity\Problem;
use Phlexible\Bundle\ProblemBundle\Problem\ProblemCollection;
use Phlexible\Bundle\ProblemBundle\ProblemChecker\ProblemCheckerInterface;
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;

/**
 * Siteroot problem checker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootProblemChecker implements ProblemCheckerInterface
{
    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @param SiterootManagerInterface $siterootManager
     */
    public function __construct(SiterootManagerInterface $siterootManager)
    {
        $this->siterootManager = $siterootManager;
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        $problems = new ProblemCollection();

        $siteroots = $this->siterootManager->findAll();

        if (!count($siteroots)) {
            $problems->add(new Problem(
                'siteroots_no_siteroots',
                Problem::SEVERITY_WARNING,
                'No Siteroots defined.',
                'Add at least one siteroot.'
            ));

            return $problems;
        }

        foreach ($siteroots as $siteRoot) {
            if (!$siteRoot->getNavigations()) {
                $problems->add(new Problem(
                    "siteroots_no_navigation_{$siteRoot->getId()}",
                    Problem::SEVERITY_WARNING,
                    "No Navigation defined for Siteroot {$siteRoot->getTitle()}.",
                    'Add Navigations to the Siteroot.'
                ));
            }

            $specialTids = $siteRoot->getSpecialTids();
            if (!$specialTids) {
                $problems->add(new Problem(
                    "siteroots_no_specialtids_{$siteRoot->getId()}",
                    Problem::SEVERITY_WARNING,
                    "No Special TIDs defined for Siteroot {$siteRoot->getTitle()}.",
                    'Add Special TIDs to the Siteroot.'
                ));
            } else {
                // TODO: repair
                /*
                $treeManager = Makeweb_Elements_Tree_Manager::getInstance();

                foreach ($specialTids as $specialTidLanguage => $specialTidValues) {
                    foreach ($specialTidValues as $specialTidKey => $specialTid) {
                        try {
                            $node = $treeManager->getNodeByNodeId($specialTid);

                            if ($node->getTree()->getSiterootId() !== $siteRoot->getId()) {
                                $problem = new Problem();
                                $problem
                                    ->setId('siteroots_inconsistant_tid_' . $specialTidKey.'_' . $siteRoot->getId())
                                    ->setCheckClass(__CLASS__)
                                    ->setSeverity(Problem::SEVERITY_WARNING)
                                    ->setMessage("Special TID $specialTidKey from Siteroot {$siteRoot->getTitle()} has TID $specialTid from wrong Siteroot {$node->getTree()->getSiteroot()->getTitle()}.")
                                    ->setHint("Set new value for Special TIDs $specialTidKey in the Siteroot")
                                    ->setIconClass('p-siteroot-component-icon')
                                ;
                                $problems[] = $problem;
                            }
                        } catch (\Exception $e) {
                            $problem = new Problem();
                            $problem
                                ->setId('siteroots_unknown_tid_' . $specialTidKey.'_' . $siteRoot->getId())
                                ->setCheckClass(__CLASS__)
                                ->setSeverity(Problem::SEVERITY_WARNING)
                                ->setMessage("Special TID $specialTidKey has unknown TID $specialTid in Siteroot {$siteRoot->getTitle()}.")
                                ->setHint("Set new value for Special TIDs $specialTidKey in the Siteroot.")
                                ->setIconClass('p-siteroot-component-icon')
                            ;
                            $problems[] = $problem;
                        }
                    }
                }
                */
            }

            if (!$siteRoot->getTitles()) {
                $problems->add(new Problem(
                    "siteroots_no_titles_{$siteRoot->getId()}",
                    Problem::SEVERITY_WARNING,
                    "No Titles defined for Siteroot {$siteRoot->getId()}.",
                    'Set Titles for the Siteroot'
                ));
            }

            if (!$siteRoot->getUrls()) {
                $problems->add(new Problem(
                    "siteroots_no_urls_{$siteRoot->getId()}",
                    Problem::SEVERITY_WARNING,
                    "No Urls defined for Siteroot {$siteRoot->getTitle('en')}.",
                    'Set Urls for the Siteroot'
                ));
            }
        }

        return $problems;
    }
}
