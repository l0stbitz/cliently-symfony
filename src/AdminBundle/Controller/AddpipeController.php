<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Addpipe;
use AppBundle\Form\AddpipeType;

/**
 * Addpipe controller.
 */
class AddpipeController extends Controller
{
    /**
     * Lists all Addpipe entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $addpipes = $em->getRepository('AppBundle:Addpipe')->findAll();

        return $this->render(
            'addpipe/index.html.twig', array(
            'addpipes' => $addpipes,
            )
        );
    }

    /**
     * Creates a new Addpipe entity.
     */
    public function newAction(Request $request)
    {
        $addpipe = new Addpipe();
        $form = $this->createForm('AdminBundle\Form\AddpipeType', $addpipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($addpipe);
            $em->flush();

            return $this->redirectToRoute('addpipe_show', array('id' => $addpipe->getId()));
        }

        return $this->render(
            'addpipe/new.html.twig', array(
            'addpipe' => $addpipe,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Addpipe entity.
     */
    public function showAction(Addpipe $addpipe)
    {
        $deleteForm = $this->createDeleteForm($addpipe);

        return $this->render(
            'addpipe/show.html.twig', array(
            'addpipe' => $addpipe,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Addpipe entity.
     */
    public function editAction(Request $request, Addpipe $addpipe)
    {
        $deleteForm = $this->createDeleteForm($addpipe);
        $editForm = $this->createForm('AdminBundle\Form\AddpipeType', $addpipe);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($addpipe);
            $em->flush();

            return $this->redirectToRoute('addpipe_edit', array('id' => $addpipe->getId()));
        }

        return $this->render(
            'addpipe/edit.html.twig', array(
            'addpipe' => $addpipe,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Addpipe entity.
     */
    public function deleteAction(Request $request, Addpipe $addpipe)
    {
        $form = $this->createDeleteForm($addpipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($addpipe);
            $em->flush();
        }

        return $this->redirectToRoute('addpipe_index');
    }

    /**
     * Creates a form to delete a Addpipe entity.
     *
     * @param Addpipe $addpipe The Addpipe entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Addpipe $addpipe)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('addpipe_delete', array('id' => $addpipe->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
