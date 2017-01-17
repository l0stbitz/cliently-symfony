<?php
namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Pipeline;
use AppBundle\Form\PipelineType;

/**
 * Pipeline controller.
 */
class PipelineController extends Controller
{

    /**
     * Lists all Pipeline entities.
     */
    public function indexAction()
    {
        return new JsonResponse(json_decode('[{"id":1,"name":"New Lead","position":1,"value":0,"pipeline_id":1},{"id":2,"name":"Qualifying","position":2,"value":0,"pipeline_id":1},{"id":3,"name":"Validation","position":3,"value":0,"pipeline_id":1},{"id":4,"name":"Negotiation","position":4,"value":0,"pipeline_id":1},{"id":5,"name":"Closed Won","position":5,"value":0,"pipeline_id":1}]'));
        /* $em = $this->getDoctrine()->getManager();

          $pipelines = $em->getRepository('AppBundle:Pipeline')->findAll();

          return $this->render('pipeline/index.html.twig', array(
          'pipelines' => $pipelines,
          )); */
    }

    /**
     * 
     *
     */
    public function stagesAction()
    {
        return new JsonResponse(json_decode('[{"id":1,"name":"Pipeline 1","position":1,"is_enabled":true,"created_at":1483748729,"updated_at":0,"stages":[{"id":1,"name":"New Lead","position":1,"value":0,"pipeline_id":1},{"id":2,"name":"Qualifying","position":2,"value":0,"pipeline_id":1},{"id":3,"name":"Validation","position":3,"value":0,"pipeline_id":1},{"id":4,"name":"Negotiation","position":4,"value":0,"pipeline_id":1},{"id":5,"name":"Closed Won","position":5,"value":0,"pipeline_id":1}]}]'));
    }

    /**
     * 
     *
     */
    public function dealsAction()
    {
        return new JsonResponse([]);
    }

    /**
     * 
     *
     */
    public function searchAction()
    {
        return new JsonResponse([]);
    }

    /**
     * Creates a new Pipeline entity.
     */
    public function newAction(Request $request)
    {
        $pipeline = new Pipeline();
        $form = $this->createForm('AdminBundle\Form\PipelineType', $pipeline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pipeline);
            $em->flush();

            return $this->redirectToRoute('pipeline_show', array('id' => $pipeline->getId()));
        }

        return $this->render(
            'pipeline/new.html.twig', array(
                'pipeline' => $pipeline,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Pipeline entity.
     */
    public function showAction(Pipeline $pipeline)
    {
        $deleteForm = $this->createDeleteForm($pipeline);

        return $this->render(
            'pipeline/show.html.twig', array(
                'pipeline' => $pipeline,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Pipeline entity.
     */
    public function editAction(Request $request, Pipeline $pipeline)
    {
        $deleteForm = $this->createDeleteForm($pipeline);
        $editForm = $this->createForm('AdminBundle\Form\PipelineType', $pipeline);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pipeline);
            $em->flush();

            return $this->redirectToRoute('pipeline_edit', array('id' => $pipeline->getId()));
        }

        return $this->render(
            'pipeline/edit.html.twig', array(
                'pipeline' => $pipeline,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Pipeline entity.
     */
    public function deleteAction(Request $request, Pipeline $pipeline)
    {
        $form = $this->createDeleteForm($pipeline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($pipeline);
            $em->flush();
        }

        return $this->redirectToRoute('pipeline_index');
    }

    /**
     * Creates a form to delete a Pipeline entity.
     *
     * @param Pipeline $pipeline The Pipeline entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Pipeline $pipeline)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pipeline_delete', array('id' => $pipeline->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
