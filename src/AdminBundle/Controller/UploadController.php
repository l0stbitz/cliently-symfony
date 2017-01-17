<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Upload;
use AppBundle\Form\UploadType;

/**
 * Upload controller.
 */
class UploadController extends Controller
{
    /**
     * Lists all Upload entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $uploads = $em->getRepository('AppBundle:Upload')->findAll();

        return $this->render(
            'upload/index.html.twig', array(
            'uploads' => $uploads,
            )
        );
    }

    /**
     * Creates a new Upload entity.
     */
    public function newAction(Request $request)
    {
        $upload = new Upload();
        $form = $this->createForm('AdminBundle\Form\UploadType', $upload);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($upload);
            $em->flush();

            return $this->redirectToRoute('upload_show', array('id' => $upload->getId()));
        }

        return $this->render(
            'upload/new.html.twig', array(
            'upload' => $upload,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Upload entity.
     */
    public function showAction(Upload $upload)
    {
        $deleteForm = $this->createDeleteForm($upload);

        return $this->render(
            'upload/show.html.twig', array(
            'upload' => $upload,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Upload entity.
     */
    public function editAction(Request $request, Upload $upload)
    {
        $deleteForm = $this->createDeleteForm($upload);
        $editForm = $this->createForm('AdminBundle\Form\UploadType', $upload);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($upload);
            $em->flush();

            return $this->redirectToRoute('upload_edit', array('id' => $upload->getId()));
        }

        return $this->render(
            'upload/edit.html.twig', array(
            'upload' => $upload,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Upload entity.
     */
    public function deleteAction(Request $request, Upload $upload)
    {
        $form = $this->createDeleteForm($upload);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($upload);
            $em->flush();
        }

        return $this->redirectToRoute('upload_index');
    }

    /**
     * Creates a form to delete a Upload entity.
     *
     * @param Upload $upload The Upload entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Upload $upload)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('upload_delete', array('id' => $upload->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
