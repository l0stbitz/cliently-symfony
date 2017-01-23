<?php
namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Client;
use AppBundle\Form\ClientType;

/**
 * Client controller.
 */
class ClientController extends Controller
{

    /**
     * Finds and displays a Client entity.
     */
    public function showAction(Request $request, Client $client)
    {
        $this->denyAccessUnlessGranted('view', $client);
        $em = $this->getDoctrine()->getManager();
        if ($request->isMethod('PUT')) {
            $data = $request->request;
            $update = false;
            $integration = false;
            foreach ($data as $k => $v) {
                switch ($k) {
                    case 'social':
                        $client->setSocial(json_encode($v));
                        $update = true;
                        $integration = $v;
                        break;
                    case 'email':
                        //TODO: Proper validation of email using forms
                        $client->setEmail($v);
                        $update = true;
                        break;
                    case 'occupation':
                        $client->setOccupation($v);
                        $update = true;
                        break;
                    case 'phone':
                        $client->setPhone($v);
                        $update = true;
                        break;
                    case 'address1':
                        $client->setAddressLine1($v);
                        $update = true;
                        break;
                    case 'address2':
                        $client->setAddressLine2($v);
                        $update = true;
                        break;
                    case 'city':
                        $client->setCity($v);
                        $update = true;
                        break;
                    case 'state':
                        $client->setState($v);
                        $update = true;
                        break;
                    case 'zip':
                        $client->setZip($v);
                        $update = true;
                        break;
                    case 'country':
                        $client->setCountry($v);
                        $update = true;
                        break;
                    default:
                        break;
                }
            }
            if ($update) {
                $client->setUpdatedAt(time());
                $em->persist($client);
                $em->flush();
            }
            if($integration){
                $this->get('app.integration_service')->updateClientIntegrations($client, $integration);
            }
        }
        return new JsonResponse($client->toArray());
    }

    /**
     * Finds and displays a Workspace entity.
     */
    public function twitterMessagesAction(Request $request, Client $client)
    {
        $this->denyAccessUnlessGranted('view', $client);
        if ($request->isMethod('POST')) {
            $data = $request->request;
            $type = $data->get('type');
            $description = $data->get('description');
            $source_id = $data->get('source_id');
            $twitterService = $this->get('app.twitter_service');
            $twitterService->handleAction($this->getUser(), $client, $type, $description, $source_id);
        }
        return new JsonResponse(['id'=>1]);
    }
}
