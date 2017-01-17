<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;

/**
 * User controller.
 */
class UserController extends Controller
{
    
    /**
     * meAction
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
    public function meAction(Request $request)
    {
        if($request->isMethod('PUT')) {
            return new JsonResponse(json_decode('{"success":[{"code":0,"message":"success"}]}'));
        }
        //Existing user
        return new JsonResponse(json_decode('{"id":1,"email":"bob@test.com","first_name":"sdfgsdfgsdfg","last_name":"sdfgsdfgsdfg","location":"Edmonton, Alberta, CA","coords":"53.544389,-113.4909267","avatar":null,"company_logo":null,"company_name":"asdlkfjasdlfkjasdlkfja","phone":"345345345345","wizard":-1,"industries":["1"],"integration_avatar":null,"integration_type":null,"accounts":[{"id":1,"name":"","type":"main","plan_id":1,"plan_class":"free","next_plan_id":1,"next_plan_class":"free","member_count":1,"credit_balance":0,"accepted_deal_count":0,"workspace_count":0,"pipeline_count":0,"workflow_count":2,"source_count":1,"enabled_member_count":1,"enabled_workspace_count":0,"enabled_pipeline_count":0,"enabled_workflow_count":1,"enabled_source_count":0,"daily_leads_scanned":0,"plan_started_at":1483748729,"is_enabled":true,"created_at":1483748729,"updated_at":0,"membership":{"role":"owner","is_enabled":true}}],"workspaces":[{"id":1,"name":"Workspace","type":"standard","account_id":1,"member_count":2,"credit_balance":0,"accepted_deal_count":0,"pipeline_count":0,"workflow_count":2,"source_count":1,"enabled_member_count":2,"enabled_pipeline_count":0,"enabled_workflow_count":1,"enabled_source_count":0,"daily_leads_scanned":0,"is_enabled":true,"created_at":1483748729,"updated_at":0,"membership":{"role":"owner","credit_balance":6,"accepted_deal_count":0,"is_enabled":true}}],"integrations":[]}'));
        //New User
        return new JsonResponse(json_decode('{"id":1,"email":"bob@test.com","first_name":null,"last_name":null,"location":null,"coords":null,"avatar":null,"company_logo":null,"company_name":null,"phone":null,"wizard":0,"industries":null,"integration_avatar":null,"integration_type":null,"accounts":[{"id":1,"name":"","type":"main","plan_id":1,"plan_class":"free","next_plan_id":1,"next_plan_class":"free","member_count":1,"credit_balance":0,"accepted_deal_count":0,"workspace_count":0,"pipeline_count":0,"workflow_count":2,"source_count":1,"enabled_member_count":1,"enabled_workspace_count":0,"enabled_pipeline_count":0,"enabled_workflow_count":1,"enabled_source_count":0,"daily_leads_scanned":0,"plan_started_at":1483748729,"is_enabled":true,"created_at":1483748729,"updated_at":0,"membership":{"role":"owner","is_enabled":true}}],"workspaces":[{"id":1,"name":"Workspace","type":"standard","account_id":1,"member_count":2,"credit_balance":0,"accepted_deal_count":0,"pipeline_count":0,"workflow_count":2,"source_count":1,"enabled_member_count":2,"enabled_pipeline_count":0,"enabled_workflow_count":1,"enabled_source_count":0,"daily_leads_scanned":0,"is_enabled":true,"created_at":1483748729,"updated_at":0,"membership":{"role":"owner","credit_balance":6,"accepted_deal_count":0,"is_enabled":true}}],"integrations":[]}'));
    }
    
    /**
     * emailValidateAction
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
    public function emailValidateAction(Request $request)
    {
        return new JsonResponse(['success'=>true]);
    }
    
    /**
     * Lists all User entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();

        return $this->render(
            'user/index.html.twig', array(
            'users' => $users,
            )
        );
    }

    /**
     * Creates a new User entity.
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('AdminBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_show', array('id' => $user->getId()));
        }

        return $this->render(
            'user/new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a User entity.
     */
    public function showAction(User $user)
    {
        $deleteForm = $this->createDeleteForm($user);

        return $this->render(
            'user/show.html.twig', array(
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     */
    public function editAction(Request $request, User $user)
    {
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('AdminBundle\Form\UserType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
        }

        return $this->render(
            'user/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a User entity.
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * Creates a form to delete a User entity.
     *
     * @param User $user The User entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
