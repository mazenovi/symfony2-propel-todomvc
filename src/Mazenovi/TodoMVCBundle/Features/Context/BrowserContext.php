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
        $els = $this->getSession()->getPage()->findAll('css', 'li .check');
        foreach($els as $el)
        {
            if(!$el->isChecked())
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
        $els = $this->getSession()->getPage()->findAll('css', 'li .check');
        $els[0]->uncheck();
    }

    /**
     * @When /^I click on Clear (\d+) completed item$/
     */
    public function iClickOnClearCompletedItem($arg1)
    {
        $el = $this->getSession()->getPage()->find('css', '.todo-clear a');
        $el->click();
    }

    /**
     * @When /^I click on todo destroy link$/
     */
    public function iClickOnTodoDestroyLink()
    {
        $els = $this->getSession()->getPage()->findAll('css', '.todo-destroy');
        $els[0]->click();
    }
}