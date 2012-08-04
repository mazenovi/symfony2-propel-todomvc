<?php

namespace Mazenovi\ToolBoxBundle\EventListener;

use Symfony\Component\DependencyInjection\Container;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface;

class RequestListener
{
    /**
	* @var CsrfProviderInterface
	*/
    private $csrfProvider;

    /**
	* @var string
	*/
    private $environment;

    /**
	* @param CsrfProviderInterface $csrfProvider
	* @param string $environment
	*/
    public function __construct(CsrfProviderInterface $csrf, $environment)
    {
        $this->csrfProvider = $csrf;
        $this->environment = $environment;
    }

    /**
	* @param GetResponseEvent $event An GetResponseEvent instance
	*/
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ('test' === $this->environment
            || false === $event->getRequest()->isXmlHttpRequest()
            || false === $event->getRequest()->hasPreviousSession()
            || 0 === strpos($event->getRequest()->getPathInfo(), '/_')
        ) {
            return;
        }

        if (!$event->getRequest()->headers->has('X-CSRF-Token')) {
            return $event->setResponse(new JsonResponse(array('error' => 'token_not_provided'), 403));
        }

        $session = $event->getRequest()->getSession();


        if (!$session->isStarted()) {
            $session->start();
        }

        $token = $event->getRequest()->headers->get('X-CSRF-Token');

        if (!$this->csrfProvider->isCsrfTokenValid('csrf', $token)) {
            return $event->setResponse(new JsonResponse(array('error' => 'token_mismatch'), 403));
        }
    }
}




