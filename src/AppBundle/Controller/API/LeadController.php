<?php

namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Lead;
use AppBundle\Form\LeadType;

/**
 * Lead controller.
 */
class LeadController extends Controller
{
    /**
     * Lists all Lead entities.
     */
    public function indexAction()
    {
        return new JsonResponse(json_decode('{"count":0,"since":1483925378}'));
    }

}
