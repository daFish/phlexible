<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Selectfield Controller
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 * @Route("/elementtypes/selectfield")
 * @Security("is_granted('ROLE_ELEMENTTYPES')")
 */
class SelectfieldController extends Controller
{
    /**
     * Return available functions
     *
     * @return JsonResponse
     * @Route("/select", name="elementtypes_selectfield_providers")
     */
    public function selectAction()
    {
        $selectFieldProviders = $this->get('phlexible_elementtype.select_field_providers');

        $data = array();
        foreach ($selectFieldProviders->all() as $selectFieldProvider) {
            $data[] = array(
                'name'  => $selectFieldProvider->getName(),
                'title' => $selectFieldProvider->getTitle($this->getUser()->getInterfaceLanguage('en')),
            );
        }

        return new JsonResponse(array('functions' => $data));
    }

    /**
     * Return selectfield data for lists
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/function", name="elementtypes_selectfield_function")
     */
    public function functionAction(Request $request)
    {
        $selectFieldProviders = $this->get('phlexible_elementtype.select_field_providers');

        $providerName = $request->get('provider');
        $language = $this->getUser()->getInterfaceLanguage('en');

        $provider = $selectFieldProviders->get($providerName);
        $data = $provider->getData($language);

        return new JsonResponse(array('data' => $data));
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

        $data = array();

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

            $data[] = array('key' => $key, 'value' => $key);
        }

        return new JsonResponse(array('data' => $data));
    }
}
