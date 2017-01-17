<?php

namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\ModuleCat;
use AppBundle\Form\ModuleCatType;

/**
 * ModuleCat controller.
 */
class ModuleCatController extends Controller
{
    /**
     * Lists all ModuleCat entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $moduleCats = $em->getRepository('AppBundle:ModuleCat')->findAll();

        return $this->render(
            'modulecat/index.html.twig', array(
            'moduleCats' => $moduleCats,
            )
        );
    }

    /**
     * Creates a new ModuleCat entity.
     */
    public function newAction(Request $request)
    {
        $moduleCat = new ModuleCat();
        $form = $this->createForm('AppBundle\Form\ModuleCatType', $moduleCat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($moduleCat);
            $em->flush();

            return $this->redirectToRoute('modulecat_show', array('id' => $moduleCat->getId()));
        }

        return $this->render(
            'modulecat/new.html.twig', array(
            'moduleCat' => $moduleCat,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a ModuleCat entity.
     */
    public function showAction(ModuleCat $moduleCat)
    {
        $deleteForm = $this->createDeleteForm($moduleCat);

        return $this->render(
            'modulecat/show.html.twig', array(
            'moduleCat' => $moduleCat,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing ModuleCat entity.
     */
    public function editAction(Request $request, ModuleCat $moduleCat)
    {
        $deleteForm = $this->createDeleteForm($moduleCat);
        $editForm = $this->createForm('AppBundle\Form\ModuleCatType', $moduleCat);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($moduleCat);
            $em->flush();

            return $this->redirectToRoute('modulecat_edit', array('id' => $moduleCat->getId()));
        }

        return $this->render(
            'modulecat/edit.html.twig', array(
            'moduleCat' => $moduleCat,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a ModuleCat entity.
     */
    public function deleteAction(Request $request, ModuleCat $moduleCat)
    {
        $form = $this->createDeleteForm($moduleCat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($moduleCat);
            $em->flush();
        }

        return $this->redirectToRoute('modulecat_index');
    }

    /**
     * Creates a form to delete a ModuleCat entity.
     *
     * @param ModuleCat $moduleCat The ModuleCat entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ModuleCat $moduleCat)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('modulecat_delete', array('id' => $moduleCat->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
