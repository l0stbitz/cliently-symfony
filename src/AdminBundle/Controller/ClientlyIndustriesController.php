<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\ClientlyIndustries;
use AppBundle\Form\ClientlyIndustriesType;

/**
 * ClientlyIndustries controller.
 */
class ClientlyIndustriesController extends Controller
{
    /**
     * Lists all ClientlyIndustries entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clientlyIndustries = $em->getRepository('AppBundle:ClientlyIndustries')->findAll();

        return $this->render(
            'clientlyindustries/index.html.twig', array(
            'clientlyIndustries' => $clientlyIndustries,
            )
        );
    }

    /**
     * Creates a new ClientlyIndustries entity.
     */
    public function newAction(Request $request)
    {
        $clientlyIndustry = new ClientlyIndustries();
        $form = $this->createForm('AdminBundle\Form\ClientlyIndustriesType', $clientlyIndustry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlyIndustry);
            $em->flush();

            return $this->redirectToRoute('clientlyindustries_show', array('id' => $clientlyIndustry->getId()));
        }

        return $this->render(
            'clientlyindustries/new.html.twig', array(
            'clientlyIndustry' => $clientlyIndustry,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a ClientlyIndustries entity.
     */
    public function showAction(ClientlyIndustries $clientlyIndustry)
    {
        $deleteForm = $this->createDeleteForm($clientlyIndustry);

        return $this->render(
            'clientlyindustries/show.html.twig', array(
            'clientlyIndustry' => $clientlyIndustry,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing ClientlyIndustries entity.
     */
    public function editAction(Request $request, ClientlyIndustries $clientlyIndustry)
    {
        $deleteForm = $this->createDeleteForm($clientlyIndustry);
        $editForm = $this->createForm('AdminBundle\Form\ClientlyIndustriesType', $clientlyIndustry);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlyIndustry);
            $em->flush();

            return $this->redirectToRoute('clientlyindustries_edit', array('id' => $clientlyIndustry->getId()));
        }

        return $this->render(
            'clientlyindustries/edit.html.twig', array(
            'clientlyIndustry' => $clientlyIndustry,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a ClientlyIndustries entity.
     */
    public function deleteAction(Request $request, ClientlyIndustries $clientlyIndustry)
    {
        $form = $this->createDeleteForm($clientlyIndustry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($clientlyIndustry);
            $em->flush();
        }

        return $this->redirectToRoute('clientlyindustries_index');
    }

    /**
     * Creates a form to delete a ClientlyIndustries entity.
     *
     * @param ClientlyIndustries $clientlyIndustry The ClientlyIndustries entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ClientlyIndustries $clientlyIndustry)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('clientlyindustries_delete', array('id' => $clientlyIndustry->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
