<?php
namespace AppBundle\Security\Handler;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * AuthenticationHandler
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
class AuthenticationHandler
implements AuthenticationSuccessHandlerInterface,
           AuthenticationFailureHandlerInterface
{
    /**
     * onAuthenticationSuccess
     * Insert description here
     *
     * @param Request
     * @param $request
     * @param TokenInterface
     * @param $token
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if ($request->isXmlHttpRequest()) {
            $result = array('success' => true);
            return new JsonResponse($result);
        } else {
            // Handle non XmlHttp request here
        }
    }

    /**
     * onAuthenticationFailure
     * Insert description here
     *
     * @param Request
     * @param $request
     * @param AuthenticationException
     * @param $exception
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->isXmlHttpRequest()) {
            $result = array('success' => false);
            return new JsonResponse($result);
        } else {
            // Handle non XmlHttp request here
        }
    }
}