<?php

namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Note;
use AppBundle\Form\NoteType;

/**
 * Note controller.
 */
class NoteController extends Controller
{

    /**
     * Finds and displays a Note entity.
     */
    public function showAction(Request $request, Note $note)
    {
        $this->denyAccessUnlessGranted('view', $note);
        $em = $this->getDoctrine()->getManager();
        if ($request->isMethod('PUT')) {
            $data = $request->request;
            $update = false;
            foreach ($data as $k => $v) {
                switch ($k) {    
                case 'description':
                    $note->setDescription($v);
                    $update = true;
                    break;                      
                default:
                    break;
                }
            }
            if ($update) {
                $note->setUpdatedAt(time());
                $em->persist($note);
                $em->flush();
            }
        }
        return new JsonResponse($note->toArray());
    }

 

}
