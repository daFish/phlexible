<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Controller;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Search controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elements/search")
 * @Security("is_granted('ROLE_ELEMENTS')")
 */
class SearchController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/elements", name="elements_search_elements")
     */
    public function elementsAction(Request $request)
    {
        $siterootId = $request->get('siteroot_id');
        $language = $request->get('language');
        $query = $request->get('query');

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $iconResolver = $this->get('phlexible_element.icon_resolver');
        $conn = $this->get('database_connection');

        $tree = $treeManager->getBySiteRootId($siterootId);

        $qb = $conn->createQueryBuilder();
        $qb
            ->select('t.id AS tid', 'e.latest_version AS version', 'evmf.backend AS title')
            ->from('tree', 't')
            ->join('t', 'element', 'e', 't.type_id = e.eid')
            ->join('e', 'element_version', 'ev', 'ev.eid = e.eid AND ev.version = e.latest_version')
            ->join('ev', 'element_version_mapped_field', 'evmf', 'evmf.element_version_id = ev.id')
            ->where($qb->expr()->eq('evmf.language', $qb->expr()->literal($language)))
            ->andWhere($qb->expr()->eq('t.siteroot_id', $qb->expr()->literal($siterootId)))
            ->andWhere($qb->expr()->like('evmf.backend', $qb->expr()->literal("%$query%")));

        $result = $conn->fetchAll($qb->getSQL());

        foreach ($result as $key => $row) {
            $treeNode = $tree->get($row['tid']);
            $result[$key]['icon'] = $iconResolver->resolveTreeNode($treeNode, $language);
        }

        return new JsonResponse(array('results' => $result));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/media", name="elements_search_media")
     */
    public function mediaAction(Request $request)
    {
        $query = $request->get('query');

        // TODO: meta search

        $results = array();
        foreach ($this->get('phlexible_media_manager.volume_manager')->all() as $volume) {
            $files = $volume->search($query);

            foreach ($files as $file) {
                /* @var $file ExtendedFileInterface */

                $results[] = array(
                    'id'        => $file->getId(),
                    'version'   => $file->getVersion(),
                    'name'      => $file->getName(),
                    'folder_id' => $file->getFolderId(),
                );
            }
        }

        return new JsonResponse(array('results' => $results));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/medialink", name="elements_search_medialink")
     */
    public function medialinkAction(Request $request)
    {
        $fileId = $request->get('file_id');

        $volume = $this->get('phlexible_media_manager.volume_manager')->getByFileId($fileId);
        $file = $volume->findFile($fileId);
        $urls = $volume->getStorageDriver()->getUrls($file);

        return new JsonResponse($urls);
    }
}
