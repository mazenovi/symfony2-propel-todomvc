<?php

namespace Mazenovi\TodoMVCBundle\Features\Context;

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException,
    Behat\Behat\Event\SuiteEvent;

use Behat\MinkExtension\Context\MinkContext;

use Symfony\Component\HttpKernel\KernelInterface;

use Behat\Symfony2Extension\Context\KernelAwareInterface;

use Mazenovi\TodoMVCBundle\Model\TodoQuery,
    Mazenovi\TodoMVCBundle\Model\Todo;


/**
 * Browser Context.
 */
class BrowserContext extends MinkContext
{
    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface $kernel
     */
    private $kernel        = null;
    private $driver            = null;
    private $session           = null;

    /**
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     *
     * @return null
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }
    
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     */
    public function __construct($parameters)
    {
        // Initialize your context here
        $this->driver = new \Behat\Mink\Driver\SahiDriver('firefox');
        $this->session = new \Behat\Mink\Session($this->driver);
    }

    /**
     * @Given /^I press enter key$/
     */
    public function iPressEnterKey()
    {
        $el = $this->getSession()->getPage()->findField("new-todo");
        //$el->focus();
        $el->keyPress(13);
    }

    /**
     * @Then /^all the checkboxes should be checked$/
     */
    public function allTheCheckboxesShouldBeChecked()
    {
        $els = $this->getSession()->getPage()->findAll('css', 'li .toggle');
        foreach($els as $el)
        {
            if(!$el->isChecked() && !$el->getAttribute('disabled'))
            {
                throw new \Exception("All todos are marked as done");
            }
        }
    }

    /**
     * @When /^I uncheck first todo checkbox$/
     */
    public function iUncheckFirstTodoCheckbox()
    {
        $els = $this->getSession()->getPage()->findAll('css', 'li .toggle');
        $els[0]->uncheck();
    }

    /**
     * @Given /^I click on Login$/
     */
    public function iClickOnLogin()
    {
        $el = $this->getSession()->getPage()->find('css', '.fos_user_login a.btn-primary');
        $el->click();
    }


    /**
     * @When /^I click on Clear my completed$/
     */
    public function iClickOnClearMyCompleted()
    {
        $el = $this->getSession()->getPage()->find('css', '#clear-completed');
        $el->click();
    }

    /**
     * @When /^I click on first todo destroy link$/
     */
    public function iClickOnFirstTodoDestroyLink()
    {
        $els = $this->getSession()->getPage()->findAll('css', '.destroy');
        $els[0]->click();
    }
}