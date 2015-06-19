<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Controller;

use Phlexible\Bundle\DataSourceBundle\Entity\DataSource;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Data controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/datasources")
 * @Security("is_granted('ROLE_DATA_SOURCES')")
 */
class DataController extends Controller
{
    /**
     * Return something
     *
     * @return JsonResponse
     * @Route("/list", name="datasources_list")
     */
    public function listAction()
    {
        $dataSourceManager = $this->get('phlexible_data_source.data_source_manager');

        $dataSources = $dataSourceManager->findBy([]);

        $sources = [];
        foreach ($dataSources as $dataSource) {
            $sources[] = [
                'id' => $dataSource->getId(),
                'title' => $dataSource->getTitle()
            ];
        }

        return new JsonResponse(['datasources' => $sources]);
    }

    /**
     * Return something
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="datasources_create")
     */
    public function createAction(Request $request)
    {
        $title = $request->get('title');

        $dataSourceManager = $this->get('phlexible_data_source.data_source_manager');

        $dataSource = new DataSource();
        $dataSource
            ->setTitle($title)
            ->setCreatedAt(new \DateTime())
            ->setCreateUserId($this->getUser()->getId())
            ->setModifiedAt($dataSource->getCreatedAt())
            ->setModifyUserId($dataSource->getCreateUserId());

        try {
            $dataSourceManager->updateDataSource($dataSource);

            $response = new ResultResponse(true);
        } catch (\Exception $e) {
            $response = new ResultResponse(false, $e->getMessage());
        }

        return $response;
    }

    /**
     * Return something
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/add", name="datasources_add")
     */
    public function addAction(Request $request)
    {
        $sourceId = $request->get('source_id');
        $key = $request->get('key');
        $language = $request->get('language', 'de');

        $dataSourceManager = $this->get('phlexible_data_source.data_source_manager');

        // load
        $source = $dataSourceManager->find($sourceId);

        // add new key
        $source->addValueForLanguage($key, false);

        // save
        $dataSourceManager->save($source, $this->getUser()->getId());

        return new ResultResponse(true);
    }

    /**
     * Return selectfield data for lists
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/suggest", name="elementtypes_selectfield_suggest")
     */
    public function suggestAction(Request $request)
    {
        $id = $request->get('id');
        $dsId = $request->get('ds_id');
        $language = $request->get('language');
        $query = $request->get('query', null);
        $valuesQuery = $request->get('valuesqry', '');

        $data = [];

        $datasourceManager = $this->get('phlexible_data_source.data_source_manager');

        $source = $datasourceManager->find($id);

        $filter = null;
        if ($query && $valuesQuery) {
            $filter = explode('|', $query);
        }

        foreach ($source->getActiveValuesForLanguage($language) as $key => $value) {
            if (!empty($query)) {
                if ($filter && !in_array($key, $filter)) {
                    continue;
                } elseif (!$filter && mb_stripos($key, $query) === false) {
                    continue;
                }
            }

            $data[] = ['key' => $key, 'value' => $key];
        }

        return new JsonResponse(['data' => $data]);
    }
}
