<?php

namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Token;
use AppBundle\Form\TokenType;

/**
 * Token controller.
 */
class TokenController extends Controller
{
    /**
     * Lists all Token entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tokens = $em->getRepository('AppBundle:Token')->findAll();

        return $this->render(
            'token/index.html.twig', array(
            'tokens' => $tokens,
            )
        );
    }

    /**
     * Creates a new Token entity.
     */
    public function newAction(Request $request)
    {
        $token = new Token();
        $form = $this->createForm('AppBundle\Form\TokenType', $token);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($token);
            $em->flush();

            return $this->redirectToRoute('token_show', array('id' => $token->getId()));
        }

        return $this->render(
            'token/new.html.twig', array(
            'token' => $token,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Token entity.
     */
    public function showAction(Token $token)
    {
        $deleteForm = $this->createDeleteForm($token);

        return $this->render(
            'token/show.html.twig', array(
            'token' => $token,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Token entity.
     */
    public function editAction(Request $request, Token $token)
    {
        $deleteForm = $this->createDeleteForm($token);
        $editForm = $this->createForm('AppBundle\Form\TokenType', $token);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($token);
            $em->flush();

            return $this->redirectToRoute('token_edit', array('id' => $token->getId()));
        }

        return $this->render(
            'token/edit.html.twig', array(
            'token' => $token,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Token entity.
     */
    public function deleteAction(Request $request, Token $token)
    {
        $form = $this->createDeleteForm($token);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($token);
            $em->flush();
        }

        return $this->redirectToRoute('token_index');
    }

    /**
     * Creates a form to delete a Token entity.
     *
     * @param Token $token The Token entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Token $token)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('token_delete', array('id' => $token->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
