<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\ActionDefaultOld;
use AppBundle\Form\ActionDefaultOldType;

/**
 * ActionDefaultOld controller.
 */
class ActionDefaultOldController extends Controller
{
    /**
     * Lists all ActionDefaultOld entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $actionDefaultOlds = $em->getRepository('AppBundle:ActionDefaultOld')->findAll();

        return $this->render(
            'actiondefaultold/index.html.twig', array(
            'actionDefaultOlds' => $actionDefaultOlds,
            )
        );
    }

    /**
     * Creates a new ActionDefaultOld entity.
     */
    public function newAction(Request $request)
    {
        $actionDefaultOld = new ActionDefaultOld();
        $form = $this->createForm('AdminBundle\Form\ActionDefaultOldType', $actionDefaultOld);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($actionDefaultOld);
            $em->flush();

            return $this->redirectToRoute('actiondefaultold_show', array('id' => $actionDefaultOld->getId()));
        }

        return $this->render(
            'actiondefaultold/new.html.twig', array(
            'actionDefaultOld' => $actionDefaultOld,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a ActionDefaultOld entity.
     */
    public function showAction(ActionDefaultOld $actionDefaultOld)
    {
        $deleteForm = $this->createDeleteForm($actionDefaultOld);

        return $this->render(
            'actiondefaultold/show.html.twig', array(
            'actionDefaultOld' => $actionDefaultOld,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing ActionDefaultOld entity.
     */
    public function editAction(Request $request, ActionDefaultOld $actionDefaultOld)
    {
        $deleteForm = $this->createDeleteForm($actionDefaultOld);
        $editForm = $this->createForm('AdminBundle\Form\ActionDefaultOldType', $actionDefaultOld);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($actionDefaultOld);
            $em->flush();

            return $this->redirectToRoute('actiondefaultold_edit', array('id' => $actionDefaultOld->getId()));
        }

        return $this->render(
            'actiondefaultold/edit.html.twig', array(
            'actionDefaultOld' => $actionDefaultOld,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a ActionDefaultOld entity.
     */
    public function deleteAction(Request $request, ActionDefaultOld $actionDefaultOld)
    {
        $form = $this->createDeleteForm($actionDefaultOld);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($actionDefaultOld);
            $em->flush();
        }

        return $this->redirectToRoute('actiondefaultold_index');
    }

    /**
     * Creates a form to delete a ActionDefaultOld entity.
     *
     * @param ActionDefaultOld $actionDefaultOld The ActionDefaultOld entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ActionDefaultOld $actionDefaultOld)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('actiondefaultold_delete', array('id' => $actionDefaultOld->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
