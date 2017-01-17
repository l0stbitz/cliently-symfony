<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\ClientlySubIndustries;
use AppBundle\Form\ClientlySubIndustriesType;

/**
 * ClientlySubIndustries controller.
 */
class ClientlySubIndustriesController extends Controller
{
    /**
     * Lists all ClientlySubIndustries entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clientlySubIndustries = $em->getRepository('AppBundle:ClientlySubIndustries')->findAll();

        return $this->render(
            'clientlysubindustries/index.html.twig', array(
            'clientlySubIndustries' => $clientlySubIndustries,
            )
        );
    }

    /**
     * Creates a new ClientlySubIndustries entity.
     */
    public function newAction(Request $request)
    {
        $clientlySubIndustry = new ClientlySubIndustries();
        $form = $this->createForm('AdminBundle\Form\ClientlySubIndustriesType', $clientlySubIndustry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlySubIndustry);
            $em->flush();

            return $this->redirectToRoute('clientlysubindustries_show', array('id' => $clientlySubIndustry->getId()));
        }

        return $this->render(
            'clientlysubindustries/new.html.twig', array(
            'clientlySubIndustry' => $clientlySubIndustry,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a ClientlySubIndustries entity.
     */
    public function showAction(ClientlySubIndustries $clientlySubIndustry)
    {
        $deleteForm = $this->createDeleteForm($clientlySubIndustry);

        return $this->render(
            'clientlysubindustries/show.html.twig', array(
            'clientlySubIndustry' => $clientlySubIndustry,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing ClientlySubIndustries entity.
     */
    public function editAction(Request $request, ClientlySubIndustries $clientlySubIndustry)
    {
        $deleteForm = $this->createDeleteForm($clientlySubIndustry);
        $editForm = $this->createForm('AdminBundle\Form\ClientlySubIndustriesType', $clientlySubIndustry);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlySubIndustry);
            $em->flush();

            return $this->redirectToRoute('clientlysubindustries_edit', array('id' => $clientlySubIndustry->getId()));
        }

        return $this->render(
            'clientlysubindustries/edit.html.twig', array(
            'clientlySubIndustry' => $clientlySubIndustry,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a ClientlySubIndustries entity.
     */
    public function deleteAction(Request $request, ClientlySubIndustries $clientlySubIndustry)
    {
        $form = $this->createDeleteForm($clientlySubIndustry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($clientlySubIndustry);
            $em->flush();
        }

        return $this->redirectToRoute('clientlysubindustries_index');
    }

    /**
     * Creates a form to delete a ClientlySubIndustries entity.
     *
     * @param ClientlySubIndustries $clientlySubIndustry The ClientlySubIndustries entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ClientlySubIndustries $clientlySubIndustry)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('clientlysubindustries_delete', array('id' => $clientlySubIndustry->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
