<?php
namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Workspace;
use AppBundle\Entity\WorkspaceMember;
use AppBundle\Form\WorkspaceMemberType;

/**
 * WorkspaceMember controller.
 */
class WorkspaceMemberController extends Controller
{

    /**
     * Lists all WorkspaceMember entities.
     */
    public function indexAction(Request $request, Workspace $workspace)
    {
        $this->denyAccessUnlessGranted('view', $workspace);
        if ($request->isMethod('POST')) {
            //TODO: Use proper validation form
            //Validate email
            $data = $request->request;
            $em = $this->getDoctrine()->getManager();
            $member = new WorkspaceMember();
            $member->setExtra(json_encode(["name" => $data->get('user_name'), "email" => $data->get('user_email')]));
            $member->setRole($data->get('role'));
            $member->setIsEnabled((int) $data->get('is_enabled'));
            $member->setWorkspace($workspace);
            $member->setOwnerId($this->getUser()->getId());
            $em->persist($member);
            $em->flush();
            return new JsonResponse(["success" => ["code" => 0, "message" => "success"]]);
        }
        return new JsonResponse($workspace->getWorkspaceMembersArray());
        //return new JsonResponse(json_decode('[{"id":1,"user_id":1,"workspace_id":1,"role":"owner","credit_balance":6,"accepted_deal_count":0,"extra":{},"owner_id":1,"is_confirmed":true,"is_enabled":true,"created_at":1483748729,"updated_at":0,"user":{"id":1,"first_name":null,"last_name":null,"avatar":null,"email":"bob@test.com","integrations":[]}},{"id":2,"user_id":0,"workspace_id":1,"role":"admin","credit_balance":0,"accepted_deal_count":0,"extra":{"name":"asdfasdfa","email":"asdfasdf@asdfasdf.com"},"owner_id":1,"is_confirmed":false,"is_enabled":true,"created_at":1483756100,"updated_at":0,"user":null}]'));
    }

    /**
     * Creates a new WorkspaceMember entity.
     */
    public function newAction(Request $request)
    {
        $workspaceMember = new WorkspaceMember();
        $form = $this->createForm('AppBundle\Form\WorkspaceMemberType', $workspaceMember);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($workspaceMember);
            $em->flush();

            return $this->redirectToRoute('workspacemember_show', array('id' => $workspaceMember->getId()));
        }

        return $this->render(
            'workspacemember/new.html.twig', array(
                'workspaceMember' => $workspaceMember,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a WorkspaceMember entity.
     */
    public function showAction(Request $request, WorkspaceMember $workspaceMember)
    {
        $this->denyAccessUnlessGranted('view', $workspaceMember);
        if ($request->isMethod('PUT')) {
            $data = $request->request;
            $update = false;
            foreach ($data as $k => $v) {
                switch ($k) {
                case 'is_enabled':
                    $workspaceMember->setIsEnabled($v);
                    $update = true;
                    break;
                default:
                    break;
                }
            }
            if ($update) {
                $workspaceMember->setUpdatedAt(time());
                $em = $this->getDoctrine()->getManager();
                $em->persist($workspaceMember);
                $em->flush();
            }
        }
        return new JsonResponse($workspaceMember->toArray());
    }
}
