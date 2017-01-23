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
     * Lists all Integration entities.
     */
    public function indexAction()
    {
        $integrations = $this->getUser()->getIntegrations();
        $ints = [];
        foreach($integrations as $i){
            $ints[] = $i->toArray();
        }
        return new JsonResponse($ints);
    }

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
        $user = $this->getUser();
        $integrations = $user->getIntegrations();
        foreach($integrations as $i){
            if($i->getType() == 1){
                //{"integration":{"id":"473","name":"Clientlytest","code":"clientlytest@hotmail.com","values":{"fullname":"Clientlytest","email":"clientlytest@hotmail.com","password":"Cleintly2017","imap_server":"imap-mail.outlook.com","imap_port":993,"smtp_server":"smtp-mail.outlook.com","smtp_port":587},"handle":"clientlytest@hotmail.com","is_primary":"1","status":"2","created_at":"1484884326"}}
                return new JsonResponse(['integration'=>$i->toArray()]);
            }
        }
        return new JsonResponse(["errors"=>[["code"=>1,"message"=>"failure"]]]);
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
}
