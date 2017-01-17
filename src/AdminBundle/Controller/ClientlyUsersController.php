<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\ClientlyUsers;
use AppBundle\Form\ClientlyUsersType;

/**
 * ClientlyUsers controller.
 */
class ClientlyUsersController extends Controller
{
    /**
     * Lists all ClientlyUsers entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clientlyUsers = $em->getRepository('AppBundle:ClientlyUsers')->findAll();

        return $this->render(
            'clientlyusers/index.html.twig', array(
            'clientlyUsers' => $clientlyUsers,
            )
        );
    }

    /**
     * Creates a new ClientlyUsers entity.
     */
    public function newAction(Request $request)
    {
        $clientlyUser = new ClientlyUsers();
        $form = $this->createForm('AdminBundle\Form\ClientlyUsersType', $clientlyUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlyUser);
            $em->flush();

            return $this->redirectToRoute('clientlyusers_show', array('id' => $clientlyUser->getId()));
        }

        return $this->render(
            'clientlyusers/new.html.twig', array(
            'clientlyUser' => $clientlyUser,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a ClientlyUsers entity.
     */
    public function showAction(ClientlyUsers $clientlyUser)
    {
        $deleteForm = $this->createDeleteForm($clientlyUser);

        return $this->render(
            'clientlyusers/show.html.twig', array(
            'clientlyUser' => $clientlyUser,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing ClientlyUsers entity.
     */
    public function editAction(Request $request, ClientlyUsers $clientlyUser)
    {
        $deleteForm = $this->createDeleteForm($clientlyUser);
        $editForm = $this->createForm('AdminBundle\Form\ClientlyUsersType', $clientlyUser);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlyUser);
            $em->flush();

            return $this->redirectToRoute('clientlyusers_edit', array('id' => $clientlyUser->getId()));
        }

        return $this->render(
            'clientlyusers/edit.html.twig', array(
            'clientlyUser' => $clientlyUser,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a ClientlyUsers entity.
     */
    public function deleteAction(Request $request, ClientlyUsers $clientlyUser)
    {
        $form = $this->createDeleteForm($clientlyUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($clientlyUser);
            $em->flush();
        }

        return $this->redirectToRoute('clientlyusers_index');
    }

    /**
     * Creates a form to delete a ClientlyUsers entity.
     *
     * @param ClientlyUsers $clientlyUser The ClientlyUsers entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ClientlyUsers $clientlyUser)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('clientlyusers_delete', array('id' => $clientlyUser->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
