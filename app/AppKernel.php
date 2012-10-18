<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new Propel\PropelBundle\PropelBundle(),
            new ManyMules\ManyMulesBackboneJsBundle\ManyMulesBackboneJsBundle(),
            new ManyMules\ManyMulesJQueryBundle\ManyMulesJQueryBundle(),
            new ManyMules\ManyMulesJson2JsBundle\ManyMulesJson2JsBundle(),
            new ManyMules\ManyMulesUnderscoreJsBundle\ManyMulesUnderscoreJsBundle(),
            new ManyMules\ManyMulesRequireJsBundle\ManyMulesRequireJsBundle(),     
            new ManyMules\ManyMulesFontAwesomeBundle\ManyMulesFontAwesomeBundle(),          
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Mazenovi\TodoMVCBundle\MazenoviTodoMVCBundle(),
            new Mazenovi\UserBundle\MazenoviUserBundle(),
            new Mazenovi\WsseAuthBundle\MazenoviWsseAuthBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
