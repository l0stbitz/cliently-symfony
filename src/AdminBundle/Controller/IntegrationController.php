<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Integration;
use AppBundle\Form\IntegrationType;

/**
 * Integration controller.
 */
class IntegrationController extends Controller
{
    
    /**
     * googleAction
     * Insert description here
     *
     * @param Request
     * @param $request
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function googleAction(Request $request)
    {
        return new JsonResponse(json_decode('{"errors":[{"code":1,"message":"failure"}]}'));
    }
    
    /**
     * twitterAction
     * Insert description here
     *
     * @param Request
     * @param $request
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function twitterAction(Request $request)
    {
        return new JsonResponse(json_decode('{"errors":[{"code":1,"message":"failure"}]}'));
    }    
    
    /**
     * Lists all Integration entities.
     */
    public function indexAction()
    {
        return new JsonResponse([]);
    }

    /**
     * Creates a new Integration entity.
     */
    public function newAction(Request $request)
    {
        $integration = new Integration();
        $form = $this->createForm('AdminBundle\Form\IntegrationType', $integration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($integration);
            $em->flush();

            return $this->redirectToRoute('integration_show', array('id' => $integration->getId()));
        }

        return $this->render(
            'integration/new.html.twig', array(
            'integration' => $integration,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Integration entity.
     */
    public function showAction(Integration $integration)
    {
        $deleteForm = $this->createDeleteForm($integration);

        return $this->render(
            'integration/show.html.twig', array(
            'integration' => $integration,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Integration entity.
     */
    public function editAction(Request $request, Integration $integration)
    {
        $deleteForm = $this->createDeleteForm($integration);
        $editForm = $this->createForm('AdminBundle\Form\IntegrationType', $integration);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($integration);
            $em->flush();

            return $this->redirectToRoute('integration_edit', array('id' => $integration->getId()));
        }

        return $this->render(
            'integration/edit.html.twig', array(
            'integration' => $integration,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Integration entity.
     */
    public function deleteAction(Request $request, Integration $integration)
    {
        $form = $this->createDeleteForm($integration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($integration);
            $em->flush();
        }

        return $this->redirectToRoute('integration_index');
    }

    /**
     * Creates a form to delete a Integration entity.
     *
     * @param Integration $integration The Integration entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Integration $integration)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('integration_delete', array('id' => $integration->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
