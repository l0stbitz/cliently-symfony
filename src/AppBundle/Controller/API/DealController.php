<?php
namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Deal;
use AppBundle\Entity\Client;
use AppBundle\Entity\Company;
use AppBundle\Entity\Task;
use AppBundle\Entity\Note;
use AppBundle\Entity\Msg;
use AppBundle\Form\DealType;

/**
 * DealController
 * Insert description here
 *
 * @author Josh Murphy
 */
class DealController extends Controller
{

    /**
     * companiesAction
     * Insert description here
     *
     * @param Request
     * @param $request
     * @param Deal
     * @param $deal
     *
     * @return
     */
    public function companiesAction(Request $request, Deal $deal)
    {
        $this->denyAccessUnlessGranted('view', $deal);
        if ($request->isMethod('POST')) {
            //TODO: Use proper validation form
            $data = $request->request;
            $em = $this->getDoctrine()->getManager();
            /*$max_pos = $em->createQueryBuilder()
                ->select('c.id')
                ->from('AppBundle:Deal', 'd')
                ->join('AppBundle:XrefClientDeal', 'x', 'WITH', 'x.dealId = d.id')
                ->join('AppBundle:Client', 'c', 'WITH', 'c.id = x.clientId')
                //->join('AppBundle:Company', 'cp', 'WITH', 'cp.id = c.companyId')
                ->where('d.id = :deal')
                //->andWhere('x.isMain = 1')
                ->setParameter('deal', $deal)
                ->getQuery()
                ->getSingleScalarResult();*/
            //TODO:How is the relationship managed?
            $company = new Company();
            $company->setName($data->get('name', ''));
            $company->setIsEnabled(1);
            $company->setOwner($this->getUser());
            $company->setWorkspaceId($deal->getStage()->getPipeline()->getWorkspace()->getId());
            $em->persist($company);
            $em->flush();
            //$deal = $em->getRepository('AppBundle:Deal')->find($deal->getId());
            //$deal->setCompanies($companies);
            return new JsonResponse($company->toArray());
        }
        //return new JsonResponse(json_decode('[{"id":1,"name":"Pipeline 1","position":1,"is_enabled":true,"created_at":1483748729,"updated_at":0,"stages":[{"id":1,"name":"New Lead","position":1,"value":0,"pipeline_id":1},{"id":2,"name":"Qualifying","position":2,"value":0,"pipeline_id":1},{"id":3,"name":"Validation","position":3,"value":0,"pipeline_id":1},{"id":4,"name":"Negotiation","position":4,"value":0,"pipeline_id":1},{"id":5,"name":"Closed Won","position":5,"value":0,"pipeline_id":1}]}]'));
        return new JsonResponse($deal->getCompaniesArray());
    }

    /**
     * Finds and displays a Deal entity.
     */
    public function showAction(Request $request, Deal $deal)
    {
        $this->denyAccessUnlessGranted('view', $deal);
        $em = $this->getDoctrine()->getManager();
        if ($request->isMethod('PUT')) {
            $data = $request->request;
            $update = false;
            foreach ($data as $k => $v) {
                switch ($k) {
                    case 'name':
                        $deal->setName($v);
                        $update = true;
                        break;
                    case 'stage_id':
                        $deal->setStageId($v);
                        $update = true;
                        break;
                    case 'source_description':
                        $deal->setSourceDescription($v);
                        $update = true;
                        break;
                    case 'value':
                        $deal->setValue($v);
                        $update = true;
                        break;
                    default:
                        break;
                }
            }
            if ($update) {
                $deal->setUpdatedAt(time());
                $em->persist($deal);
                $em->flush();
            }
        }
        $d = $deal->toArray();
        //TODO: Review
        //for now request clients outside of the entity because of the 
        //future datalayer changes
        $c = [];
        $clients = $em->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:Client', 'c')
            ->join('AppBundle:XrefClientDeal', 'x', 'WITH', 'c.id = x.clientId')
            ->join('AppBundle:Deal', 'd', 'WITH', 'd.id = x.dealId')
            ->where('x.dealId = :dealId')
            ->setParameter('dealId', $d['id'])
            ->getQuery()
            ->getResult();
        foreach ($clients as $client) {
            $c[] = $client->toArray();
        }
        $d['clients'] = $c;
        $c = [];
        $companies = $em->createQueryBuilder()
            ->select('cp.id, cp.name, cp.description')
            ->from('AppBundle:Deal', 'd')
            ->leftJoin('AppBundle:XrefClientDeal', 'x', 'WITH', 'x.dealId = d.id')
            ->leftJoin('AppBundle:Client', 'c', 'WITH', 'c.id = x.clientId')
            ->leftJoin('AppBundle:Company', 'cp', 'WITH', 'cp.id = c.companyId')
            ->where('d.id = :dealId')
            ->setParameter('dealId', $d['id'])
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
        foreach ($companies as $company) {
            if (!is_null($company['id'])) {
                $c[] = $company;
            }
        }
        $d['company'] = $c;
        return new JsonResponse($d);
    }

