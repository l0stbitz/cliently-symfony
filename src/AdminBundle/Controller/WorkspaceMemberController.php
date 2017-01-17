<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
    public function indexAction()
    {
        return new JsonResponse(json_decode('[{"id":1,"user_id":1,"workspace_id":1,"role":"owner","credit_balance":6,"accepted_deal_count":0,"extra":{},"owner_id":1,"is_confirmed":true,"is_enabled":true,"created_at":1483748729,"updated_at":0,"user":{"id":1,"first_name":null,"last_name":null,"avatar":null,"email":"bob@test.com","integrations":[]}},{"id":2,"user_id":0,"workspace_id":1,"role":"admin","credit_balance":0,"accepted_deal_count":0,"extra":{"name":"asdfasdfa","email":"asdfasdf@asdfasdf.com"},"owner_id":1,"is_confirmed":false,"is_enabled":true,"created_at":1483756100,"updated_at":0,"user":null}]'));
        /* $em = $this->getDoctrine()->getManager();

        $workspaceMembers = $em->getRepository('AppBundle:WorkspaceMember')->findAll();

        return $this->render('workspacemember/index.html.twig', array(
            'workspaceMembers' => $workspaceMembers,
        ));*/
    }

    /**
     * Creates a new WorkspaceMember entity.
     */
    public function newAction(Request $request)
    {
        $workspaceMember = new WorkspaceMember();
        $form = $this->createForm('AdminBundle\Form\WorkspaceMemberType', $workspaceMember);
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
    public function showAction(WorkspaceMember $workspaceMember)
    {
        $deleteForm = $this->createDeleteForm($workspaceMember);

        return $this->render(
            'workspacemember/show.html.twig', array(
            'workspaceMember' => $workspaceMember,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing WorkspaceMember entity.
     */
    public function editAction(Request $request, WorkspaceMember $workspaceMember)
    {
        $deleteForm = $this->createDeleteForm($workspaceMember);
        $editForm = $this->createForm('AdminBundle\Form\WorkspaceMemberType', $workspaceMember);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($workspaceMember);
            $em->flush();

            return $this->redirectToRoute('workspacemember_edit', array('id' => $workspaceMember->getId()));
        }

        return $this->render(
            'workspacemember/edit.html.twig', array(
            'workspaceMember' => $workspaceMember,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a WorkspaceMember entity.
     */
    public function deleteAction(Request $request, WorkspaceMember $workspaceMember)
    {
        $form = $this->createDeleteForm($workspaceMember);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($workspaceMember);
            $em->flush();
        }

        return $this->redirectToRoute('workspacemember_index');
    }

    /**
     * Creates a form to delete a WorkspaceMember entity.
     *
     * @param WorkspaceMember $workspaceMember The WorkspaceMember entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(WorkspaceMember $workspaceMember)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('workspacemember_delete', array('id' => $workspaceMember->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
