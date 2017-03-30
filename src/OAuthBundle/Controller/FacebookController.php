<?php

namespace OAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class FacebookController extends Controller
{
    public function connectAction()
    {
        return $this->get('oauth2.registry')->getClient('facebook')->redirect();
    }
}
