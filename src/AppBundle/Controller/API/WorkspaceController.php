<?php
namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Workspace;
use AppBundle\Form\WorkspaceType;

/**
 * Workspace controller.
 */
class WorkspaceController extends Controller
{

    /**
     * 
     *
     */
    public function leadsAction(Workspace $workspace)
    {
        $this->denyAccessUnlessGranted('view', $workspace);
        return new JsonResponse([]);
    }

    /**
     * 
     *
     */
    public function leadsDiscoverAction(Workspace $workspace)
    {
        $this->denyAccessUnlessGranted('view', $workspace);
        return new JsonResponse([]);
    }

    /**
     * 
     *
     */
    public function exportDealsAction(Workspace $workspace)
    {
        $this->denyAccessUnlessGranted('view', $workspace);
        return new JsonResponse([]);
    }

    /**
     * Finds and displays a Workspace entity.
     */
    public function showAction(Request $request, Workspace $workspace)
    {
        $this->denyAccessUnlessGranted('view', $workspace);
        if ($request->isMethod('PUT')) {
            $data = $request->request;
            $update = false;
            foreach ($data as $k => $v) {
                switch ($k) {
                case 'name':
                    $workspace->setName($v);
                    $update = true;
                    break;
                case 'is_enabled':
                    $workspace->setIsEnabled($v);
                    $update = true;
                    break;
                default:
                    break;
                }
            }
            if ($update) {
                $workspace->setUpdatedAt(time());
                $em = $this->getDoctrine()->getManager();
                $em->persist($workspace);
                $em->flush();
            }
        }
        $data = $workspace->toArray();
        //TODO: Review Membership, replace stubbed response
        $data['membership'] = json_decode('{"role":"owner","credit_balance":6,"accepted_deal_count":0,"is_enabled":true}');        
        return new JsonResponse($data);
    }
}
