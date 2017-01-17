<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\DealAction;
use AppBundle\Form\DealActionType;

/**
 * DealAction controller.
 */
class DealActionController extends Controller
{
    /**
     * Lists all DealAction entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dealActions = $em->getRepository('AppBundle:DealAction')->findAll();

        return $this->render(
            'dealaction/index.html.twig', array(
            'dealActions' => $dealActions,
            )
        );
    }

    /**
     * Creates a new DealAction entity.
     */
    public function newAction(Request $request)
    {
        $dealAction = new DealAction();
        $form = $this->createForm('AppBundle\Form\DealActionType', $dealAction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($dealAction);
            $em->flush();

            return $this->redirectToRoute('dealaction_show', array('id' => $dealAction->getId()));
        }

        return $this->render(
            'dealaction/new.html.twig', array(
            'dealAction' => $dealAction,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a DealAction entity.
     */
    public function showAction(DealAction $dealAction)
    {
        $deleteForm = $this->createDeleteForm($dealAction);

        return $this->render(
            'dealaction/show.html.twig', array(
            'dealAction' => $dealAction,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing DealAction entity.
     */
    public function editAction(Request $request, DealAction $dealAction)
    {
        $deleteForm = $this->createDeleteForm($dealAction);
        $editForm = $this->createForm('AppBundle\Form\DealActionType', $dealAction);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($dealAction);
            $em->flush();

            return $this->redirectToRoute('dealaction_edit', array('id' => $dealAction->getId()));
        }

        return $this->render(
            'dealaction/edit.html.twig', array(
            'dealAction' => $dealAction,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a DealAction entity.
     */
    public function deleteAction(Request $request, DealAction $dealAction)
    {
        $form = $this->createDeleteForm($dealAction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($dealAction);
            $em->flush();
        }

        return $this->redirectToRoute('dealaction_index');
    }

    /**
     * Creates a form to delete a DealAction entity.
     *
     * @param DealAction $dealAction The DealAction entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DealAction $dealAction)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('dealaction_delete', array('id' => $dealAction->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
