<?php

namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\Workspace;
use AppBundle\Entity\WorkspaceMember;
use AppBundle\Form\AccountType;

/**
 * Account controller.
 */
class AccountController extends Controller
{
    /**
     * Lists all Workspace entities for an Account.
     */
    public function workspaceAction(Request $request, Account $account)
    {
        $this->denyAccessUnlessGranted('view', $account);
        if ($request->isMethod('POST')) {
            //TODO: Use proper validation form
            //Need to add this users as the owner
            $data = $request->request;
            $user = $this->getUser();
            $em = $this->getDoctrine()->getManager();
            $workspace = new Workspace();
            $workspace->setName($data->get('name'));
            $workspace->setOwnerId($user->getId());
            $workspace->setOwner($user);
            $workspace->setIsEnabled((int) $data->get('is_enabled'));
            $workspace->setAccount($account);
            $em->persist($workspace);
            
            $member = new WorkspaceMember();
            $member->setExtra(json_encode(["name" => $user->getName(), "email" => $user->getEmail()]));
            $member->setRole(1);
            $member->setIsEnabled(1);
            $member->setIsConfirmed(1);
            $member->setWorkspace($workspace);
            $member->setOwnerId($user->getId());
            
            //TODO: Default work thru
            $em->persist($member);
            $em->flush();
            return new JsonResponse(["success" => ["code" => 0, "message" => "success"]]);
        }
        return new JsonResponse($account->getWorkspacesArray());
    }

    /**
     * Finds and displays a Account entity.
     */
    public function showAction(Account $account)
    {
        $this->denyAccessUnlessGranted('view', $account);
        return new JsonResponse($account->toArray());
    }
}
