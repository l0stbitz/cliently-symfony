<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\AccountMember;
use AppBundle\Form\AccountMemberType;

/**
 * AccountMember controller.
 */
class AccountMemberController extends Controller
{
    /**
     * Lists all AccountMember entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $accountMembers = $em->getRepository('AppBundle:AccountMember')->findAll();

        return $this->render(
            'accountmember/index.html.twig', array(
            'accountMembers' => $accountMembers,
            )
        );
    }

    /**
     * Creates a new AccountMember entity.
     */
    public function newAction(Request $request)
    {
        $accountMember = new AccountMember();
        $form = $this->createForm('AdminBundle\Form\AccountMemberType', $accountMember);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($accountMember);
            $em->flush();

            return $this->redirectToRoute('accountmember_show', array('id' => $accountMember->getId()));
        }

        return $this->render(
            'accountmember/new.html.twig', array(
            'accountMember' => $accountMember,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a AccountMember entity.
     */
    public function showAction(AccountMember $accountMember)
    {
        $deleteForm = $this->createDeleteForm($accountMember);

        return $this->render(
            'accountmember/show.html.twig', array(
            'accountMember' => $accountMember,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing AccountMember entity.
     */
    public function editAction(Request $request, AccountMember $accountMember)
    {
        $deleteForm = $this->createDeleteForm($accountMember);
        $editForm = $this->createForm('AdminBundle\Form\AccountMemberType', $accountMember);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($accountMember);
            $em->flush();

            return $this->redirectToRoute('accountmember_edit', array('id' => $accountMember->getId()));
        }

        return $this->render(
            'accountmember/edit.html.twig', array(
            'accountMember' => $accountMember,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a AccountMember entity.
     */
    public function deleteAction(Request $request, AccountMember $accountMember)
    {
        $form = $this->createDeleteForm($accountMember);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($accountMember);
            $em->flush();
        }

        return $this->redirectToRoute('accountmember_index');
    }

    /**
     * Creates a form to delete a AccountMember entity.
     *
     * @param AccountMember $accountMember The AccountMember entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(AccountMember $accountMember)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('accountmember_delete', array('id' => $accountMember->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
