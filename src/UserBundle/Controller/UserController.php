<?php

namespace UserBundle\Controller;

use OAuthBundle\Document\User;
use OAuthBundle\Form\RegistrationForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function registerAction(Request $request)
    {
        $form = $this->createForm(RegistrationForm::class);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $user = $form->getData();
            $encoded = $this->get("security.password_encoder")->encodePassword(
                $user,
                $user->getPlainPassword()
            );
            $user->setPassword($encoded);
            $em = $this->get("t_boileau_rethink.entity_manager");
            $em->insert($user);
            $this->addFlash('success', 'Welcome '.$user->getEmail());

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('user.login_authenticator'),
                    'main'
                );
        }

        return $this->render('UserBundle:User:register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
