<?php
namespace UserBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\Entity\Integration;
use AppBundle\Service\UserService;
use Doctrine\ORM\EntityManager;

class FOSUBUserProvider extends BaseClass
{

    /**
     * Constructor.
     *
     * @param UserManagerInterface $userManager fOSUB user provider
     * @param array                $properties  property mapping
     */
    public function __construct($userManager, array $properties, EntityManager $em, UserService $userService)
    {
        parent::__construct($userManager, $properties);
        $this->em = $em;
        $this->userService = $userService;
    }

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();
//on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($service);
        $setter_id = $setter . 'Id';
        $setter_token = $setter . 'AccessToken';
//we "disconnect" previously connected users
        $integration = $this->em->getRepository('AppBundle:Integration')->findOneBy(['code' => $username]);
        if (null !== $previousUser = $integration->getUser()) {
            //if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }
//we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());
        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();
        $integration = $this->em->getRepository('AppBundle:Integration')->findOneBy(['code' => $username]);
        // $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));
//when the user is registrating
        if (!$integration) {

// create new user here
            $user = $this->userManager->createUser();

//modify here with relevant data
            $user->setUsername($username);
            $user->setEmail($username);
            $user->setPassword($username);
            $user->setEnabled(true);
            $this->userManager->updateUser($user);

            $integration = new Integration();
            $integration->setAvatar($response->getProfilePicture());
            $integration->setCode($username);
            $integration->setHandle($response->getNickname());
            $integration->setName($response->getRealName());
            $integration->setSource($this->em->getReference('AppBundle:Source', 0));
            $integration->setUser($user);
            switch ($response->getResourceOwner()->getName()) {
                case 'google':
                    $integration->setType(3);
                    $integration->setValues(json_encode(['access_token' => $response->getAccessToken(),
                        'access_token_secret' => $response->getTokenSecret()]));
                    break;
                case 'twitter':
                    $integration->setType(2);
                    $integration->setValues(json_encode(['access_token' => $response->getAccessToken(),
                        'access_token_secret' => $response->getTokenSecret(),
                        'twitter_user_id' => $username,
                        'twitter_screen_name' => $response->getNickname()]));
                    break;
            }
            //Persist
            $this->em->persist($integration);
            $this->em->flush();
            //Add Default data
            $this->userService->createUser($user, false);

            return $user;
        }
        switch ($response->getResourceOwner()->getName()) {
            case 'google':
                $integration->setValues(json_encode(['access_token' => $response->getAccessToken(),
                    'access_token_secret' => $response->getTokenSecret()]));
                break;
            case 'twitter':
                $integration->setType(2);
                $integration->setValues(json_encode(['access_token' => $response->getAccessToken(),
                    'access_token_secret' => $response->getTokenSecret(),
                    'twitter_user_id' => $username,
                    'twitter_screen_name' => $response->getNickname()]));
                break;
        }
//if user exists - go with the HWIOAuth way
        //$user = parent::loadUserByOAuthUserResponse($response);
        //$serviceName = $response->getResourceOwner()->getName();
        //$setter = 'set' . ucfirst($serviceName) . 'AccessToken';
//update access token
        //$user->$setter($response->getAccessToken());
        return $integration->getUser();
    }
}
