<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
    public function indexAction(Request $request)
    {
        return $this->render('AppBundle:Default:index.html.twig');
    }

    /**
     * searchAction
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
    public function searchAction(Request $request)
    {
        return $this->render('AppBundle:Default:index.html.twig');
    }

    /**
     * clientsAction
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
    public function clientsAction(Request $request)
    {
        return $this->render('AppBundle:Default:index.html.twig');
    }

    /**
     * sourcesAction
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
    public function sourcesAction(Request $request)
    {
        return $this->render('AppBundle:Default:index.html.twig');
    }

    /**
     * settingsAction
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
    public function settingsAction(Request $request)
    {
        return $this->render('AppBundle:Default:index.html.twig');
    }

    /**
     * userAction
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
    public function userAction(Request $request)
    {
        return $this->render('AppBundle:Default:index.html.twig');
    }

    /**
     * teamMembersAction
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
    public function teamMembersAction(Request $request)
    {
        return $this->render('AppBundle:Default:index.html.twig');
    }

    /**
     * billingMembersAction
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
    public function billingMembersAction(Request $request)
    {
        return $this->render('AppBundle:Default:index.html.twig');
    }

    /**
     * integrationsAction
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
    public function integrationsAction(Request $request)
    {
        return $this->render('AppBundle:Default:index.html.twig');
    }
}
