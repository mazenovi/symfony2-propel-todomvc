<?php
// see http://php-and-symfony.matthiasnoback.nl/2012/07/symfony2-security-using-advanced-request-matchers-to-activate-firewalls/
namespace Mazenovi\WsseAuthBundle\Security\Firewall;
 
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;



 
class WsseRequestMatcher implements RequestMatcherInterface
{
    public function __construct($protectedUrls)
    {
        $this->protectedUrls = $protectedUrls;
    }

    public function matches(Request $request)
    {
        
        foreach($this->protectedUrls as $k => $protectedUrl)
        {
            $matcher = new RequestMatcher();
            $matcher->matchPath($protectedUrl['pattern']);
            if($matcher->matches($request))
            {
                return in_array($request->getMethod(), $protectedUrl['methods']);
            }
        }

        return false;
    }
}