<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\ClientlyIndustryLeads;
use AppBundle\Form\ClientlyIndustryLeadsType;

/**
 * ClientlyIndustryLeads controller.
 */
class ClientlyIndustryLeadsController extends Controller
{
    /**
     * Lists all ClientlyIndustryLeads entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clientlyIndustryLeads = $em->getRepository('AppBundle:ClientlyIndustryLeads')->findAll();

        return $this->render(
            'clientlyindustryleads/index.html.twig', array(
            'clientlyIndustryLeads' => $clientlyIndustryLeads,
            )
        );
    }

    /**
     * Creates a new ClientlyIndustryLeads entity.
     */
    public function newAction(Request $request)
    {
        $clientlyIndustryLead = new ClientlyIndustryLeads();
        $form = $this->createForm('AdminBundle\Form\ClientlyIndustryLeadsType', $clientlyIndustryLead);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlyIndustryLead);
            $em->flush();

            return $this->redirectToRoute('clientlyindustryleads_show', array('id' => $clientlyIndustryLead->getId()));
        }

        return $this->render(
            'clientlyindustryleads/new.html.twig', array(
            'clientlyIndustryLead' => $clientlyIndustryLead,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a ClientlyIndustryLeads entity.
     */
    public function showAction(ClientlyIndustryLeads $clientlyIndustryLead)
    {
        $deleteForm = $this->createDeleteForm($clientlyIndustryLead);

        return $this->render(
            'clientlyindustryleads/show.html.twig', array(
            'clientlyIndustryLead' => $clientlyIndustryLead,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing ClientlyIndustryLeads entity.
     */
    public function editAction(Request $request, ClientlyIndustryLeads $clientlyIndustryLead)
    {
        $deleteForm = $this->createDeleteForm($clientlyIndustryLead);
        $editForm = $this->createForm('AdminBundle\Form\ClientlyIndustryLeadsType', $clientlyIndustryLead);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlyIndustryLead);
            $em->flush();

            return $this->redirectToRoute('clientlyindustryleads_edit', array('id' => $clientlyIndustryLead->getId()));
        }

        return $this->render(
            'clientlyindustryleads/edit.html.twig', array(
            'clientlyIndustryLead' => $clientlyIndustryLead,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a ClientlyIndustryLeads entity.
     */
    public function deleteAction(Request $request, ClientlyIndustryLeads $clientlyIndustryLead)
    {
        $form = $this->createDeleteForm($clientlyIndustryLead);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($clientlyIndustryLead);
            $em->flush();
        }

        return $this->redirectToRoute('clientlyindustryleads_index');
    }

    /**
     * Creates a form to delete a ClientlyIndustryLeads entity.
     *
     * @param ClientlyIndustryLeads $clientlyIndustryLead The ClientlyIndustryLeads entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ClientlyIndustryLeads $clientlyIndustryLead)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('clientlyindustryleads_delete', array('id' => $clientlyIndustryLead->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
