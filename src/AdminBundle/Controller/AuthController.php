<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Auth;
use AppBundle\Form\AuthType;

/**
 * Auth controller.
 */
class AuthController extends Controller
{
    /**
     * Lists all Auth entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $auths = $em->getRepository('AppBundle:Auth')->findAll();

        return $this->render(
            'auth/index.html.twig', array(
            'auths' => $auths,
            )
        );
    }

    /**
     * Creates a new Auth entity.
     */
    public function newAction(Request $request)
    {
        $auth = new Auth();
        $form = $this->createForm('AdminBundle\Form\AuthType', $auth);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($auth);
            $em->flush();

            return $this->redirectToRoute('auth_show', array('id' => $auth->getId()));
        }

        return $this->render(
            'auth/new.html.twig', array(
            'auth' => $auth,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Auth entity.
     */
    public function showAction(Auth $auth)
    {
        $deleteForm = $this->createDeleteForm($auth);

        return $this->render(
            'auth/show.html.twig', array(
            'auth' => $auth,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Auth entity.
     */
    public function editAction(Request $request, Auth $auth)
    {
        $deleteForm = $this->createDeleteForm($auth);
        $editForm = $this->createForm('AdminBundle\Form\AuthType', $auth);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($auth);
            $em->flush();

            return $this->redirectToRoute('auth_edit', array('id' => $auth->getId()));
        }

        return $this->render(
            'auth/edit.html.twig', array(
            'auth' => $auth,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Auth entity.
     */
    public function deleteAction(Request $request, Auth $auth)
    {
        $form = $this->createDeleteForm($auth);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($auth);
            $em->flush();
        }

        return $this->redirectToRoute('auth_index');
    }

    /**
     * Creates a form to delete a Auth entity.
     *
     * @param Auth $auth The Auth entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Auth $auth)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('auth_delete', array('id' => $auth->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
