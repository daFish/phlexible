<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Controller;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Component\MediaTemplate\Model\ImageTemplate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Media templates controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_MEDIA_TEMPLATES')")
 * @Prefix("/mediatemplate")
 * @NamePrefix("phlexible_mediatemplate_")
 */
class MediaTemplatesController extends FOSRestController
{
    /**
     * Get media templates
     *
     * @return Response
     *
     * @ApiDoc
     */
    public function getMediatemplatesAction()
    {
        $repository = $this->get('phlexible_media_template.template_manager');

        $mediaTemplates = $repository->findAll();

        return $this->handleView($this->view(
            array(
                'mediatemplates' => array_values($mediaTemplates),
                'count'          => count($mediaTemplates),
            )
        ));
    }

    /**
     * Create mediatemplate
     *
     * @param ImageTemplate $mediaTemplate
     *
     * @return Response
     *
     * @ParamConverter("mediatemplate", converter="fos_rest.request_body")
     * @Post("/mediatemplates")
     * @ApiDoc
     */
    public function postMediatemplatesAction(ImageTemplate $mediaTemplate)
    {
        $templateRepository = $this->get('phlexible_media_template.template_manager');

        $templateRepository->updateTemplate($mediaTemplate);

        return $this->handleView($this->view(
            array(
                'success' => true,
            )
        ));
    }

    /**
     * List variables
     *
     * @param Request $request
     *
     * @return Response
     */
    public function loadAction(Request $request)
    {
        $repository = $this->get('phlexible_media_template.template_manager');

        $templateKey = $request->get('template_key');

        $template = $repository->find($templateKey);
        $parameters = $template->getParameters();

        if (isset($parameters['method'])) {
            $parameters['xmethod'] = $parameters['method'];
            unset($parameters['method']);
        }

        return new Response(['success' => true, 'data' => $parameters]);
    }

    /**
     * Save variables
     *
     * @param Request $request
     *
     * @return Response
     */
    public function saveAction(Request $request)
    {
        $repository = $this->get('phlexible_media_template.template_manager');

        $templateKey = $request->get('template_key');
        $params = $request->request->all();

        unset($params['template_key'],
            $params['module'],
            $params['controller'],
            $params['action']);

        $template = $repository->find($templateKey);

        $params = $this->fixParams($params);

        foreach ($params as $key => $value) {
            $template->setParameter($key, $value);
        }

        $repository->updateTemplate($template);

        return new Response(true, 'Media template "' . $template->getKey() . '" saved.');
    }

    /**
     * @param array $params
     *
     * @return array
     */
    private function fixParams(array $params)
    {
        $qualityOverride = false;

        foreach ($params as $key => $value) {
            if ($key == 'xmethod') {
                $params['method'] = $value;
                unset($params['xmethod']);
            } elseif ($key == 'backgroundcolor' && !preg_match('/^\#[0-9A-Za-z]{6}$/', $value)) {
                $params['backgroundcolor'] = '';
            } elseif ($key == 'compression') {
                if (!$qualityOverride) {
                    $params['quality'] = 0;
                }
                $params['quality'] = $params['quality'] + $value * 10;
                $qualityOverride = true;
                unset($params['compression']);
            } elseif ($key == 'filtertype') {
                if (!$qualityOverride) {
                    $params['quality'] = 0;
                }
                $params['quality'] = $params['quality'] + $value;
                $qualityOverride = true;
                unset($params['filtertype']);
            }
        }

        return $params;
    }
}
