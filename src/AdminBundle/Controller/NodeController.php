<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Node;
use AppBundle\Form\NodeType;

/**
 * Node controller.
 */
class NodeController extends Controller
{
    /**
     * Lists all Node entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $nodes = $em->getRepository('AppBundle:Node')->findAll();

        return $this->render(
            'node/index.html.twig', array(
            'nodes' => $nodes,
            )
        );
    }

    /**
     * Creates a new Node entity.
     */
    public function newAction(Request $request)
    {
        $node = new Node();
        $form = $this->createForm('AdminBundle\Form\NodeType', $node);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($node);
            $em->flush();

            return $this->redirectToRoute('node_show', array('id' => $node->getId()));
        }

        return $this->render(
            'node/new.html.twig', array(
            'node' => $node,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Node entity.
     */
    public function showAction(Node $node)
    {
        $deleteForm = $this->createDeleteForm($node);

        return $this->render(
            'node/show.html.twig', array(
            'node' => $node,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Node entity.
     */
    public function editAction(Request $request, Node $node)
    {
        $deleteForm = $this->createDeleteForm($node);
        $editForm = $this->createForm('AdminBundle\Form\NodeType', $node);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($node);
            $em->flush();

            return $this->redirectToRoute('node_edit', array('id' => $node->getId()));
        }

        return $this->render(
            'node/edit.html.twig', array(
            'node' => $node,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Node entity.
     */
    public function deleteAction(Request $request, Node $node)
    {
        $form = $this->createDeleteForm($node);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($node);
            $em->flush();
        }

        return $this->redirectToRoute('node_index');
    }

    /**
     * Creates a form to delete a Node entity.
     *
     * @param Node $node The Node entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Node $node)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('node_delete', array('id' => $node->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
