<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Stage;
use AppBundle\Form\StageType;

/**
 * Stage controller.
 */
class StageController extends Controller
{
    /**
     * Lists all Stage entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $stages = $em->getRepository('AppBundle:Stage')->findAll();

        return $this->render(
            'stage/index.html.twig', array(
            'stages' => $stages,
            )
        );
    }

    /**
     * Creates a new Stage entity.
     */
    public function newAction(Request $request)
    {
        $stage = new Stage();
        $form = $this->createForm('AdminBundle\Form\StageType', $stage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stage);
            $em->flush();

            return $this->redirectToRoute('stage_show', array('id' => $stage->getId()));
        }

        return $this->render(
            'stage/new.html.twig', array(
            'stage' => $stage,
            'form' => $form->createView(),
            )
        );
    }
    
    /**
     * 
     *
     */
    public function dealsAction(Request $request, Stage $stage)
    {
        if($request->isMethod('POST')) {
            return new JsonResponse(json_decode('[{"success":[{"code":0,"message":"success"}]}'));
        }
        return new JsonResponse([]);
    }

    /**
     * Finds and displays a Stage entity.
     */
    public function showAction(Request $request, Stage $stage)
    {
        if($request->isMethod('PUT')) {
            return new JsonResponse(json_decode('[{"success":[{"code":0,"message":"success"}]}'));
        }
        return new JsonResponse([]);
    }

    /**
     * Displays a form to edit an existing Stage entity.
     */
    public function editAction(Request $request, Stage $stage)
    {
        $deleteForm = $this->createDeleteForm($stage);
        $editForm = $this->createForm('AdminBundle\Form\StageType', $stage);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stage);
            $em->flush();

            return $this->redirectToRoute('stage_edit', array('id' => $stage->getId()));
        }

        return $this->render(
            'stage/edit.html.twig', array(
            'stage' => $stage,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Stage entity.
     */
    public function deleteAction(Request $request, Stage $stage)
    {
        $form = $this->createDeleteForm($stage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($stage);
            $em->flush();
        }

        return $this->redirectToRoute('stage_index');
    }

    /**
     * Creates a form to delete a Stage entity.
     *
     * @param Stage $stage The Stage entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Stage $stage)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('stage_delete', array('id' => $stage->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
