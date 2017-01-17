<?php
namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Workflow;
use AppBundle\Form\WorkflowType;

/**
 * Workflow controller.
 */
class WorkflowController extends Controller
{

    /**
     * Lists all Workflow entities.
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->isMethod('POST')) {
            return new JsonResponse(json_decode('{"errors":[{"code":409,"message":"conflict"}]}'), 409);
        }
        $workflows = $em->getRepository('AppBundle:Workflow')->findBy(['ownerId' => $this->getUser()->getId()]);
        $arr = [];
        foreach ($workflows as $w) {
            $wa = $w->toArray();
            $wa['actions'] = $w->getActionsArray();
            $wa['sources'] = $w->getSourcesArray();
            $arr[] = $wa;
        }

        return new JsonResponse($arr);
        //Existing user
        //return new JsonResponse(json_decode('[{"id":1,"name":"Marketing Flow","stop_on_respond":[],"position":1,"is_enabled":0,"sources":[],"actions":[]},{"id":2,"name":"Flow","stop_on_respond":[],"position":2,"is_enabled":1,"sources":[{"id":4,"position":1,"class":"dbperson_get_clients","module_class":"dbperson","values":{"company_names":["asdfasdfsdf"],"person_title":"asdfasdf","location":"","title_seniorities":["Board Members","C_EXECUTIVES","DIRECTOR"],"revenues":["Sales.under500K","Sales.500Kto1M"],"industries":["266","1290","1802"],"title_roles":["4784231","4456551"],"employee_sizes":["Employees.20to49"],"countries":["Country.Algeria","Country.Agola","Country.Benin","Country.Botswana","Country.BurkinaFaso","Country.Burundi","Country.Cameroon","Country.CapeVerde","Country.CentralAfricanRepublic","Country.Chad","Country.Comoros","Country.Congo","Country.Djibouti","Country.Egypt","Country.EquatorialGuinea","Country.Eritrea","Country.Ethiopia","Country.Gabon","Country.Gambia","Country.Ghana","Country.Guinea","Country.GuineaBissau","Country.Kenya","Country.Lesotho","Country.Liberia","Country.Libya","Country.Madagascar","Country.Malawi","Country.Mali","Country.Mauritania","Country.Mauritius","Country.Morocco","Country.Mozambique","Country.Namibia","Country.Niger","Country.Nigeria","Country.Rwanda","Country.SaoTome","Country.Senegal","Country.Seychelles","Country.SierraLeone","Country.Somalia","Country.SouthAfrica","Country.Sudan","Country.Swaziland","Country.Togo","Country.Tunisia","Country.Uganda","Country.Tanzania","Country.westernSahara","Country.Zambia","Country.Zimbabwe","Country.Afghanistan ","Country.Armenia","Country.Azerbaijan","Country.Bahrain","Country.Bangladesh","Country.Bhutan","Country.Brunei","Country.Burma","Country.Cambodia","Country.China","Country.Cyprus","Country.EastTimor","Country.Georgia","Country.India","Country.Indonesia","Country.Iran","Country.Iraq","Country.Israel","Country.Japan","Country.Jordan","Country.Kazakstan","Country.Korea","Country.Kuwait","Country.Kyrgyzstan","Country.Laos","Country.Lebanon","Country.Macau","Country.Malaysia","Country.Maldives","Country.Mongolia","Country.Myanmar","Country.Nepal","Country.NorthKorea","Country.Oman","Country.Pakistan","Country.Philippines","Country.Qatar","Country.SaudiArabia","Country.Singapore","Country.SouthKorea","Country.SriLanka","Country.Syria","Country.Taiwan","Country.Tajikistan","Country.Thailand","Country.Tibet","Country.Turkey","Country.Turkmenistan","Country.UnitedArabEmirates","Country.Uzbekistan","Country.Vietnam","Country.Yemen","Country.Australia","Country.CookIslands","Country.Fiji","Country.FrenchPolynesia","Country.Guam","Country.Kiribati","Country.NewCaledonia","Country.NewZealand","Country.PapuaNewGuinea","Country.Pitcairn","Country.Samoa","Country.SolomonIslands","Country.Tonga","Country.Vanuatu"],"daily_limit":"100"},"is_enabled":0}],"actions":[]},{"id":3,"name":"Marketing Flow","stop_on_respond":[],"position":3,"is_enabled":0,"sources":[{"id":6,"position":1,"class":"twitter_get_deals","module_class":"twitter","values":{"location":"Edmonton, Alberta, CA","range":"1000","keywords":["\"new website\"","\"website launch\"","\"grand opening\""],"coords":"53.544389,-113.4909267"},"is_enabled":1}],"actions":[{"id":5,"position":1,"class":"twitter_follow","module_class":"twitter","values":{"follow":true},"is_enabled":1},{"id":7,"position":2,"class":"twitter_retweet","module_class":"twitter","values":{"msg":""},"is_enabled":1}]},{"id":4,"name":"Marketing Flow","stop_on_respond":[],"position":4,"is_enabled":0,"sources":[{"id":9,"position":1,"class":"twitter_get_deals","module_class":"twitter","values":{"location":"Edmonton, Alberta, CA","range":"1000","keywords":["\"new website\"","\"website launch\"","\"grand opening\""],"coords":"53.544389,-113.4909267"},"is_enabled":1}],"actions":[{"id":8,"position":1,"class":"twitter_follow","module_class":"twitter","values":{"follow":true},"is_enabled":1},{"id":10,"position":2,"class":"twitter_retweet","module_class":"twitter","values":{"msg":""},"is_enabled":1}]}]'));
        //New User
        //return new JsonResponse(json_decode('[{"id":"1","name":"Marketing Flow","stop_on_respond":[],"position":"1","is_enabled":"0"},{"id":"2","name":"Flow","stop_on_respond":[],"position":"2","is_enabled":"1"}]'));
    }

    /**
     * 
     * @param Workflow $workflow
     * @return array
     */
    private function getActionsForWorkflow(Workflow $workflow)
    {
        $this->denyAccessUnlessGranted('view', $workflow);
        $em = $this->getDoctrine()->getManager();
        $actions = $workflow->getActions();
        $arr = [];
        foreach ($actions as $a) {
            $arr[] = $a->toArray();
        }
        return $arr;
    }
    
    /**
     * 
     * @param Workflow $workflow
     * @return array
     */
    private function getSourcesForWorkflow(Workflow $workflow)
    {
        $this->denyAccessUnlessGranted('view', $workflow);
        $em = $this->getDoctrine()->getManager();
        //$sources = $em->getRepository('AppBundle:Source')->findBy(['workflowId' => $workflow->getId()]);
        $arr = [];
        //foreach ($sources as $s) {
        //    $arr[] = $s->toArray();
        //}
        return $arr;
    }

    /**
     * 
     *
     */
    public function sourceAction(Request $request, $id, $source_id)
    {
        return new JsonResponse([]);
    }

    /**
     * 
     *
     */
    public function actionAction(Request $request, Workflow $workflow)
    {
        $this->denyAccessUnlessGranted('view', $workflow);
        return new JsonResponse([]);
    }
}
