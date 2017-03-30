<?php

namespace OAuthBundle\Security;

use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use UserBundle\Document\User;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FacebookAuthenticator extends SocialAuthenticator
{
  private $container;

  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;
  }

  public function getCredentials(Request $request)
  {
    if ($request->getPathInfo() != '/connect/facebook/check') {
      return;
    }
    return $this->fetchAccessToken($this->getFacebookClient());
  }

  public function getUser($credentials, UserProviderInterface $userProvider)
  {
    $facebookUser = $this->getFacebookClient()->fetchUserFromToken($credentials);
    if($existingUser = $this->container->get("t_boileau_rethink.entity_manager")->getRepository("UserBundle\Document\User")->query(function($table) use ($facebookUser){
        return $table->filter(["facebook_id" => $facebookUser->getId()]);
    })->getSingleResult()){
        return $existingUser;
    }

    $user = new User();
    $user->setFacebookId($facebookUser->getId());
    $user->setFirstname($facebookUser->getFirstName());
    $user->setLastname($facebookUser->getLastName());
    $user->setUsername($facebookUser->getEmail());
    $user->setEmail($facebookUser->getEmail());
    $this->container->get("t_boileau_rethink.entity_manager")->insert($user);

    return $user;
  }

  private function getFacebookClient()
  {
    return $this->container->get("oauth2.registry")->getClient('facebook');
  }

  public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
  {
      $url = $this->container->get("router")->generate('homepage');
      return new RedirectResponse($url);
  }

  public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
  {
    $data = array(
      'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
    );
    return new JsonResponse($data, Response::HTTP_FORBIDDEN);
  }
  public function start(Request $request, AuthenticationException $authException = null)
  {
    $data = array(
      'message' => 'Authentication Required'
    );
    return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
  }
}
