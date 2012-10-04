<?php

namespace Mazenovi\TodoMVCBundle\Features\Context;

use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Behat\Event\FeatureEvent;

use Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

/**
 * Feature context.
 */
class FeatureContext extends BehatContext implements KernelAwareInterface
{

    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface $kernel
     */
    private static $kernel;
    private static $application;
    private $parameters;

    /**
     * Initializes context with parameters from behat.yml.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
        $this->useContext('RestContext', new RestContext($parameters));
        $this->useContext('BrowserContext', new BrowserContext($parameters));
    }

    /**
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     *
     * @return null
     */
    public function setKernel(KernelInterface $kernel)
    {
        self::$kernel = $kernel;
    }

    /** @BeforeFeature */
    public static function setupFeature(FeatureEvent $event)
    {
        self::runCommand('todomvc:build --quiet');
    }

    protected static function runCommand($command)
    {
        if (null === self::$application) {
            self::$application = new \Symfony\Bundle\FrameworkBundle\Console\Application(self::$kernel);
            self::$application->setAutoExit(false);
        }
        return self::$application->run(new \Symfony\Component\Console\Input\StringInput($command));
    }

}
