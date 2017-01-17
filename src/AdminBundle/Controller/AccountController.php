<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Account;
use AppBundle\Form\AccountType;

/**
 * Account controller.
 */
class AccountController extends Controller
{
    /**
     * Lists all Account entities.
     */
    public function workspaceAction()
    {
        return new JsonResponse(json_decode('[{"id":1,"name":"Workspace","type":"standard","account_id":1,"member_count":2,"credit_balance":0,"accepted_deal_count":0,"pipeline_count":0,"workflow_count":2,"source_count":1,"enabled_member_count":2,"enabled_pipeline_count":0,"enabled_workflow_count":1,"enabled_source_count":0,"daily_leads_scanned":0,"is_enabled":true,"created_at":1483748729,"updated_at":0}]'));
        /*$em = $this->getDoctrine()->getManager();

        $accounts = $em->getRepository('AppBundle:Account')->findAll();

        return $this->render('AdminBundle:account:index.html.twig', array(
            'accounts' => $accounts,
        ));*/
    }
    /**
     * Lists all Account entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $accounts = $em->getRepository('AppBundle:Account')->findAll();

        return $this->render(
            'AdminBundle:account:index.html.twig', array(
            'accounts' => $accounts,
            )
        );
    }

    /**
     * Creates a new Account entity.
     */
    public function newAction(Request $request)
    {
        $account = new Account();
        $form = $this->createForm('AdminBundle\Form\AccountType', $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($account);
            $em->flush();

            return $this->redirectToRoute('account_show', array('id' => $account->getId()));
        }

        return $this->render(
            'account/new.html.twig', array(
            'account' => $account,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Account entity.
     */
    public function showAction(Account $account)
    {
        return new JsonResponse(json_decode('{"id":1,"name":"","type":"main","plan_id":1,"plan_class":"free","next_plan_id":1,"next_plan_class":"free","member_count":1,"credit_balance":0,"accepted_deal_count":0,"workspace_count":0,"pipeline_count":0,"workflow_count":2,"source_count":1,"enabled_member_count":1,"enabled_workspace_count":0,"enabled_pipeline_count":0,"enabled_workflow_count":1,"enabled_source_count":0,"daily_leads_scanned":0,"plan_started_at":1483748729,"is_enabled":true,"created_at":1483748729,"updated_at":0}'));
        $deleteForm = $this->createDeleteForm($account);

        return $this->render(
            'account/show.html.twig', array(
            'account' => $account,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Account entity.
     */
    public function editAction(Request $request, Account $account)
    {
        $deleteForm = $this->createDeleteForm($account);
        $editForm = $this->createForm('AdminBundle\Form\AccountType', $account);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($account);
            $em->flush();

            return $this->redirectToRoute('account_edit', array('id' => $account->getId()));
        }

        return $this->render(
            'account/edit.html.twig', array(
            'account' => $account,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Account entity.
     */
    public function deleteAction(Request $request, Account $account)
    {
        $form = $this->createDeleteForm($account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($account);
            $em->flush();
        }

        return $this->redirectToRoute('account_index');
    }

    /**
     * Creates a form to delete a Account entity.
     *
     * @param Account $account The Account entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Account $account)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('account_delete', array('id' => $account->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
