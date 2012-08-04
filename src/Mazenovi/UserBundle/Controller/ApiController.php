<?php

namespace Mazenovi\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request,
	Symfony\Component\HttpFoundation\Response,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use FOS\RestBundle\Controller\Annotations\Prefix,
    FOS\RestBundle\Controller\Annotations\NamePrefix,
    FOS\RestBundle\Controller\Annotations\View,
    FOS\RestBundle\Controller\Annotations\QueryParam,
    FOS\RestBundle\Request\QueryFetcher;

use FOS\UserBundle\Propel\User;

class ApiController extends Controller
{
    /**
     * For Rest Routing
     * see http://www.slideshare.net/Wombert/designing-http-interfaces-and-restful-web-services-phpday2012-20120520#65
     * see http://www.slideshare.net/Wombert/designing-http-interfaces-and-restful-web-services-phpday2012-20120520#66
     * see http://en.wikipedia.org/wiki/HATEOAS
     */

    /**
     * get (tmp) user token
     *
     * @Route("/users/token", defaults = { "_format" = "~" }, name="mazenovi_user_api_getusertoken", options={"expose"=true}))
     * @Method({"GET"})
     * @View()
     */
    public function getUserTokenAction()
    {
        $response = new Response();

        $response->headers->set('X-CSRF-Token',$this->get('form.csrf_provider')->generateCsrfToken('csrf'));
        
        return $response;
    }

    /**
     * get user roles
     *
     * @Route("/users/roles", defaults = { "_format" = "~" }, name="mazenovi_user_api_getuserroles", options={"expose"=true})
     * @Method({"GET"})
     * @View()
     */
    public function getUserRolesAction()
    {
        if(is_object($this->get('security.context')->getToken()->getUser()))
        {
        	$roles = $this->get('security.context')->getToken()->getUser()->getRoles();
        }
        else
       	{
       		$roles = array();
       	}

        return array( 'roles' => $roles );
    }
 }
