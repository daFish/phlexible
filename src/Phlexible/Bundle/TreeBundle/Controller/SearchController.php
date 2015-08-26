<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Controller;

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
 * @Route("/tree/search")
 * @Security("is_granted('ROLE_ELEMENTS')")
 */
class SearchController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/nodes", name="tree_search_nodes")
     */
    public function nodesAction(Request $request)
    {
        $siterootId = $request->get('siteroot_id');
        $language = $request->get('language');
        $query = $request->get('query');

        $nodeManager = $this->get('phlexible_tree.node_manager');
        $iconResolver = $this->get('phlexible_tree.icon_resolver');
        $conn = $this->get('database_connection');

        $qb = $conn->createQueryBuilder();
        $qb
            ->select('t.id AS nodeId', 'e.latest_version AS version', 'evmf.backend AS title')
            ->from('tree', 't')
            ->join('t', 'element', 'e', 't.type_id = e.eid')
            ->join('e', 'element_version', 'ev', 'ev.eid = e.eid AND ev.version = e.latest_version')
            ->join('ev', 'element_version_mapped_field', 'evmf', 'evmf.element_version_id = ev.id')
            ->where($qb->expr()->eq('evmf.language', $qb->expr()->literal($language)))
            ->andWhere($qb->expr()->eq('t.siteroot_id', $qb->expr()->literal($siterootId)))
            ->andWhere($qb->expr()->like('evmf.backend', $qb->expr()->literal("%$query%")));

        $result = $qb->execute()->fetchAll();

        foreach ($result as $key => $row) {
            $node = $nodeManager->find($row['nodeId']);
            $result[$key]['icon'] = $iconResolver->resolveNode($node);
        }

        return new JsonResponse(array('results' => $result));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/media", name="tree_search_files")
     */
    public function filesAction(Request $request)
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
     * @Route("/filelink", name="tree_search_filelink")
     */
    public function filelinkAction(Request $request)
    {
        $fileId = $request->get('file_id');

        $volume = $this->get('phlexible_media_manager.volume_manager')->getByFileId($fileId);
        $file = $volume->findFile($fileId);
        $urls = $volume->getStorageDriver()->getUrls($file);

        return new JsonResponse($urls);
    }
}
