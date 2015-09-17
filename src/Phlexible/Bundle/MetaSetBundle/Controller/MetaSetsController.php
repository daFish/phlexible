<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MetaSetBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\MetaSetBundle\Form\Type\MetaSetType;
use Phlexible\Component\MetaSet\Domain\MetaSet;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Sets controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_META_SETS')")
 * @Rest\NamePrefix("phlexible_api_metaset_")
 */
class MetaSetsController extends FOSRestController
{
    /**
     * Get meta sets.
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of MetaSet",
     *   section="metaset",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful"
     *   }
     * )
     */
    public function getMetasetsAction()
    {
        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $metaSets = $metaSetManager->findAll();

        return array(
            'metasets' => array_values($metaSets),
            'total' => count($metaSets),
        );
    }

    /**
     * Get meta set.
     *
     * @param string $metasetId
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a MetaSet",
     *   section="metaset",
     *   output="Phlexible\Component\MetaSetComponent\Model\MetaSet",
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when job was not found"
     *   }
     * )
     */
    public function getMetasetAction($metasetId)
    {
        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $metaSet = $metaSetManager->find($metasetId);

        if (!$metaSet instanceof MetaSet) {
            throw new NotFoundHttpException('Meta set not found');
        }

        return $metaSet;
    }

    /**
     * Get meta sets.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Create a MetaSet",
     *   section="metaset",
     *   input="Phlexible\Bundle\MetaSetBundle\Form\Type\MetaSetType",
     *   statusCodes={
     *     201="Returned when metaset was created",
     *     204="Returned when metaset was updated",
     *     404="Returned when metaset was not found"
     *   }
     * )
     */
    public function postMetasetsAction(Request $request)
    {
        return $this->processForm($request, new MetaSet());
    }

    /**
     * Get meta set.
     *
     * @param Request $request
     * @param string  $metasetId
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Update a MetaSet",
     *   section="metaset",
     *   input="Phlexible\Bundle\MetaSetBundle\Form\Type\MetaSetType",
     *   statusCodes={
     *     201="Returned when metaset was created",
     *     204="Returned when metaset was updated",
     *     404="Returned when metaset was not found"
     *   }
     * )
     */
    public function putMetasetAction(Request $request, $metasetId)
    {
        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $metaSet = $metaSetManager->find($metasetId);

        return $this->processForm($request, $metaSet);
    }

    /**
     * @param Request $request
     * @param MetaSet $metaSet
     *
     * @return View|Response
     */
    private function processForm(Request $request, MetaSet $metaSet)
    {
        $statusCode = !$metaSet->getId() ? 201 : 204;

        $form = $this->createForm(new MetaSetType(), $metaSet);
        $form->submit($request);

        if ($form->isValid()) {
            $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
            $metaSetManager->updateMetaset($metaSet);

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'phlexible_api_metaset_get_metaset', array('metasetId' => $metaSet->getId()),
                        true // absolute
                    )
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }

    /**
     * List fields.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/fields", name="metasets_sets_fields")
     */
    public function fieldsAction(Request $request)
    {
        $id = $request->get('id');

        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $metaSet = $metaSetManager->find($id);
        $fields = $metaSet->getFields();

        $data = array();
        foreach ($fields as $field) {
            $data[] = array(
                'id' => $field->getId(),
                'key' => $field->getName(),
                'type' => $field->getType(),
                'required' => $field->isRequired(),
                'synchronized' => $field->isSynchronized(),
                'readonly' => $field->isReadonly(),
                'options' => $field->getOptions(),
            );
        }

        return new JsonResponse(array('values' => $data));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="metasets_values")
     */
    public function valuesAction(Request $request)
    {
        $sourceId = $request->get('source_id');
        $language = $request->get('language', 'en');

        $datasourceManager = $this->get('phlexible_data_source.data_source_manager');
        $datasource = $datasourceManager->find($sourceId);
        $keys = $datasource->getValuesForLanguage($language);

        $data = array();
        foreach ($keys as $key) {
            if (!$key) {
                continue;
            }

            $data[] = array('key' => $key, 'value' => $key);
        }

        return new JsonResponse(array('values' => $data));
    }

    /**
     * Create set.
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="metasets_sets_create")
     */
    public function createAction(Request $request)
    {
        $name = $request->get('name', 'new_set');

        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');

        if ($metaSetManager->findOneByName($name)) {
            return new ResultResponse(false, 'Name already in use.');
        }

        $metaSet = $metaSetManager->createMetaSet();
        $metaSet
            ->setId(Uuid::generate())
            ->setName($name)
            ->setCreatedBy($this->getUser()->getDisplayName())
            ->setCreatedAt(new \DateTime())
            ->setModifiedBy($this->getUser()->getDisplayName())
            ->setModifiedAt(new \DateTime());

        $metaSetManager->updateMetaSet($metaSet);

        return new ResultResponse(true, "Meta Set {$metaSet->getName()} created.");
    }

    /**
     * Rename set.
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/rename", name="metasets_sets_rename")
     */
    public function renameAction(Request $request)
    {
        $id = $request->get('id');
        $name = $request->get('name');

        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');

        if ($metaSetManager->findOneByName($name)) {
            return new ResultResponse(false, 'Name already in use.');
        }

        $metaSet = $metaSetManager->find($id);
        $oldName = $metaSet->getName();

        $metaSet->setName($name);
        $metaSetManager->updateMetaSet($metaSet);

        return new ResultResponse(true, "Meta Set $oldName renamed to $name.");
    }

    /**
     * Save set.
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="metasets_sets_save")
     */
    public function saveAction(Request $request)
    {
        $id = $request->get('id');
        $data = $request->get('data');
        $data = json_decode($data, true);

        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $metaSet = $metaSetManager->find($id);

        $metaSet->setRevision($metaSet->getRevision() + 1);

        $fields = array();
        foreach ($metaSet->getFields() as $field) {
            $fields[$field->getId()] = $field;
        }

        foreach ($data as $item) {
            if (!empty($item['options'])) {
                $options = array();
                foreach (explode(',', $item['options']) as $key => $value) {
                    $options[$key] = trim($value);
                }
                $item['options'] = implode(',', $options);
            }

            if (isset($fields[$item['id']])) {
                $field = $fields[$item['id']];
                unset($fields[$item['id']]);
            } else {
                $field = $metaSetManager->createMetaSetField();
            }

            $field
                ->setId(Uuid::generate())
                ->setName($item['key'])
                ->setMetaSet($metaSet)
                ->setType($item['type'])
                ->setRequired(!empty($item['required']) ? 1 : 0)
                ->setSynchronized(!empty($item['synchronized']) ? 1 : 0)
                ->setReadonly(!empty($item['readonly']) ? 1 : 0)
                ->setOptions(!empty($item['options']) ? $item['options'] : null);

            $metaSet->addField($field);
        }

        $metaSet
            ->setModifiedBy($this->getUser()->getDisplayName())
            ->setModifiedAt(new \DateTime());

        foreach ($fields as $field) {
            $metaSet->removeField($field);
        }

        $metaSetManager->updateMetaSet($metaSet);

        return new ResultResponse(true, "Fields saved for set {$metaSet->getName()}.");
    }
}
