<?php

namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\ModuleProcess;
use AppBundle\Form\ModuleProcessType;

/**
 * ModuleProcess controller.
 */
class ModuleProcessController extends Controller
{
    /**
     * Lists all ModuleProcess entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $moduleProcesses = $em->getRepository('AppBundle:ModuleProcess')->findAll();

        return $this->render(
            'moduleprocess/index.html.twig', array(
            'moduleProcesses' => $moduleProcesses,
            )
        );
    }

    /**
     * Creates a new ModuleProcess entity.
     */
    public function newAction(Request $request)
    {
        $moduleProcess = new ModuleProcess();
        $form = $this->createForm('AppBundle\Form\ModuleProcessType', $moduleProcess);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($moduleProcess);
            $em->flush();

            return $this->redirectToRoute('moduleprocess_show', array('id' => $moduleProcess->getId()));
        }

        return $this->render(
            'moduleprocess/new.html.twig', array(
            'moduleProcess' => $moduleProcess,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a ModuleProcess entity.
     */
    public function showAction(ModuleProcess $moduleProcess)
    {
        $deleteForm = $this->createDeleteForm($moduleProcess);

        return $this->render(
            'moduleprocess/show.html.twig', array(
            'moduleProcess' => $moduleProcess,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing ModuleProcess entity.
     */
    public function editAction(Request $request, ModuleProcess $moduleProcess)
    {
        $deleteForm = $this->createDeleteForm($moduleProcess);
        $editForm = $this->createForm('AppBundle\Form\ModuleProcessType', $moduleProcess);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($moduleProcess);
            $em->flush();

            return $this->redirectToRoute('moduleprocess_edit', array('id' => $moduleProcess->getId()));
        }

        return $this->render(
            'moduleprocess/edit.html.twig', array(
            'moduleProcess' => $moduleProcess,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a ModuleProcess entity.
     */
    public function deleteAction(Request $request, ModuleProcess $moduleProcess)
    {
        $form = $this->createDeleteForm($moduleProcess);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($moduleProcess);
            $em->flush();
        }

        return $this->redirectToRoute('moduleprocess_index');
    }

    /**
     * Creates a form to delete a ModuleProcess entity.
     *
     * @param ModuleProcess $moduleProcess The ModuleProcess entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ModuleProcess $moduleProcess)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('moduleprocess_delete', array('id' => $moduleProcess->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
