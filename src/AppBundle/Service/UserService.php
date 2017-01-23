<?php
namespace AppBundle\Service;

use AppBundle\Entity\Plan;
use AppBundle\Entity\Account;
use AppBundle\Entity\AccountMember;
use AppBundle\Entity\Workspace;
use AppBundle\Entity\WorkspaceMember;
use AppBundle\Entity\Pipeline;
use AppBundle\Entity\Stage;
use AppBundle\Entity\User;

/**
 * Description of UserService
 *
 * @author Josh Murphy
 */
class UserService
{

    /**
     *
     *
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function createUser($user, $persistUser = true)
    {
        $em = $this->container->get('doctrine')->getManager();
        //Create User
        if ($persistUser) {
            $em->persist($user);
            $em->flush();
        }
        $account = new Account();
        $account->setType(Account::TYPE_BY_CLASS['main']['class']);
        $account->setPlanId(Plan::BY_CLASS['free']['id']);
        $account->setNextPlanId(Plan::BY_CLASS['free']['id']);
        $account->setMemberCount(1);
        $account->setOwner($user);
        $account->setIsEnabled(1);
        $account->setPlanStartedAt(time());
        $em->persist($account);
        $em->flush();

        $accountMember = new AccountMember();
        $accountMember->setAccountId($account->getId());
        $accountMember->setRole(AccountMember::ROLE_BY_CLASS['owner']['id']);
        $accountMember->setOwnerId($user->getId());
        $accountMember->setIsEnabled(1);
        $accountMember->setIsConfirmed(1);
        $em->persist($accountMember);
        $em->flush();

        $workspace = new Workspace();
        $workspace->setAccount($account);
        $workspace->setName('Workspace');
        $workspace->setOwner($user);
        $workspace->setIsEnabled(1);
        $workspace->setType(Workspace::TYPE_BY_CLASS['standard']['id']);
        $em->persist($workspace);
        $em->flush();

        $workspaceMember = new WorkspaceMember();
        $workspaceMember->setOwnerId($user->getId());
        $workspaceMember->setRole(WorkspaceMember::ROLE_BY_CLASS['owner']['id']);
        $workspaceMember->setUser($user);
        $workspaceMember->setWorkspace($workspace);
        $workspaceMember->setIsEnabled(1);
        $workspaceMember->setIsConfirmed(1);
        $workspaceMember->setCreditBalance(6);
        $em->persist($workspaceMember);
        $em->flush();
        $pipeline = $this->createInitialPipelines($user, $workspace);
        $this->createInitialStages($user, $pipeline);
    }

    public function createInitialPipelines(User $owner, Workspace $workspace)
    {
        $em = $this->container->get('doctrine')->getManager();
        $origObject = $this->container->get('doctrine')->getRepository('AppBundle:PipelineDefault')->findOneBy(array('ownerId' => 0));
        // Here modify the original object if needed...

        $newObject = new Pipeline();

        $oldReflection = new \ReflectionObject($origObject);
        $newReflection = new \ReflectionObject($newObject);

        foreach ($oldReflection->getProperties() as $property) {
            if ($newReflection->hasProperty($property->getName())) {
                $newProperty = $newReflection->getProperty($property->getName());
                $newProperty->setAccessible(true);
                $property->setAccessible(true);
                $newProperty->setValue($newObject, $property->getValue($origObject));
            }
        }
        $newObject->setOwner($owner);
        $newObject->setWorkspace($workspace);
        $em->persist($newObject);
        $em->flush();

        return $newObject;
    }

    public function createInitialStages(User $owner, Pipeline $pipeline)
    {
        $em = $this->container->get('doctrine')->getManager();
        $origObjects = $this->container->get('doctrine')->getRepository('AppBundle:StageDefault')->findBy(array('ownerId' => 0));
        foreach ($origObjects as $origObject) {
            // Here modify the original object if needed...

            $newObject = new Stage();

            $oldReflection = new \ReflectionObject($origObject);
            $newReflection = new \ReflectionObject($newObject);

            foreach ($oldReflection->getProperties() as $property) {
                if ($newReflection->hasProperty($property->getName())) {
                    $newProperty = $newReflection->getProperty($property->getName());
                    $newProperty->setAccessible(true);
                    $property->setAccessible(true);
                    $newProperty->setValue($newObject, $property->getValue($origObject));
                }
            }
            $newObject->setOwner($owner);
            $newObject->setPipeline($pipeline);
            $em->persist($newObject);
        }
        $em->flush();

        return TRUE;
    }
}
