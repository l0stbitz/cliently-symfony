<?php
namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Workspace;
use AppBundle\Entity\Pipeline;
use AppBundle\Entity\Stage;
use AppBundle\Form\PipelineType;

/**
 * Pipeline controller.
 */
class PipelineController extends Controller
{

    /**
     * Lists all Pipeline entities.
     */
    public function indexAction(Request $request, Workspace $workspace)
    {
        $this->denyAccessUnlessGranted('view', $workspace);
        $pipelines = $workspace->getPipelinesArray();
        if ($request->isMethod('POST')) {
            //TODO: Use proper validation form
            $data = $request->request;
            $em = $this->getDoctrine()->getManager();
            $pipeline = new Pipeline();
            $pipeline->setName($data->get('name'));
            $pipeline->setIsEnabled(1);
            $pipeline->setWorkspace($workspace);
            $pipeline->setOwnerId($this->getUser()->getId());
            $em->persist($pipeline);
            $stage = new Stage();
            $stage->setName('Stage 1');
            $stage->setIsEnabled(1);
            $stage->setPipelineId($pipeline->getId());
            $stage->setPipeline($pipeline);
            $stage->setOwnerId($this->getUser()->getId());
            $em->persist($stage);
            $em->flush();
            $stages = $em->getRepository('AppBundle:Stage')->findBy(['pipelineId' => $pipeline->getId()]);

            $pipeline->setStages($stages);
            //Must need to refresh the whole object?
            $pipelines[] = $pipeline;
            //return new JsonResponse(["success" => ["code" => 0, "message" => "success"]]);
        }
        return new JsonResponse($pipelines);
        //return new JsonResponse(json_decode('[{"id":1,"name":"New Lead","position":1,"value":0,"pipeline_id":1},{"id":2,"name":"Qualifying","position":2,"value":0,"pipeline_id":1},{"id":3,"name":"Validation","position":3,"value":0,"pipeline_id":1},{"id":4,"name":"Negotiation","position":4,"value":0,"pipeline_id":1},{"id":5,"name":"Closed Won","position":5,"value":0,"pipeline_id":1}]'));
    }

    /**
     * 
     *
     */
    public function stagesAction(Request $request, Pipeline $pipeline)
    {
        $this->denyAccessUnlessGranted('view', $pipeline);
        if ($request->isMethod('POST')) {
            //TODO: Use proper validation form
            $data = $request->request;
            $em = $this->getDoctrine()->getManager();
            $max_pos = $em->createQueryBuilder()
                ->select('MAX(e.id)')
                ->from('AppBundle:Stage', 'e')
                ->where('e.pipeline = :pipeline')
                ->setParameter('pipeline', $pipeline)
                ->getQuery()
                ->getSingleScalarResult();
            $stage = new Stage();
            $stage->setName($data->get('name'));
            $stage->setIsEnabled(1);
            $stage->setPipelineId($pipeline->getId());
            $stage->setPipeline($pipeline);
            $stage->setPosition($max_pos + 1);
            $stage->setOwner($this->getUser());
            $em->persist($stage);
            $em->flush();
            $stages = $em->getRepository('AppBundle:Stage')->findBy(['pipelineId' => $pipeline->getId()]);
            $pipeline->setStages($stages);
        }
        //return new JsonResponse(json_decode('[{"id":1,"name":"Pipeline 1","position":1,"is_enabled":true,"created_at":1483748729,"updated_at":0,"stages":[{"id":1,"name":"New Lead","position":1,"value":0,"pipeline_id":1},{"id":2,"name":"Qualifying","position":2,"value":0,"pipeline_id":1},{"id":3,"name":"Validation","position":3,"value":0,"pipeline_id":1},{"id":4,"name":"Negotiation","position":4,"value":0,"pipeline_id":1},{"id":5,"name":"Closed Won","position":5,"value":0,"pipeline_id":1}]}]'));
        return new JsonResponse($pipeline->getStagesArray());
    }

    /**
     * 
     *
     */
    public function dealsAction(Request $request, Pipeline $pipeline)
    {
        $this->denyAccessUnlessGranted('view', $pipeline);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request;
        $deals = $em->createQueryBuilder()
            ->select('d')
            ->from('AppBundle:Deal', 'd')
            ->join('AppBundle:Stage', 's', 'WITH', 's.id = d.stageId')
            ->join('AppBundle:Pipeline', 'p', 'WITH', 'p.id = s.pipelineId')
            //->where('d.ownerId = :ownerId')
            ->where('p.id = :pipelineId')
            ->andWhere('d.isEnabled = 1')
            //->setParameter('ownerId', $data->get('owner_id'))
            ->setParameter('pipelineId', $pipeline->getId())
            ->getQuery()
            ->getResult();
        $arr = [];
        foreach ($deals as $deal) {
            $d = $deal->toArray();
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
            $arr[] = $d;
        }
        return new JsonResponse($arr);
        //print_r($arr);exit;
        //return new JsonResponse(json_decode('[{"id":200,"source_description":"","value":0,"stage_id":122,"action_values":[],"created_at":1484082889,"accessed_at":1484082889,"client_source_type":null,"task_due_at":null,"new_events_count":0,"clients":[{"id":200,"name":"Test 1","occupation":"","email":"test@lostbitz.com","phone":"","is_verified":false,"source":null}],"company":null,"source":null}]'));
    }

    /**
     * 
     *
     */
    public function searchAction()
    {
        return new JsonResponse([]);
    }

    /**
     * Finds and displays a Pipeline entity.
     */
    public function showAction(Request $request, Pipeline $pipeline)
    {
        $this->denyAccessUnlessGranted('view', $pipeline);
        if ($request->isMethod('PUT')) {
            $data = $request->request;
            $update = false;
            foreach ($data as $k => $v) {
                switch ($k) {
                case 'name':
                    $pipeline->setName($v);
                    $update = true;
                    break;
                default:
                    break;
                }
            }
            if ($update) {
                $em = $this->getDoctrine()->getManager();
                $pipeline->setUpdatedAt(time());
                $em->persist($pipeline);
                $em->flush();
            }
        }
        return new JsonResponse($pipeline->toArray());
    }
}
