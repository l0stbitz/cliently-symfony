<?php
namespace AppBundle\Security\Voter;

use AppBundle\Entity\Workflow;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class WorkflowVoter extends Voter
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

        // only vote on Workflow objects inside this voter
        if (!$subject instanceof Workflow) {
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

        // you know $subject is a Workflow object, thanks to supports
        /**
 * @var Workflow $workflow 
*/
        $workflow = $subject;

        switch ($attribute) {
        case self::VIEW:
            return $this->canView($workflow, $user);
        case self::EDIT:
            return $this->canEdit($workflow, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Workflow $workflow, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($workflow, $user)) {
            return true;
        }
        return false;
    }

    private function canEdit(Workflow $workflow, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $workflow->getOwner();
    }
}