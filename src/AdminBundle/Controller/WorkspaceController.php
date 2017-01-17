<?php

namespace AdminBundle\Controller;

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
        return new JsonResponse([]);
    }
    
    /**
     * 
     *
     */
    public function leadsDiscoverAction(Workspace $workspace)
    {
        return new JsonResponse([]);
    }   
    
    /**
     * 
     *
     */
    public function exportDealsAction(Workspace $workspace)
    {
        return new JsonResponse([]);
    }      
    
    /**
     * Lists all Workspace entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $workspaces = $em->getRepository('AppBundle:Workspace')->findAll();

        return $this->render(
            'workspace/index.html.twig', array(
            'workspaces' => $workspaces,
            )
        );
    }

    /**
     * Creates a new Workspace entity.
     */
    public function newAction(Request $request)
    {
        $workspace = new Workspace();
        $form = $this->createForm('AdminBundle\Form\WorkspaceType', $workspace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($workspace);
            $em->flush();

            return $this->redirectToRoute('workspace_show', array('id' => $workspace->getId()));
        }

        return $this->render(
            'workspace/new.html.twig', array(
            'workspace' => $workspace,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Workspace entity.
     */
    public function showAction(Workspace $workspace)
    {
        return new JsonResponse(json_decode('{"id":1,"name":"Workspace","type":"standard","account_id":1,"member_count":2,"credit_balance":0,"accepted_deal_count":0,"pipeline_count":0,"workflow_count":2,"source_count":1,"enabled_member_count":2,"enabled_pipeline_count":0,"enabled_workflow_count":1,"enabled_source_count":0,"daily_leads_scanned":0,"is_enabled":true,"created_at":1483748729,"updated_at":0,"membership":{"role":"owner","credit_balance":6,"accepted_deal_count":0,"is_enabled":true},"workspace_members":[{"id":1,"user_id":1,"workspace_id":1,"role":"owner","credit_balance":6,"accepted_deal_count":0,"extra":{},"owner_id":1,"is_confirmed":true,"is_enabled":true,"created_at":1483748729,"updated_at":0,"user":{"id":1,"first_name":"sdfgsdfgsdfg","last_name":"sdfgsdfgsdfg","avatar":null,"email":"bob@test.com","integrations":[]}},{"id":2,"user_id":0,"workspace_id":1,"role":"admin","credit_balance":0,"accepted_deal_count":0,"extra":{"name":"asdfasdfa","email":"asdfasdf@asdfasdf.com"},"owner_id":1,"is_confirmed":false,"is_enabled":true,"created_at":1483756100,"updated_at":0,"user":null}]}'));
    }

    /**
     * Displays a form to edit an existing Workspace entity.
     */
    public function editAction(Request $request, Workspace $workspace)
    {
        $deleteForm = $this->createDeleteForm($workspace);
        $editForm = $this->createForm('AdminBundle\Form\WorkspaceType', $workspace);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($workspace);
            $em->flush();

            return $this->redirectToRoute('workspace_edit', array('id' => $workspace->getId()));
        }

        return $this->render(
            'workspace/edit.html.twig', array(
            'workspace' => $workspace,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Workspace entity.
     */
    public function deleteAction(Request $request, Workspace $workspace)
    {
        $form = $this->createDeleteForm($workspace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($workspace);
            $em->flush();
        }

        return $this->redirectToRoute('workspace_index');
    }

    /**
     * Creates a form to delete a Workspace entity.
     *
     * @param Workspace $workspace The Workspace entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Workspace $workspace)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('workspace_delete', array('id' => $workspace->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
