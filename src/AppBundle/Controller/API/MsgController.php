<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Msg;
use AppBundle\Form\MsgType;

/**
 * Msg controller.
 */
class MsgController extends Controller
{
    /**
     * Lists all Msg entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $msgs = $em->getRepository('AppBundle:Msg')->findAll();

        return $this->render(
            'msg/index.html.twig', array(
            'msgs' => $msgs,
            )
        );
    }

    /**
     * Creates a new Msg entity.
     */
    public function newAction(Request $request)
    {
        $msg = new Msg();
        $form = $this->createForm('AppBundle\Form\MsgType', $msg);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($msg);
            $em->flush();

            return $this->redirectToRoute('msg_show', array('id' => $msg->getId()));
        }

        return $this->render(
            'msg/new.html.twig', array(
            'msg' => $msg,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Msg entity.
     */
    public function showAction(Msg $msg)
    {
        $deleteForm = $this->createDeleteForm($msg);

        return $this->render(
            'msg/show.html.twig', array(
            'msg' => $msg,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Msg entity.
     */
    public function editAction(Request $request, Msg $msg)
    {
        $deleteForm = $this->createDeleteForm($msg);
        $editForm = $this->createForm('AppBundle\Form\MsgType', $msg);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($msg);
            $em->flush();

            return $this->redirectToRoute('msg_edit', array('id' => $msg->getId()));
        }

        return $this->render(
            'msg/edit.html.twig', array(
            'msg' => $msg,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Msg entity.
     */
    public function deleteAction(Request $request, Msg $msg)
    {
        $form = $this->createDeleteForm($msg);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($msg);
            $em->flush();
        }

        return $this->redirectToRoute('msg_index');
    }

    /**
     * Creates a form to delete a Msg entity.
     *
     * @param Msg $msg The Msg entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Msg $msg)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('msg_delete', array('id' => $msg->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
