<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\ClientlyKeywords;
use AppBundle\Form\ClientlyKeywordsType;

/**
 * ClientlyKeywords controller.
 */
class ClientlyKeywordsController extends Controller
{
    /**
     * Lists all ClientlyKeywords entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clientlyKeywords = $em->getRepository('AppBundle:ClientlyKeywords')->findAll();

        return $this->render(
            'clientlykeywords/index.html.twig', array(
            'clientlyKeywords' => $clientlyKeywords,
            )
        );
    }

    /**
     * Creates a new ClientlyKeywords entity.
     */
    public function newAction(Request $request)
    {
        $clientlyKeyword = new ClientlyKeywords();
        $form = $this->createForm('AdminBundle\Form\ClientlyKeywordsType', $clientlyKeyword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlyKeyword);
            $em->flush();

            return $this->redirectToRoute('clientlykeywords_show', array('id' => $clientlyKeyword->getId()));
        }

        return $this->render(
            'clientlykeywords/new.html.twig', array(
            'clientlyKeyword' => $clientlyKeyword,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a ClientlyKeywords entity.
     */
    public function showAction(ClientlyKeywords $clientlyKeyword)
    {
        $deleteForm = $this->createDeleteForm($clientlyKeyword);

        return $this->render(
            'clientlykeywords/show.html.twig', array(
            'clientlyKeyword' => $clientlyKeyword,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing ClientlyKeywords entity.
     */
    public function editAction(Request $request, ClientlyKeywords $clientlyKeyword)
    {
        $deleteForm = $this->createDeleteForm($clientlyKeyword);
        $editForm = $this->createForm('AdminBundle\Form\ClientlyKeywordsType', $clientlyKeyword);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlyKeyword);
            $em->flush();

            return $this->redirectToRoute('clientlykeywords_edit', array('id' => $clientlyKeyword->getId()));
        }

        return $this->render(
            'clientlykeywords/edit.html.twig', array(
            'clientlyKeyword' => $clientlyKeyword,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a ClientlyKeywords entity.
     */
    public function deleteAction(Request $request, ClientlyKeywords $clientlyKeyword)
    {
        $form = $this->createDeleteForm($clientlyKeyword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($clientlyKeyword);
            $em->flush();
        }

        return $this->redirectToRoute('clientlykeywords_index');
    }

    /**
     * Creates a form to delete a ClientlyKeywords entity.
     *
     * @param ClientlyKeywords $clientlyKeyword The ClientlyKeywords entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ClientlyKeywords $clientlyKeyword)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('clientlykeywords_delete', array('id' => $clientlyKeyword->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
