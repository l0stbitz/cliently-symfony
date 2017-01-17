<?php
namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Integration;
use AppBundle\Form\IntegrationType;

/**
 * Integration controller.
 */
class IntegrationController extends Controller
{

    /**
     * imapAction
     * Insert description here
     *
     * @param Request
     * @param $request
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function imapAction(Request $request)
    {
        return new JsonResponse(json_decode('{"errors":[{"code":1,"message":"failure"}]}'));
    }

    /**
     * slackAction
     * Insert description here
     *
     * @param Request
     * @param $request
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function slackAction(Request $request)
    {
        return new JsonResponse(json_decode('{"errors":[{"code":1,"message":"failure"}]}'));
    }

    /**
     * googleAction
     * Insert description here
     *
     * @param Request
     * @param $request
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function googleAction(Request $request)
    {
        return new JsonResponse(json_decode('{"errors":[{"code":1,"message":"failure"}]}'));
    }

    /**
     * twitterAction
     * Insert description here
     *
     * @param Request
     * @param $request
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function twitterAction(Request $request)
    {
        return new JsonResponse(json_decode('{"errors":[{"code":1,"message":"failure"}]}'));
    }

    /**
     * Lists all Integration entities.
     */
    public function indexAction()
    {
        return new JsonResponse([]);
    }
}
