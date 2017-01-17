<?php

namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Subscription;
use AppBundle\Form\SubscriptionType;

/**
 * Subscription controller.
 */
class SubscriptionController extends Controller
{
    /**
     * Lists all Subscription entities.
     */
    public function indexAction()
    {
        return new JsonResponse(json_decode('[{"id":"1","class":"free","name":"Free","users":"1","deals":"0","plans":[{"id":"1","class":"free","subscription_id":"1","period":"-1","price":"0"}]},{"id":"2","class":"pro","name":"Pro","users":"1","deals":"75","plans":[{"id":"2","class":"pro_annual","subscription_id":"2","period":"12","price":"1188"},{"id":"3","class":"pro_monthly","subscription_id":"2","period":"1","price":"129"}]},{"id":"3","class":"business","name":"Business","users":"4","deals":"250","plans":[{"id":"4","class":"business_annual","subscription_id":"3","period":"12","price":"3588"},{"id":"5","class":"business_monthly","subscription_id":"3","period":"1","price":"379"}]},{"id":"4","class":"enterprise","name":"Enterprise","users":"10","deals":"550","plans":[{"id":"6","class":"enterprise_annual","subscription_id":"4","period":"12","price":"7188"},{"id":"7","class":"enterprise_monthly","subscription_id":"4","period":"1","price":"699"}]}]'));
    }

    /**
     * Creates a new Subscription entity.
     */
    public function newAction(Request $request)
    {
        $subscription = new Subscription();
        $form = $this->createForm('AppBundle\Form\SubscriptionType', $subscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($subscription);
            $em->flush();

            return $this->redirectToRoute('subscription_show', array('id' => $subscription->getId()));
        }

        return $this->render(
            'subscription/new.html.twig', array(
            'subscription' => $subscription,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Subscription entity.
     */
    public function showAction(Subscription $subscription)
    {
        $deleteForm = $this->createDeleteForm($subscription);

        return $this->render(
            'subscription/show.html.twig', array(
            'subscription' => $subscription,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Subscription entity.
     */
    public function editAction(Request $request, Subscription $subscription)
    {
        $deleteForm = $this->createDeleteForm($subscription);
        $editForm = $this->createForm('AppBundle\Form\SubscriptionType', $subscription);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($subscription);
            $em->flush();

            return $this->redirectToRoute('subscription_edit', array('id' => $subscription->getId()));
        }

        return $this->render(
            'subscription/edit.html.twig', array(
            'subscription' => $subscription,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Subscription entity.
     */
    public function deleteAction(Request $request, Subscription $subscription)
    {
        $form = $this->createDeleteForm($subscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($subscription);
            $em->flush();
        }

        return $this->redirectToRoute('subscription_index');
    }

    /**
     * Creates a form to delete a Subscription entity.
     *
     * @param Subscription $subscription The Subscription entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Subscription $subscription)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('subscription_delete', array('id' => $subscription->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
