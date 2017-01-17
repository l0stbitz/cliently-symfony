<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Industry;
use AppBundle\Form\IndustryType;

/**
 * Industry controller.
 */
class IndustryController extends Controller
{
    /**
     * Lists all Industry entities.
     */
    public function indexAction()
    {
        return new JsonResponse(json_decode('[{"id":"1","name":"Marketing","parent_id":"0"},{"id":"2","name":"Photography","parent_id":"0"},{"id":"3","name":"Music Production","parent_id":"0"},{"id":"4","name":"Accounting","parent_id":"0"},{"id":"5","name":"Web Development","parent_id":"0"},{"id":"6","name":"Graphic Design","parent_id":"0"},{"id":"7","name":"Other","parent_id":"0"}]'));
        /*$em = $this->getDoctrine()->getManager();

        $industries = $em->getRepository('AppBundle:Industry')->findAll();

        return $this->render('industry/index.html.twig', array(
            'industries' => $industries,
        ));*/
    }

    /**
     * Creates a new Industry entity.
     */
    public function newAction(Request $request)
    {
        $industry = new Industry();
        $form = $this->createForm('AdminBundle\Form\IndustryType', $industry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($industry);
            $em->flush();

            return $this->redirectToRoute('industry_show', array('id' => $industry->getId()));
        }

        return $this->render(
            'industry/new.html.twig', array(
            'industry' => $industry,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Industry entity.
     */
    public function showAction(Industry $industry)
    {
        $deleteForm = $this->createDeleteForm($industry);

        return $this->render(
            'industry/show.html.twig', array(
            'industry' => $industry,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Industry entity.
     */
    public function editAction(Request $request, Industry $industry)
    {
        $deleteForm = $this->createDeleteForm($industry);
        $editForm = $this->createForm('AdminBundle\Form\IndustryType', $industry);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($industry);
            $em->flush();

            return $this->redirectToRoute('industry_edit', array('id' => $industry->getId()));
        }

        return $this->render(
            'industry/edit.html.twig', array(
            'industry' => $industry,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Industry entity.
     */
    public function deleteAction(Request $request, Industry $industry)
    {
        $form = $this->createDeleteForm($industry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($industry);
            $em->flush();
        }

        return $this->redirectToRoute('industry_index');
    }

    /**
     * Creates a form to delete a Industry entity.
     *
     * @param Industry $industry The Industry entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Industry $industry)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('industry_delete', array('id' => $industry->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
