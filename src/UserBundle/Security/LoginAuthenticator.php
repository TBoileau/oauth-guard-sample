<?php

namespace UserBundle\Security;

use UserBundle\Form\LoginForm;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginAuthenticator extends AbstractFormLoginAuthenticator
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getCredentials(Request $request)
    {
        $isLoginSubmit = $request->getPathInfo() == '/login' && $request->isMethod('POST');
        if (!$isLoginSubmit) {
            return;
        }

        $form = $this->container->get("form.factory")->create(LoginForm::class);
        $form->handleRequest($request);

        $data = $form->getData();
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $data['_username']
        );

        return $data;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['_username'];
        if($existingUser = $this->container->get("t_boileau_rethink.entity_manager")->getRepository("UserBundle\Document\User")->query(function($table) use ($username){
            return $table->filter(["username" => $username]);
        })->getSingleResult()){
            return $existingUser;
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials['_password'];

        if ($this->container->get("security.password_encoder")->isPasswordValid($user, $password)) {
            return true;
        }

        return false;
    }

    protected function getLoginUrl()
    {
        return $this->container->get("router")->generate('user_login');
    }

    protected function getDefaultSuccessRedirectUrl()
    {
        return $this->container->get("router")->generate('homepage');
    }
}
