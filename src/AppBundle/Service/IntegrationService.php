<?php
namespace AppBundle\Service;

use Abraham\TwitterOAuth\TwitterOAuth;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Client;
use AppBundle\Entity\Source;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Description of IntegrationService
 *
 * @author Josh Murphy
 */
class IntegrationService
{

    protected $user;

    /**
     *
     *
     */
    public function __construct($container, TokenStorage $tokenStorage)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->user = $tokenStorage->getToken()->getUser();
    }

    public function updateClientIntegrations(Client $client, $integrations = [])
    {
        $twitterService = $this->container->get('app.twitter_service');
        $twitterService->authUser($this->user);
        $user = $twitterService->getUserInfo($client);
        if (!$user) {
            return false;
        }
        $source = $this->em->getRepository('AppBundle:Source')->findOneBy(['code' => $user->id_str, 'type' => Source::TYPE_TWITTER_USER]);
        $extra = json_encode(array(
            'avatar' => str_replace('http://', '//', $user->profile_image_url),
            'fullname' => $user->name,
            'username' => $user->screen_name,
            'description' => $user->description,
            'location' => $user->location
        ));
        if ($source) {
            $source->setExtra($extra);
            $source->setUpdatedAt(time());
        } else {
            $source = new Source();
            $source->setCode($user->id_str);
            $source->setType(Source::TYPE_TWITTER_USER);
            $source->setExtra($extra);
            $source->setIsEnabled(1);
        }
        $this->em->persist($source);
        $this->em->flush();
        //if ($client->getSource() == NULL) {
            $client->setSource($source);
            $this->em->persist($source);
            $this->em->flush();
        //}
        return true;
    }
}
