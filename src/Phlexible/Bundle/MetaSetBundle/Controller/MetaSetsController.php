<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Sets controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Security("is_granted('ROLE_META_SETS')")
 */
class MetaSetsController extends FOSRestController
{
    /**
     * List sets
     *
     * @return JsonResponse
     */
    public function getMetaSetsAction()
    {
        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $metaSets = $metaSetManager->findAll();

        return $this->handleView($this->view(
            array(
                'metaSets' => $metaSets,
                'count'    => count($metaSets)
            )
        ));
    }

    /**
     * List fields
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

        $data = [];
        foreach ($fields as $field) {
            $data[] = [
                'id'           => $field->getId(),
                'key'          => $field->getName(),
                'type'         => $field->getType(),
                'required'     => $field->isRequired(),
                'synchronized' => $field->isSynchronized(),
                'readonly'     => $field->isReadonly(),
                'options'      => $field->getOptions(),
            ];
        }

        return new JsonResponse(['values' => $data]);
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

        $data = [];
        foreach ($keys as $key) {
            if (!$key) {
                continue;
            }

            $data[] = ['key' => $key, 'value' => $key];
        }

        return new JsonResponse(['values' => $data]);
    }
    /**
     * Create set
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
            ->setCreateUser($this->getUser()->getDisplayName())
            ->setCreatedAt(new \DateTime())
            ->setModifyUser($this->getUser()->getDisplayName())
            ->setModifiedAt(new \DateTime());

        $metaSetManager->updateMetaSet($metaSet);

        return new ResultResponse(true, "Meta Set {$metaSet->getName()} created.");
    }

    /**
     * Rename set
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
     * Save set
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

        $fields = [];
        foreach ($metaSet->getFields() as $field) {
            $fields[$field->getId()] = $field;
        }

        foreach ($data as $item) {
            if (!empty($item['options'])) {
                $options = [];
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
            ->setModifyUser($this->getUser()->getDisplayName())
            ->setModifiedAt(new \DateTime());

        foreach ($fields as $field) {
            $metaSet->removeField($field);
        }

        $metaSetManager->updateMetaSet($metaSet);

        return new ResultResponse(true, "Fields saved for set {$metaSet->getName()}.");
    }
}
