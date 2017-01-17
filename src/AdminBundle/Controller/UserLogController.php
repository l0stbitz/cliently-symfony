<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\UserLog;
use AppBundle\Form\UserLogType;

/**
 * UserLog controller.
 */
class UserLogController extends Controller
{
    /**
     * Lists all UserLog entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $userLogs = $em->getRepository('AppBundle:UserLog')->findAll();

        return $this->render(
            'userlog/index.html.twig', array(
            'userLogs' => $userLogs,
            )
        );
    }

    /**
     * Creates a new UserLog entity.
     */
    public function newAction(Request $request)
    {
        $userLog = new UserLog();
        $form = $this->createForm('AdminBundle\Form\UserLogType', $userLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($userLog);
            $em->flush();

            return $this->redirectToRoute('userlog_show', array('id' => $userLog->getId()));
        }

        return $this->render(
            'userlog/new.html.twig', array(
            'userLog' => $userLog,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a UserLog entity.
     */
    public function showAction(UserLog $userLog)
    {
        $deleteForm = $this->createDeleteForm($userLog);

        return $this->render(
            'userlog/show.html.twig', array(
            'userLog' => $userLog,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing UserLog entity.
     */
    public function editAction(Request $request, UserLog $userLog)
    {
        $deleteForm = $this->createDeleteForm($userLog);
        $editForm = $this->createForm('AdminBundle\Form\UserLogType', $userLog);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($userLog);
            $em->flush();

            return $this->redirectToRoute('userlog_edit', array('id' => $userLog->getId()));
        }

        return $this->render(
            'userlog/edit.html.twig', array(
            'userLog' => $userLog,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a UserLog entity.
     */
    public function deleteAction(Request $request, UserLog $userLog)
    {
        $form = $this->createDeleteForm($userLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($userLog);
            $em->flush();
        }

        return $this->redirectToRoute('userlog_index');
    }

    /**
     * Creates a form to delete a UserLog entity.
     *
     * @param UserLog $userLog The UserLog entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(UserLog $userLog)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('userlog_delete', array('id' => $userLog->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
