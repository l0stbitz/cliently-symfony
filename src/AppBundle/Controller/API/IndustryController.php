<?php

namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Industry;
use AppBundle\Form\IndustryType;

/**
 * Industry controller.
 */
class IndustryController extends Controller
{
    /**
     * Lists all Industry entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $industries = $em->getRepository('AppBundle:Industry')->findAll();
        $arr = [];
        foreach($industries as $i){
            $arr[] = $i->toArray();
        }
        
        return new JsonResponse($arr);
    }
}
