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

class GoogleAuthenticator extends SocialAuthenticator
{
  private $container;

  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;
  }

  public function getCredentials(Request $request)
  {
    if ($request->getPathInfo() != '/connect/google/check') {
      return;
    }
    return $this->fetchAccessToken($this->getGoogleClient());
  }

  public function getUser($credentials, UserProviderInterface $userProvider)
  {
    $googleUser = $this->getGoogleClient()->fetchUserFromToken($credentials);
    if($existingUser = $this->container->get("t_boileau_rethink.entity_manager")->getRepository("UserBundle\Document\User")->query(function($table) use ($googleUser){
        return $table->filter(["google_id" => $googleUser->getId()]);
    })->getSingleResult()){
        return $existingUser;
    }

    $user = new User();
    $user->setGoogleId($googleUser->getId());
    $user->setFirstname($googleUser->getFirstName());
    $user->setLastname($googleUser->getLastName());
    $user->setUsername($googleUser->getEmail());
    $user->setEmail($googleUser->getEmail());
    $this->container->get("t_boileau_rethink.entity_manager")->insert($user);

    return $user;
  }

  private function getGoogleClient()
  {
    return $this->container->get("oauth2.registry")->getClient('google');
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
