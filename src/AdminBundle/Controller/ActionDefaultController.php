<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\ActionDefault;
use AppBundle\Form\ActionDefaultType;

/**
 * ActionDefault controller.
 */
class ActionDefaultController extends Controller
{
    /**
     * Lists all ActionDefault entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $actionDefaults = $em->getRepository('AppBundle:ActionDefault')->findAll();

        return $this->render(
            'actiondefault/index.html.twig', array(
            'actionDefaults' => $actionDefaults,
            )
        );
    }

    /**
     * Creates a new ActionDefault entity.
     */
    public function newAction(Request $request)
    {
        $actionDefault = new ActionDefault();
        $form = $this->createForm('AdminBundle\Form\ActionDefaultType', $actionDefault);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($actionDefault);
            $em->flush();

            return $this->redirectToRoute('actiondefault_show', array('id' => $actionDefault->getId()));
        }

        return $this->render(
            'actiondefault/new.html.twig', array(
            'actionDefault' => $actionDefault,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a ActionDefault entity.
     */
    public function showAction(ActionDefault $actionDefault)
    {
        $deleteForm = $this->createDeleteForm($actionDefault);

        return $this->render(
            'actiondefault/show.html.twig', array(
            'actionDefault' => $actionDefault,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing ActionDefault entity.
     */
    public function editAction(Request $request, ActionDefault $actionDefault)
    {
        $deleteForm = $this->createDeleteForm($actionDefault);
        $editForm = $this->createForm('AdminBundle\Form\ActionDefaultType', $actionDefault);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($actionDefault);
            $em->flush();

            return $this->redirectToRoute('actiondefault_edit', array('id' => $actionDefault->getId()));
        }

        return $this->render(
            'actiondefault/edit.html.twig', array(
            'actionDefault' => $actionDefault,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a ActionDefault entity.
     */
    public function deleteAction(Request $request, ActionDefault $actionDefault)
    {
        $form = $this->createDeleteForm($actionDefault);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($actionDefault);
            $em->flush();
        }

        return $this->redirectToRoute('actiondefault_index');
    }

    /**
     * Creates a form to delete a ActionDefault entity.
     *
     * @param ActionDefault $actionDefault The ActionDefault entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ActionDefault $actionDefault)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('actiondefault_delete', array('id' => $actionDefault->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
