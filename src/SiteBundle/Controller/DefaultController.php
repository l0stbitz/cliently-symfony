<?php
namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * DefaultController
 * Insert description here
 *
 * @category
 * @package
 * @author
 * @copyright
 * @license
 * @version
 * @link
 * @see
 * @since
 */
class DefaultController extends Controller
{

    /**
     * indexAction
     * Insert description here
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function indexAction()
    {
        return $this->render('SiteBundle:Default:index.html.twig');
    }

    /**
     * app_index
     * Insert description here
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function app_index()
    {
        $this->load->model(['Account_model', 'Workspace_model']);
        $this->load->helper('url');

        if (!isset($_SESSION['user_id'])) {
            redirect(PROTOCOL . '://' . $this->config->item('main_domain'));
        }

        $this->load->library('Usertrack');

        $data = array();

        if (ENVIRONMENT === 'production') {
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : false;
            $data['usertrack'] = $this->usertrack->get_data($user_id);
        }

        $accounts = $this->Account_model->get(false, false, $_SESSION['user_id'], false, false, $_SESSION['user_id']);
        if ($accounts === null) {
            redirect('/message/error');
        } elseif (!$accounts) {
            // redirect('/message/suspended-account-membership');
        } else {
            $data['accounts'] = $accounts;
        }

        $workspaces = $this->Workspace_model->get(false, false, false, $_SESSION['user_id'], $_SESSION['user_id']);
        if ($workspaces === null) {
            redirect('/message/error');
        } elseif (!$workspaces) {
            redirect('/message/suspended-workspace-membership');
        } else {
            $data['workspaces'] = $workspaces;
        }

        return $this->render('SiteBundle:Default:app', $data);
    }

    /**
     * pricingAction
     * Insert description here
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function pricingAction()
    {
        return $this->render('SiteBundle:Default:pricing.html.twig');
    }

    /**
     * featuresAction
     * Insert description here
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function featuresAction()
    {
        return $this->render('SiteBundle:Default:features.html.twig');
    }

    /**
     * aboutUsAction
     * Insert description here
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function aboutUsAction()
    {
        return $this->render('SiteBundle:Default:aboutus.html.twig');
    }

    /**
     * contactUsAction
     * Insert description here
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function contactUsAction()
    {
        return $this->render('SiteBundle:Default:contactus.html.twig');
    }

    /**
     * stripeAction
     * Insert description here
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function stripeAction()
    {
        return $this->render('SiteBundle:Default:stripe.html.twig');
    }

    /**
     * messageAction
     * Insert description here
     *
     * @param $type
     * @param $is_popup
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function messageAction($type, $is_popup = false)
    {
        $this->load->library('Usertrack');

        $data = array();

        if (ENVIRONMENT === 'production') {
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : false;
            $data['usertrack'] = $this->usertrack->get_data($user_id);
        }

        $data['is_popup'] = $is_popup;

        if ($type === 'error') {
            $data['type'] = 'custom';
            $data['status'] = 'failure';
            $data['title'] = 'Failure';
            $data['msg'] = 'Unexpected error';
            $data['next_page'] = [
                'url' => PROTOCOL . '://' . $this->config->item('main_domain'),
                'delay' => 5,
                // 'redirect' => TRUE,
                'name' => 'Home',
            ];
        } elseif ($type === 'logout-required') {
            $data['type'] = 'custom';
            $data['status'] = 'failure';
            $data['title'] = 'Failure';
            $data['msg'] = 'Please logout first, and then retry your action again.';
            $data['next_page'] = [
                'url' => PROTOCOL . '://app.' . $this->config->item('main_domain'),
                'delay' => 5,
                // 'redirect' => TRUE,
                'name' => 'Go to App',
            ];
        } elseif ($type === 'account-invitation-expired') {
            $data['type'] = 'custom';
            $data['status'] = 'failure';
            $data['title'] = 'Failure';
            $data['msg'] = 'This confirmation link has expired.';
            $data['next_page'] = [
                'url' => PROTOCOL . '://' . $this->config->item('main_domain'),
                'delay' => 5,
                // 'redirect' => TRUE,
                'name' => 'Home',
            ];
        } elseif ($type === 'account-invitation-removed') {
            $data['type'] = 'custom';
            $data['status'] = 'failure';
            $data['title'] = 'Failure';
            $data['msg'] = 'We\'re sorry, but you\'ve been removed from this team. Please contact your company administrator for further details.';
            $data['next_page'] = [
                'url' => PROTOCOL . '://' . $this->config->item('main_domain'),
                'delay' => 5,
                // 'redirect' => TRUE,
                'name' => 'Home',
            ];
        } elseif ($type === 'account-invitation-resent') {
            $data['type'] = 'custom';
            $data['status'] = 'failure';
            $data['title'] = 'Failure';
            $data['msg'] = 'This confirmation link has expired. Make sure you use latest one.';
            $data['next_page'] = [
                'url' => PROTOCOL . '://' . $this->config->item('main_domain'),
                'delay' => 5,
                // 'redirect' => TRUE,
                'name' => 'Home',
            ];
        } elseif ($type === 'suspended-account-membership') {
            $data['type'] = 'custom';
            $data['status'] = 'failure';
            $data['title'] = 'Failure';
            $data['msg'] = 'Your account membership has been made inactive or deleted. Please contact your company administrator for further details.';
            $data['next_page'] = [
                'url' => PROTOCOL . '://' . $this->config->item('main_domain'),
                'delay' => 5,
                // 'redirect' => TRUE,
                'name' => 'Home',
            ];
        } elseif ($type === 'workspace-invitation-expired') {
            $data['type'] = 'custom';
            $data['status'] = 'failure';
            $data['title'] = 'Failure';
            $data['msg'] = 'This confirmation link has expired.';
            $data['next_page'] = [
                'url' => PROTOCOL . '://' . $this->config->item('main_domain'),
                'delay' => 5,
                // 'redirect' => TRUE,
                'name' => 'Home',
            ];
        } elseif ($type === 'workspace-invitation-removed') {
            $data['type'] = 'custom';
            $data['status'] = 'failure';
            $data['title'] = 'Failure';
            $data['msg'] = 'We\'re sorry, but you\'ve been removed from this team. Please contact your company administrator for further details.';
            $data['next_page'] = [
                'url' => PROTOCOL . '://' . $this->config->item('main_domain'),
                'delay' => 5,
                // 'redirect' => TRUE,
                'name' => 'Home',
            ];
        } elseif ($type === 'workspace-invitation-resent') {
            $data['type'] = 'custom';
            $data['status'] = 'failure';
            $data['title'] = 'Failure';
            $data['msg'] = 'This confirmation link has expired. Make sure you use latest one.';
            $data['next_page'] = [
                'url' => PROTOCOL . '://' . $this->config->item('main_domain'),
                'delay' => 5,
                // 'redirect' => TRUE,
                'name' => 'Home',
            ];
        } elseif ($type === 'suspended-workspace-membership') {
            $data['type'] = 'custom';
            $data['status'] = 'failure';
            $data['title'] = 'Failure';
            $data['msg'] = 'Your workspace membership has been made inactive or deleted. Please contact your company administrator for further details.';
            $data['next_page'] = [
                'url' => PROTOCOL . '://' . $this->config->item('main_domain'),
                'delay' => 5,
                // 'redirect' => TRUE,
                'name' => 'Home',
            ];
        } elseif ($type === 'twitter-signin-failure') {
            $data['type'] = 'twitter';
            $data['process'] = 'signin';
            $data['status'] = 'failure';
        } elseif ($type === 'twitter-signin-success') {
            $data['type'] = 'twitter';
            $data['process'] = 'signin';
            $data['status'] = 'success';
        } elseif ($type === 'twitter-signup-failure') {
            $data['type'] = 'twitter';
            $data['process'] = 'signup';
            $data['status'] = 'failure';
        } elseif ($type === 'twitter-signup-success') {
            $data['type'] = 'twitter';
            $data['process'] = 'signup';
            $data['status'] = 'success';
        } elseif ($type === 'google-signin-failure') {
            $data['type'] = 'google';
            $data['process'] = 'signin';
            $data['status'] = 'failure';
        } elseif ($type === 'google-signin-success') {
            $data['type'] = 'google';
            $data['process'] = 'signin';
            $data['status'] = 'success';
        } elseif ($type === 'google-signup-failure') {
            $data['type'] = 'google';
            $data['process'] = 'signup';
            $data['status'] = 'failure';
        } elseif ($type === 'google-signup-success') {
            $data['type'] = 'google';
            $data['process'] = 'signup';
            $data['status'] = 'success';
        } elseif ($type === 'twitter-integration-failure') {
            $data['type'] = 'twitter';
            $data['process'] = 'integration';
            $data['status'] = 'failure';
        } elseif ($type === 'twitter-integration-success') {
            $data['type'] = 'twitter';
            $data['process'] = 'integration';
            $data['status'] = 'success';
        } elseif ($type === 'google-integration-failure') {
            $data['type'] = 'google';
            $data['process'] = 'integration';
            $data['status'] = 'failure';
        } elseif ($type === 'google-integration-success') {
            $data['type'] = 'google';
            $data['process'] = 'integration';
            $data['status'] = 'success';
        } elseif ($type === 'slack-integration-failure') {
            $data['type'] = 'slack';
            $data['process'] = 'integration';
            $data['status'] = 'failure';
        } elseif ($type === 'slack-integration-success') {
            $data['type'] = 'slack';
            $data['process'] = 'integration';
            $data['status'] = 'success';
        }

        return $this->render('SiteBundle:Default:message.html.twig', $data);
    }

    /**
     * sharedVideoAction
     * Insert description here
     *
     * @param $key
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function sharedVideoAction($key)
    {
        $this->load->model('Share_model');

        $share = $this->Share_model->get_by_key_for_email_video($key);
        if ($share === null) {
            $this->app_core->log(App_core::CODE_DB_ERROR, 'Share_model->get');
            show_404();
        } elseif ($share === false) {
            show_404();
        } else {
            return $this->render('SiteBundle:Default:video_landing.html.twig', $share);
        }
    }
}
