<?php
namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;

/**
 * User controller.
 */
class UserController extends Controller
{

    /**
     * meAction
     * Insert description here
     *
     * @param Request
     * @param $request
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function meAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->isMethod('PUT')) {
            //TODO: Use proper validation form
            $data = $request->request;
            $user = $this->getUser();
            $update = false;
            foreach ($data as $k => $v) {
                switch ($k) {
                    case 'first_name':
                        $user->setFirstName($v);
                        $update = true;
                        break;
                    case 'last_name':
                        $user->setLastName($v);
                        $update = true;
                        break;
                    case 'company_name':
                        $user->setCompanyName($v);
                        $update = true;
                        break;
                    case 'company_size':
                        $user->setCompanySize($v);
                        $update = true;
                        break;
                    case 'email':
                        //TODO: Validate email if present
                        $user->setEmail($v);
                        $update = true;
                        break; 
                    case 'password':
                        //TODO: Validate password if present
                        $encoder = $this->get('security.password_encoder');
                        $encoded = $encoder->encodePassword($user, $v);
                        $user->setPassword($encoded);
                        $update = true;
                        break;                     
                    case 'phone':
                        $user->setPhone($v);
                        $update = true;
                        break;                    
                    case 'wizard':
                    case 'user_wizard':
                        $user->setWizard($v);
                        $update = true;
                        break;
                    case 'location':
                        $user->setLocation($v);
                        $update = true;
                        break;
                    case 'coords':
                        $user->setCoords($v);
                        $update = true;
                        break;
                    default:
                        break;
                }
            }
            if ($update) {
                $user->setUpdatedAt(time());
                $em->persist($user);
                $em->flush();
            }
            return new JsonResponse(["success" => ["code" => 0, "message" => "success"]]);

            //TODO: Complete user update 
            // Perform validation
            /* if (isset($email)) {
              $email = validate_email($email);
              }
              if (isset($password)) {
              $password_info = FALSE;
              $password = process_password($password, $password_info);
              }
              // Check email existance
              if (isset($email)) {
              $email_availability = $this->User_model->check_email_availability($_SESSION['user_id'], $email);
              }
             * 
              // if ($user['industries'] !== '[]')
              // {
              // 	// Don't allow to set industries twice
              // }
              // else
              // {
              if ($industries) {
              if ($workspace) {
              if (isset($location))
              $workflow_location = $location;
              else
              $workflow_location = $user['location'];

              if (isset($coords))
              $workflow_coords = $coords;
              else
              $workflow_coords = $user['coords'];

              $this->Industry_model->create_initial_workflows($_SESSION['user_id'], $workspace['id'], array($industries[0]), $workflow_location, $workflow_coords);
              }
              $row['industries'] = json_encode(array($industries[0]));
              }
              // }
              }

              if (isset($wizard) && $wizard === '-1') {
              $this->load->model(['Action_model', 'Action_type_model', 'Workspace_member_model']);
              // $user = $this->User_model->get_user($_SESSION['user_id']);
              // if ($user['wizard'] !== '-1')
              // {
              if (isset($twitter_viral) && $twitter_viral) {
              $this->load->model('Integration_model');
              $twitter_integration = $this->Integration_model->get_integration_by_type($_SESSION['user_id'], Integration_model::TYPE_BY_CLASS['twitter']['id']);
              if ($twitter_integration) {
              $twitter_values = json_decode($twitter_integration['values'], TRUE);
              $this->load->library('twitter', array('initial_auth' => FALSE));
              $authed = $this->twitter->auth($twitter_values['access_token'], $twitter_values['access_token_secret']);
              if (!$authed) {
              $this->app_core->log(App_core::CODE_MAIL_ERROR, 'twitter auth failed');
              } else {
              $tweet_sent = $this->twitter->create('Hey guys, found this great tool to get great leads. Check out @getcliently https://cliently.com');
              ;
              if ($tweet_sent) {
              $this->Workspace_member_model->influence_on_credits(5, $workspace['id'], $_SESSION['user_id']);
              }
              }
              }
              }
              $this->load->library('Lead_scanner');
              $this->lead_scanner->hard_limit = 25;
              $total_deal_count = $this->lead_scanner->do_scan(FALSE, $_SESSION['user_id']);
             * 
             * 
             * 
             *              */
        }
        //Existing user
        $user = $this->getUser()->toArray();
        $accounts = $em->getRepository('AppBundle:Account')->findBy(['ownerId' => $user['id']]);
        //$user['accounts'] = json_decode('[{"id":1,"name":"","type":"main","plan_id":1,"plan_class":"free","next_plan_id":1,"next_plan_class":"free","member_count":1,"credit_balance":0,"accepted_deal_count":0,"workspace_count":0,"pipeline_count":0,"workflow_count":2,"source_count":1,"enabled_member_count":1,"enabled_workspace_count":0,"enabled_pipeline_count":0,"enabled_workflow_count":1,"enabled_source_count":0,"daily_leads_scanned":0,"plan_started_at":1483748729,"is_enabled":true,"created_at":1483748729,"updated_at":0,"membership":{"role":"owner","is_enabled":true}}]');
        $user['accounts'] = [];
        foreach ($accounts as $a) {
            $user['accounts'][] = $a->toArray();
        }
        $user['workspaces'] = $this->getUser()->getWorkspacesArray();
        //$user['integrations'] = [];
        return new JsonResponse($user);
    }

    /**
     * emailValidateAction
     * Insert description here
     *
     * @param Request
     * @param $request
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function emailValidateAction(Request $request)
    {
        return new JsonResponse(['success' => true]);
    }
}
