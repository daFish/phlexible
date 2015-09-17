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
use Phlexible\Bundle\ProblemBundle\Model\ProblemCheckerInterface;
use Phlexible\Component\Site\Model\SiteManagerInterface;
use Phlexible\Bundle\ProblemBundle\Problem\ProblemCollection;

/**
 * Siteroot problem checker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootProblemChecker implements ProblemCheckerInterface
{
    /**
     * @var SiteManagerInterface
     */
    private $siterootManager;

    /**
     * @param SiteManagerInterface $siterootManager
     */
    public function __construct(SiteManagerInterface $siterootManager)
    {
        $this->siterootManager = $siterootManager;
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        $problems = new ProblemCollection();

        $sites = $this->siterootManager->findAll();

        if (!count($sites)) {
            $problems->add(new Problem(
                'siteroots_no_siteroots',
                Problem::SEVERITY_WARNING,
                'No Siteroots defined.',
                'Add at least one siteroot.'
            ));

            return $problems;
        }

        foreach ($sites as $site) {
            if (!$site->getNavigations()) {
                $problems->add(new Problem(
                    "siteroots_no_navigation_{$site->getId()}",
                    Problem::SEVERITY_WARNING,
                    "No navigation defined for site {$site->getTitle()}.",
                    'Add a navigation to the site.'
                ));
            }

            $nodeAliases = $site->getNodeAliases();
            if (!$nodeAliases) {
                $problems->add(new Problem(
                    "site_no_node_aliases_{$site->getId()}",
                    Problem::SEVERITY_WARNING,
                    "No node aliases defined for Site {$site->getTitle()}.",
                    'Add a node alias to the site.'
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
                                    ->setIconClass('p-siteroot-component-icon');
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
                                ->setIconClass('p-siteroot-component-icon');
                            $problems[] = $problem;
                        }
                    }
                }
                */
            }

            if (!$site->getTitles()) {
                $problems->add(new Problem(
                    "site_no_titles_{$site->getId()}",
                    Problem::SEVERITY_WARNING,
                    "No Titles defined for Siteroot {$site->getId()}.",
                    'Set the titles for the Siteroot.'
                ));
            }

            if (!$site->getEntryPoints()) {
                $problems->add(new Problem(
                    "site_no_entry_pointy_{$site->getId()}",
                    Problem::SEVERITY_WARNING,
                    "No entry points defined for site {$site->getTitle('en')}.",
                    'Add at least one entry point for the site.'
                ));
            }
        }

        return $problems;
    }
}
