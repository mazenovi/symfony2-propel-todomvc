<?php

namespace Mazenovi\TodoMVCBundle\View;

use FOS\RestBundle\View\View,
    FOS\RestBundle\View\ViewHandler,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\SecurityContextInterface;

use Symfony\Component\Security\Core\User\UserInterface;

class JSONViewHandler
{
    private $securityContext;
    private $serializer;

    /**
     * @param SecurityContextInterface $securityContext
     * @param serializer
     */
    public function __construct(SecurityContextInterface $securityContext, $serializer)
    {
        $this->securityContext = $securityContext;
        $this->serializer = $serializer;
    }

    /**
     * @param ViewHandler $viewHandler
     * @param View $view
     * @param Request $request
     * @param string $format
     *
     * @return Response
     */
    public function createResponse(ViewHandler $handler, View $view, Request $request, $format)
    {
        $data = $view->getData();
        $user = $this->securityContext->getToken()->getUser();
        if($view->getData() instanceOf \PropelObjectCollection && $user instanceof UserInterface)
        {    
            $data = array();
            foreach($view->getData() as $k => $o)
            {

                $refl = new \ReflectionClass('Symfony\Component\Security\Acl\Permission\MaskBuilder');

                foreach($refl->getConstants() as $k => $val)
                {
                    if(preg_match('/^MASK\_/', $k))
                    {
                        if($this->securityContext->isGranted(substr($k, 5), $o))
                        {
                            $o->addPermission(substr($k, 5));
                        }
                        foreach(call_user_func_array(array(get_class($o).'Peer','getFieldNames'), array(\BasePeer::TYPE_FIELDNAME)) as $field)
                        {
                            if($this->securityContext->isGranted(substr($k, 5), $o, $field))
                            {
                                $o->addFieldPermission(substr($k, 5), $field);
                            }
                        }
                    }
                }
                array_push($data, $o);            
            }
        }
        return new Response($this->serializer->serialize($data, $format), 200, $view->getHeaders());
    }
}
