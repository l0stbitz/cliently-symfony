<?php
namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Msg;
use AppBundle\Form\MsgType;

/**
 * Msg controller.
 */
class MsgController extends Controller
{

    /**
     * forwardAction
     * Insert description here
     *
     * @param Request
     * @param $request
     * @param Deal
     * @param $deal
     * @param Client
     * @param $client
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function forwardAction(Request $request, Msg $origMsg)
    {
        $this->denyAccessUnlessGranted('view', $origMsg);
        if ($request->isMethod('POST')) {
                       //TODO: Use proper validation form
            $data = $request->request;
            $em = $this->getDoctrine()->getManager();
            $name = $data->get('name', '');
            $description = $data->get('description');
            $email = $data->get('email');
            $cc = $data->get('cc', '');
            $bcc = $data->get('bcc', '');
            $references = trim($origMsg->getReferences() . ' ' . $origMsg->getUid());
            $in_reply = $origMsg->getUid();
            $row = $this->get('app.email_service')->deliverImap($email, $description, $name, $origMsg->getClient()->getName(), $cc, $bcc, $in_reply, $references);
            $mail = new Msg($row);
            $mail->setStatus(1);
            $mail->setDeal($origMsg->getDeal());
            $mail->setClient($origMsg->getClient());
            $mail->setOwnerId($this->getUser()->getId());
            $em->persist($mail);
            $em->flush();
            //TODO: Trigger mail ... Should be an async process, queued
            return new JsonResponse($mail->toArray());
        }
        return new JsonResponse([]);
    }

    /**
     * replyAction
     * Insert description here
     *
     * @param Request $request
     * @param Deal $deal
     * @param Client $client
     *
     * @return
     */
    public function replyAction(Request $request, Msg $origMsg)
    {
        $this->denyAccessUnlessGranted('view', $origMsg);
        if ($request->isMethod('POST')) {
            //TODO: Use proper validation form
            $data = $request->request;
            $em = $this->getDoctrine()->getManager();
            $name = $data->get('name', '');
            $description = $data->get('description');
            $cc = $data->get('cc', '');
            $bcc = $data->get('bcc', '');
            $references = trim($origMsg->getReferences() . ' ' . $origMsg->getUid());
            $in_reply = $origMsg->getUid();
            $row = $this->get('app.email_service')->deliverImap($origMsg->getClient()->getEmail(), 
                $description, $name, $origMsg->getClient()->getName(), $cc, $bcc, $in_reply, $references);
            $mail = new Msg($row);
            $mail->setStatus(1);
            $mail->setDeal($origMsg->getDeal());
            $mail->setClient($origMsg->getClient());
            $mail->setOwnerId($this->getUser()->getId());
            $em->persist($mail);
            $em->flush();
            //TODO: Trigger mail ... Should be an async process, queued
            return new JsonResponse($mail->toArray());
        }
        return new JsonResponse([]);
    }
}
