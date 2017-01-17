<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Lead;
use AppBundle\Form\LeadType;

/**
 * Lead controller.
 */
class LeadController extends Controller
{
    /**
     * Lists all Lead entities.
     */
    public function indexAction()
    {
        return new JsonResponse(json_decode('{"count":0,"since":1483925378}'));
        /*$em = $this->getDoctrine()->getManager();

        $leads = $em->getRepository('AppBundle:Lead')->findAll();

        return $this->render('lead/index.html.twig', array(
            'leads' => $leads,
        ));*/
    }

    /**
     * Creates a new Lead entity.
     */
    public function newAction(Request $request)
    {
        $lead = new Lead();
        $form = $this->createForm('AdminBundle\Form\LeadType', $lead);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($lead);
            $em->flush();

            return $this->redirectToRoute('lead_show', array('id' => $lead->getId()));
        }

        return $this->render(
            'lead/new.html.twig', array(
            'lead' => $lead,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Lead entity.
     */
    public function showAction(Lead $lead)
    {
        $deleteForm = $this->createDeleteForm($lead);

        return $this->render(
            'lead/show.html.twig', array(
            'lead' => $lead,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Lead entity.
     */
    public function editAction(Request $request, Lead $lead)
    {
        $deleteForm = $this->createDeleteForm($lead);
        $editForm = $this->createForm('AdminBundle\Form\LeadType', $lead);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($lead);
            $em->flush();

            return $this->redirectToRoute('lead_edit', array('id' => $lead->getId()));
        }

        return $this->render(
            'lead/edit.html.twig', array(
            'lead' => $lead,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Lead entity.
     */
    public function deleteAction(Request $request, Lead $lead)
    {
        $form = $this->createDeleteForm($lead);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($lead);
            $em->flush();
        }

        return $this->redirectToRoute('lead_index');
    }

    /**
     * Creates a form to delete a Lead entity.
     *
     * @param Lead $lead The Lead entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Lead $lead)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lead_delete', array('id' => $lead->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
