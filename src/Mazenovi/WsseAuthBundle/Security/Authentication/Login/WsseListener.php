<?php 
// see http://stackoverflow.com/questions/11180351/symfony2-after-successful-login-event-perform-set-of-actions

namespace Mazenovi\WsseAuthBundle\Security\Authentication\Login;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class WsseListener
{
    public function __construct(SecurityContext $security, Session $session, $cacheDir)
    {
        $this->security = $security;
        $this->session = $session;
        $this->cacheDir = $cacheDir;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        // creates a nonce, a time stamp, the password digest and store all in session
        $user = $this->security->getToken()->getUser();
        // see http://www.xml.com/pub/a/2003/12/17/dive.html        
        // this piece of code shoulde be in a custom user class BUT
        $this->session->set('Nonce', hash('sha512', $this->makeRandomString()));
        $this->session->set('Created', date('m/d/y h:i:s A'));
        $this->session->set('PasswordDigest', base64_encode(sha1(base64_decode($this->session->get('Nonce')).$this->session->get('Created').$user->getPassword(), true)));
    }

    // see http://stackoverflow.com/questions/4145531/how-to-create-and-use-nonces
    public function makeRandomString($bits = 256) {
        $bytes = ceil($bits / 8);
        $return = '';
        for ($i = 0; $i < $bytes; $i++) {
            $return .= chr(mt_rand(0, 255));
        }
        return $return;
    }

}
