<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Event\GetConfigEvent;
use Phlexible\Bundle\TreeBundle\Model\TreeManagerInterface;
use Phlexible\Component\Site\Model\SiteManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Get config listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetConfigListener
{
    /**
     * @var SiteManagerInterface
     */
    private $siterootManager;

    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var bool
     */
    private $publishCommentRequired;

    /**
     * @var bool
     */
    private $publishConfirmRequired;

    /**
     * @var bool
     */
    private $createUseMultilanguage;

    /**
     * @var bool
     */
    private $createRestricted;

    /**
     * @var array
     */
    private $availableLanguages;

    /**
     * @param SiteManagerInterface      $siterootManager
     * @param TreeManagerInterface          $treeManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param bool                          $publishCommentRequired
     * @param bool                          $publishConfirmRequired
     * @param bool                          $createUseMultilanguage
     * @param bool                          $createRestricted
     * @param string                        $availableLanguages
     */
    public function __construct(
        SiteManagerInterface $siterootManager,
        TreeManagerInterface $treeManager,
        AuthorizationCheckerInterface $authorizationChecker,
        $publishCommentRequired,
        $publishConfirmRequired,
        $createUseMultilanguage,
        $createRestricted,
        $availableLanguages
    )
    {
        $this->siterootManager = $siterootManager;
        $this->treeManager = $treeManager;
        $this->authorizationChecker = $authorizationChecker;
        $this->publishCommentRequired = $publishCommentRequired;
        $this->publishConfirmRequired = $publishConfirmRequired;
        $this->createUseMultilanguage = $createUseMultilanguage;
        $this->createRestricted = $createRestricted;
        $this->availableLanguages = explode(',', $availableLanguages);
    }

    /**
     * @param GetConfigEvent $event
     */
    public function onGetConfig(GetConfigEvent $event)
    {
        $config = $event->getConfig();

        $config->set('tree.publish.comment_required', (bool) $this->publishCommentRequired);
        $config->set('tree.publish.confirm_required', (bool) $this->publishConfirmRequired);
        $config->set('tree.create.use_multilanguage', (bool) $this->createUseMultilanguage);
        $config->set('tree.create.restricted', (bool) $this->createRestricted);

        $siteroots = $this->siterootManager->findAll();

        $siterootLanguages = array();
        $siterootConfig = array();

        foreach ($siteroots as $siteroot) {
            $siterootId = $siteroot->getId();

            if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
                $siterootLanguages[$siterootId] = $this->availableLanguages;
            } else {
                $siterootLanguages[$siterootId] = array();

                $tree = $this->treeManager->getBySiterootId($siterootId);
                $root = $tree->getRoot();

                foreach ($this->availableLanguages as $language) {
                    if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') && !$this->authorizationChecker->isGranted(['permission' => 'VIEW', 'language' => $language], $root)) {
                        continue;
                    }

                    $siterootLanguages[$siterootId][] = $language;
                }
            }

            if (count($siterootLanguages[$siterootId])) {
                $siterootConfig[$siterootId] = array(
                    'id' => $siteroot->getId(),
                    'title' => $siteroot->getTitle(),
                );
            } else {
                unset($siterootLanguages[$siterootId]);
            }
        }

        $config->set('user.siteroot.languages', $siterootLanguages);

        $config->set('siteroot.config', $siterootConfig);
    }
}
