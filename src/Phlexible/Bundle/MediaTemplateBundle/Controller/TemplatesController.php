<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Component\MediaTemplate\Domain\AudioTemplate;
use Phlexible\Component\MediaTemplate\Domain\ImageTemplate;
use Phlexible\Component\MediaTemplate\Domain\VideoTemplate;
use Phlexible\Component\MediaTemplate\Exception\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Templates controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediatemplates/templates")
 * @Security("is_granted('ROLE_MEDIA_TEMPLATES')")
 */
class TemplatesController extends Controller
{
    /**
     * List mediatemplates
     *
     * @return JsonResponse
     * @Route("/list", name="mediatemplates_templates_list")
     */
    public function listAction()
    {
        $repository = $this->get('phlexible_media_template.template_manager');

        $allTemplates = $repository->findAll();

        $templates = array();
        foreach ($allTemplates as $template) {
            if (substr($template->getKey(), 0, 4) === '_mm_') {
                continue;
            }

            $templates[] = array(
                'key'  => $template->getKey(),
                'type' => $template->getType()
            );
        }

        return new JsonResponse(array('templates' => $templates));
    }

    /**
     * Create mediatemplate
     *
     * @param Request $request
     *
     * @throws \InvalidArgumentException
     * @return ResultResponse
     * @Route("/create", name="mediatemplates_templates_create")
     */
    public function createAction(Request $request)
    {
        $templateRepository = $this->get('phlexible_media_template.template_manager');

        $type = $request->get('type');
        $key  = $request->get('key');

        switch ($type) {
            case 'image':
                $template = new ImageTemplate();
                $template->setCache(false);
                break;
            case 'video':
                $template = new VideoTemplate();
                $template->setCache(true);
                break;
            case 'audio':
                $template = new AudioTemplate();
                $template->setCache(true);
                break;
            default:
                throw new InvalidArgumentException("Unknown template type $type");
        }

        $template->setKey($key);

        $templateRepository->updateTemplate($template);

        return new ResultResponse(true, 'New "' . $type . '" template "' . $key . '" created.');
    }
}
