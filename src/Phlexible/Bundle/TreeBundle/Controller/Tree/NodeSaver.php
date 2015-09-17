<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Controller\Tree;

use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Data saver.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeSaver
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    private $availableLanguages;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param string                   $availableLanguages
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        $availableLanguages)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->availableLanguages = explode(',', $availableLanguages);
    }

    /**
     * Save element data.
     *
     * @param NodeContext   $nodeContext
     * @param Request       $request
     * @param UserInterface $user
     */
    public function save(NodeContext $nodeContext, Request $request, UserInterface $user)
    {
        $language = $request->get('language');

        $comment = null;
        if ($request->get('comment')) {
            $comment = $request->get('comment');
        }

        // TODO: available languages
        //$this->saveMeta($elementVersion, $language, $isMaster, array('de'), $request);

        $node = $nodeContext->getNode();

        if ($request->get('configuration')) {
            $configuration = json_decode($request->get('configuration'), true);

            if (!empty($configuration['navigation'])) {
                $node->setInNavigation(true);
            } else {
                $node->setInNavigation(false);
            }
            if (!empty($configuration['template'])) {
                $node->setTemplate($configuration['template']);
            } else {
                $node->setTemplate(null);
            }
            if (!empty($configuration['robotsNoIndex'])) {
                $node->setAttribute('robotsNoIndex', true);
            } else {
                $node->removeAttribute('robotsNoIndex');
            }
            if (!empty($configuration['robotsNoFollow'])) {
                $node->setAttribute('robotsNoFollow', true);
            } else {
                $node->removeAttribute('robotsNoFollow');
            }
            if (!empty($configuration['searchNoIndex'])) {
                $node->setAttribute('searchNoIndex', true);
            } else {
                $node->removeAttribute('searchNoIndex');
            }

            if (isset($configuration['security'])) {
                $security = $configuration['security'];

                $node->setAttribute('security', $security);

                if (!empty($security['authentication_required'])) {
                    $node->setAttribute('authenticationRequired', true);
                } else {
                    $node->removeAttribute('authenticationRequired');
                }
                if (!empty($security['roles'])) {
                    $node->setAttribute('roles', $security['roles']);
                } else {
                    $node->removeAttribute('roles');
                }
                if (!empty($security['check_acl'])) {
                    $node->setAttribute('checkAcl', true);
                } else {
                    $node->removeAttribute('checkAcl');
                }
                if (!empty($security['expression'])) {
                    $node->setAttribute('expression', $security['expression']);
                } else {
                    $node->removeAttribute('expression');
                }
            } else {
                $node->removeAttribute('security');
            }

            if (isset($configuration['cache'])) {
                $cache = $configuration['cache'];

                $node->setAttribute('cache', $cache);

                if (!empty($cache['expires'])) {
                    $node->setAttribute('expires', $cache['expires']);
                } else {
                    $node->removeAttribute('expires');
                }
                if (!empty($cache['public'])) {
                    $node->setAttribute('public', true);
                } else {
                    $node->removeAttribute('public');
                }
                if (!empty($cache['maxage'])) {
                    $node->setAttribute('maxage', $cache['maxage']);
                } else {
                    $node->removeAttribute('maxage');
                }
                if (!empty($cache['smaxage'])) {
                    $node->setAttribute('smaxage', $cache['smaxage']);
                } else {
                    $node->removeAttribute('smaxage');
                }
                if (!empty($cache['vary'])) {
                    $node->setAttribute('vary', $cache['vary']);
                } else {
                    $node->removeAttribute('vary');
                }
            } else {
                $node->removeAttribute('cache');
            }

            if (isset($configuration['routing'])) {
                $routing = $configuration['routing'];

                $node->setAttribute('routing', $configuration['routing']);
                /*
                if (!empty($routing['name'])) {
                    $node->setAttribute('name', $routing['name']);
                } else {
                    $node->removeAttribute('name');
                }
                if (!empty($routing['path'])) {
                    $node->setAttribute('path', $routing['path']);
                } else {
                    $node->removeAttribute('path');
                }
                if (!empty($routing['defaults'])) {
                    $node->setAttribute('defaults', $routing['defaults']);
                } else {
                    $node->removeAttribute('defaults');
                }
                if (!empty($routing['methods'])) {
                    $node->setAttribute('methods', $routing['methods']);
                } else {
                    $node->removeAttribute('methods');
                }
                if (!empty($routing['schemes'])) {
                    $node->setAttribute('schemes', $routing['schemes']);
                } else {
                    $node->removeAttribute('schemes');
                }
                if (!empty($routing['controller'])) {
                    $node->setAttribute('controller', $routing['controller']);
                } else {
                    $node->removeAttribute('controller');
                }
                */
            } else {
                $node->removeAttribute('routing');
            }
        }

        $nodeContext->getTree()->updateNode($nodeContext);
    }
}