    /**
     * tasksAction
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
     */
    public function tasksAction(Request $request, Deal $deal, Client $client)
    {
        $this->denyAccessUnlessGranted('view', $deal);
        if ($request->isMethod('POST')) {
            //TODO: Use proper validation form
            $data = $request->request;
            $em = $this->getDoctrine()->getManager();
            /* $max_pos = $em->createQueryBuilder()
              ->select('MAX(t.position)')
              ->from('AppBundle:Task', 't')
              ->where('t.dealId = :deal')
              ->andWhere('t.clientId = :client')
              ->setParameter('client', $client)
              ->setParameter('deal', $deal)
              ->getQuery()
              ->getSingleScalarResult();
              echo $max_pos . ' test '; */
            $task = new Task();
            $task->setName($data->get('name', ''));
            $task->setDescription($data->get('description', ''));
            $task->setType($data->get('type'));
            $task->setDeal($deal);
            $task->setClientId($client->getId());
            $task->setIsCompleted($data->get('is_completed'));
            //$task->setPosition($max_pos + 1); ????
            $task->setDueAt($data->get('due_at'));
            $task->setOwnerId($this->getUser()->getId());
            $em->persist($task);
            $em->flush();
            return new JsonResponse($task->toArray());
        }
        $tasks = [];
        foreach ($deal->getTasksArray() as $t) {
            if ($t['client_id'] == $client->getId()) {
                $tasks[] = $t;
            }
        }
        return new JsonResponse($tasks);
    }

    /**
     * notesAction
     * Insert description here
     *
     * @param Request $request
     * @param Deal    $deal
     * @param Client  $client
     *
     * @return
     */
    public function notesAction(Request $request, Deal $deal, Client $client)
    {
        $this->denyAccessUnlessGranted('view', $deal);
        if ($request->isMethod('POST')) {
            //TODO: Use proper validation form
            $data = $request->request;
            $em = $this->getDoctrine()->getManager();
            $note = new Note();
            $note->setStatus(1);
            $note->setDescription($data->get('description', ''));
            $note->setDeal($deal);
            $note->setClientId($client->getId());
            $note->setOwnerId($this->getUser()->getId());
            $em->persist($note);
            $em->flush();
            return new JsonResponse($note->toArray());
        }
        $notes = [];
        foreach ($deal->getNotesArray() as $n) {
            if ($n['client_id'] == $client->getId()) {
                $notes[] = $n;
            }
        }
        return new JsonResponse($notes);
    }

    /**
     * mailsAction
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
    public function mailsAction(Request $request, Deal $deal, Client $client)
    {
        $this->denyAccessUnlessGranted('view', $deal);
        if ($request->isMethod('POST')) {
            //TODO: Use proper validation form
            $data = $request->request;
            $em = $this->getDoctrine()->getManager();
            $mail = new Msg();
            $mail->setName($data->get('name', ''));
            $mail->setStatus(1);
            //TODO: Review code property, assume uniqe mailid?
            $mail->setCode(md5(uniqid()));
            $mail->setType(Msg::TYPE_EMAIL);
            $mail->setEmail($client->getEmail());
            $mail->setDescription($data->get('description', ''));
            $mail->setCc($data->get('cc', ''));
            $mail->setBcc($data->get('bcc', ''));
            $mail->setDeal($deal);
            $mail->setClientId($client->getId());
            $mail->setOwnerId($this->getUser()->getId());
            $em->persist($mail);
            $em->flush();
            //TODO: Trigger mail ... Should be an async process, queued
            return new JsonResponse($mail->toArray());
        }
        $mails = [];
        foreach ($deal->getMailsArray() as $m) {
            if ($m['client_id'] == $client->getId()) {
                $mails[] = $m;
            }
        }
        return new JsonResponse($mails);
    }
}
