<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\ClientlySetting;
use AppBundle\Form\ClientlySettingType;

/**
 * ClientlySetting controller.
 */
class ClientlySettingController extends Controller
{
    /**
     * Lists all ClientlySetting entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clientlySettings = $em->getRepository('AppBundle:ClientlySetting')->findAll();

        return $this->render(
            'clientlysetting/index.html.twig', array(
            'clientlySettings' => $clientlySettings,
            )
        );
    }

    /**
     * Creates a new ClientlySetting entity.
     */
    public function newAction(Request $request)
    {
        $clientlySetting = new ClientlySetting();
        $form = $this->createForm('AdminBundle\Form\ClientlySettingType', $clientlySetting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlySetting);
            $em->flush();

            return $this->redirectToRoute('clientlysetting_show', array('id' => $clientlySetting->getId()));
        }

        return $this->render(
            'clientlysetting/new.html.twig', array(
            'clientlySetting' => $clientlySetting,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a ClientlySetting entity.
     */
    public function showAction(ClientlySetting $clientlySetting)
    {
        $deleteForm = $this->createDeleteForm($clientlySetting);

        return $this->render(
            'clientlysetting/show.html.twig', array(
            'clientlySetting' => $clientlySetting,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing ClientlySetting entity.
     */
    public function editAction(Request $request, ClientlySetting $clientlySetting)
    {
        $deleteForm = $this->createDeleteForm($clientlySetting);
        $editForm = $this->createForm('AdminBundle\Form\ClientlySettingType', $clientlySetting);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlySetting);
            $em->flush();

            return $this->redirectToRoute('clientlysetting_edit', array('id' => $clientlySetting->getId()));
        }

        return $this->render(
            'clientlysetting/edit.html.twig', array(
            'clientlySetting' => $clientlySetting,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a ClientlySetting entity.
     */
    public function deleteAction(Request $request, ClientlySetting $clientlySetting)
    {
        $form = $this->createDeleteForm($clientlySetting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($clientlySetting);
            $em->flush();
        }

        return $this->redirectToRoute('clientlysetting_index');
    }

    /**
     * Creates a form to delete a ClientlySetting entity.
     *
     * @param ClientlySetting $clientlySetting The ClientlySetting entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ClientlySetting $clientlySetting)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('clientlysetting_delete', array('id' => $clientlySetting->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
