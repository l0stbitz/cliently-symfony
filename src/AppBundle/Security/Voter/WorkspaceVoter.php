<?php
namespace AppBundle\Security\Voter;

use AppBundle\Entity\Workspace;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class WorkspaceVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'view';
    const EDIT = 'edit';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::VIEW, self::EDIT))) {
            return false;
        }

        // only vote on Workspace objects inside this voter
        if (!$subject instanceof Workspace) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Workspace object, thanks to supports
        /**
 * @var Workspace $workspace 
*/
        $workspace = $subject;

        switch ($attribute) {
        case self::VIEW:
            return $this->canView($workspace, $user);
        case self::EDIT:
            return $this->canEdit($workspace, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Workspace $workspace, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($workspace, $user)) {
            return true;
        }
        return false;
    }

    private function canEdit(Workspace $workspace, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $workspace->getOwner();
    }
}