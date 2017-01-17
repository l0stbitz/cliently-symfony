<?php
namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Stage;
use AppBundle\Entity\Client;
use AppBundle\Entity\Deal;
use AppBundle\Entity\XrefClientDeal;
use AppBundle\Form\StageType;

/**
 * Stage controller.
 */
class StageController extends Controller
{

    /**
     * 
     *
     */
    public function dealsAction(Request $request, Stage $stage)
    {
        $this->denyAccessUnlessGranted('view', $stage);
        if ($request->isMethod('POST')) {
            //create client
            $em = $this->getDoctrine()->getManager();
            $data = $request->request;
            $c = $data->get('client');
            $client = new Client();
            $client->setName($c['name'] ?? '');
            $client->setIsEnabled(1);
            $client->setWorkspace($stage->getPipeline()->getWorkspace());
            $client->setOwner($this->getUser());
            $em->persist($client);
            $em->flush();
            $deal = new Deal();
            $deal->setIsEnabled(1);
            $deal->setInitialClientId($client->getId());
            $deal->setStage($stage);
            $deal->setOwnerId($this->getUser()->getId());
            $em->persist($deal);
            $em->flush();
            $xref = new XrefClientDeal();
            $xref->setClientId($client->getId());
            $xref->setDealId($deal->getId());
            $xref->setOwnerId($this->getUser()->getId());
            $xref->setIsEnabled(1);
            $xref->setIsMain(1);
            $em->persist($xref);
            $em->flush();
            //create deal?
            //xref
            //deal workflow model?
            //create source with client data
            //{"id":2,"source_description":"","value":0,"is_enabled":true,"initial_client_id":2,"workflow_id":0,"stage_id":2,"action_values":[],"owner_id":1,"created_at":1484245555,"updated_at":0,"accessed_at":1484245555,"clients":[{"id":2,"name":"|Test","avatar":"","occupation":"","description":"","email":"","address_line1":"","address_line2":"","city":"","state":"","zip":"","country":"","coords":"","phone":"","social":[],"contacts":[],"new_events_count":0,"company_id":0,"source_id":0,"is_verified":false,"is_enabled":true,"created_at":1484245555,"updated_at":0}]}

            return new JsonResponse(json_decode('[{"success":[{"code":0,"message":"success"}]}'));
        }
        return new JsonResponse([]);
    }

    /**
     * Finds and displays a Stage entity.
     */
    public function showAction(Request $request, Stage $stage)
    {
        $this->denyAccessUnlessGranted('view', $stage);
        if ($request->isMethod('DELETE')) {
            //TODO: Handle move to other stage
            $data = $request->request;
            $em = $this->getDoctrine()->getManager();
            $em->remove($stage);
            $em->flush();

            return new JsonResponse(["success" => ["code" => 0, "message" => "success"]]);
        }
        if ($request->isMethod('PUT')) {
            return new JsonResponse(json_decode('[{"success":[{"code":0,"message":"success"}]}'));
        }
        return new JsonResponse([]);
    }
}
