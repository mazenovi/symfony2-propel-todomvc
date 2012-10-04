<?php 

namespace Mazenovi\TodoMVCBundle\Features\Context;

use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\MinkExtension\Context\MinkContext;

use Symfony\Component\Yaml\Yaml,
    Symfony\Component\Validator\Constraints\UrlValidator,
    Symfony\Component\Validator\Constraints\Url,
    Symfony\Component\Validator\GlobalExecutionContext,
    Symfony\Component\Validator\ExecutionContext,
    Symfony\Component\Validator\GraphWalker,
    Symfony\Component\Validator\Mapping\ClassMetadataFactoryInterface,
    Symfony\Component\Validator\ConstraintValidatorFactoryInterface;

use Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Guzzle\Http\Client,
    Guzzle\Http\Message\RequestFactory;

use Mazenovi\TodoMVCBundle\Model\Todo;

use FOS\UserBundle\Propel\UserQuery;
use Mazenovi\WsseAuthBundle\Security\Authentication\Token\WsseUserToken;


/**
 * Rest context.
 */
class RestContext extends BehatContext implements KernelAwareInterface
{

    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface $kernel
     */
    private $kernel = null;
    private $restObject        = null;
    private $restObjectType    = null;
    private $restObjectMethod  = 'get';
    private $client            = null;
    private $response          = null;
    private $requestUrl        = null;
    private $parameters        = array();
    
    /**
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     *
     * @return null
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

     public function getParameter($name)
    {
        if (count($this->kernel->getContainer()->getParameter("behat")) === 0) {
            throw new \Exception('Parameters not loaded!');
        } else {
            $parameters = $this->kernel->getContainer()->getParameter("behat");
            return (isset($parameters[$name])) ? $parameters[$name] : null;
        }
    }
    
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     */
    public function __construct($parameters)
    {
        // Initialize your context here
        $this->restObject  = new Todo();
        $this->client      = new Client();
    }

    /**
     * @Given /^that I am loggedin as "([^"]*)"$/
     */
    public function thatIAmLoggedinAs($username)
    {
        $user = UserQuery::create()->findOneByUsername($username);
        
        $token = new WsseUserToken();
        $token->setUser($username);
        $token->nonce    = hash('sha512', 'abigsecret');
        $token->created  = date('m/d/y h:i:s A');
        $token->digest   = base64_encode(sha1(base64_decode($token->nonce).$token->created.$user->getPassword(), true));
                
        $this->kernel->getContainer()->get('security.context')->setToken($token);
    }

    /**
     * @Given /^that I want to list All "([^"]*)"$/
     */
    public function thatIWantToListAll($objectType)
    {
        $this->restObjectType   = ucwords(strtolower(substr($objectType, 0, -1)));
        $this->restObjectMethod = 'get';
    }

    /**
     * @Given /^that I want to make a new "([^"]*)"$/
     */
    public function thatIWantToMakeANew($objectType)
    {
        $this->restObjectType   = ucwords(strtolower($objectType));
        $this->restObjectMethod = 'post';
    }

    /**
     * @Given /^that I want to mark a "([^"]*)" as done$/
     */
    public function thatIWantToMarkAAsDone($objectType)
    {
        $this->restObjectType   = ucwords(strtolower($objectType));
        $this->restObjectMethod = 'put';
    }

    
    /**
     * @Given /^that I want to delete a "([^"]*)"$/
     */
    public function thatIWantToDeleteA($objectType)
    {
        $this->restObjectType   = ucwords(strtolower($objectType));
        $this->restObjectMethod = 'delete';
    }

    /**
     * @Given /^that its "([^"]*)" is "([^"]*)"$/
     */
    public function thatItsIs($propertyName, $propertyValue)
    {    
        $setPropertyName = 'set'.ucwords(strtolower($propertyName));
        $this->restObject->$setPropertyName($propertyValue);
    }

    /**
     * @When /^I request "([^"]*)"$/
     */
    public function iRequest($pageUrl)
    {
        $baseUrl            = $this->getParameter('base_url');
        $this->requestUrl  = $baseUrl.$pageUrl;
        $postFields = array_change_key_case($this->restObject->toArray(), CASE_LOWER);
        $token = $this->kernel->getContainer()->get('security.context')->getToken();

        switch (strtoupper($this->restObjectMethod)) {
            case 'GET':
                $response = $this->client
                    ->get($this->requestUrl)
                    ->setHeader('Accept', 'application/json')
                    ->send();
                break;
            case 'POST':
                $response = $this->client
                    ->post($this->requestUrl, null, $postFields)
                    ->setHeader('X-WSSE', 'UsernameToken Username="'.$token->getUser().'", PasswordDigest="'.$token->digest.'", Nonce="'.$token->nonce.'", Created="'.$token->created.'"')
                    ->setHeader('Accept', 'application/json')
                    ->send();
                break;
            case 'PUT':
                $postFields['completed'] = '1';
                $response = $this->client
                    ->put($this->requestUrl.$postFields['id'], null, json_encode($postFields))
                    ->setHeader('Content-Type', 'application/json; charset=UTF-8')
                    ->setHeader('X-WSSE', 'UsernameToken Username="'.$token->getUser().'", PasswordDigest="'.$token->digest.'", Nonce="'.$token->nonce.'", Created="'.$token->created.'"')
                    ->setHeader('Accept', 'application/json, text/javascript, */*; q=0.01')
                    ->send();

                /**
                 * ,Version à base d'en-têtes existanes ... peut être utile
                 * $response = RequestFactory::getInstance()->fromMessage(
                 *   "PUT /app_dev.php/todos/".$postFields['id']." HTTP/1.1\r\n".
                 *   "Host: todo\r\n".
                 *   "Accept: application/json, text/javascript, * /*; q=0.01\r\n".
                 *   "Content-Type: application/json; charset=UTF-8\r\n".
                 *   "\r\n".
                 *   json_encode($postFields)
                 * )
                 * ->setClient($this->client)
                 * ->send();
                 */
                
                break;
            case 'DELETE':
                $response = $this->client
                    ->delete($this->requestUrl.$postFields['id'])
                    ->setHeader('X-WSSE', 'UsernameToken Username="'.$token->getUser().'", PasswordDigest="'.$token->digest.'", Nonce="'.$token->nonce.'", Created="'.$token->created.'"')
                    ->setHeader('Accept', 'application/json')
                    ->send();
                break;
        }
        $this->response = $response;
    }

    /**
     * @Then /^the response is JSON$/
     */
    public function theResponseIsJson()
    {
        $data = json_decode($this->response->getBody(true));
        if (empty($data)) {
            throw new \Exception("Response was not JSON\n" . $this->response);
        }
    }

    /**
     * @Given /^the response has a length equals to "([^"]*)"$/
     */
    public function theResponseHasALengthEqualsTo($responseLength)
    {
        $data = json_decode($this->response->getBody(true));
        if (count($data)!=$responseLength) {
            throw new \Exception(count($data).' is more than the '.$responseLength.' todo(s) expected');
        }
    }

    /**
     * @Given /^the response has a "([^"]*)" property$/
     */
    public function theResponseHasAProperty($propertyName)
    {
        $data = json_decode($this->response->getBody(true));

        if (!empty($data)) {
            if (!isset($data->$propertyName)) {
                throw new \Exception("Property '".$propertyName."' is not set!\n");
            }
        } else {
            throw new \Exception("Response was not JSON\n" . $this->response->getBody(true));
        }
    }

    /**
     * @Then /^the "([^"]*)" property equals "([^"]*)"$/
     */
    public function thePropertyEquals($propertyName, $propertyValue)
    {
        $data = json_decode($this->response->getBody(true));

        if (!empty($data)) {
            if (!isset($data->$propertyName)) {
                throw new \Exception("Property '".$propertyName."' is not set!\n");
            }
            if ($data->$propertyName != $propertyValue) {
                throw new \Exception('Property value mismatch! (given: '.$propertyValue.', match: '.$data->$propertyName.')');
            }
        } else {
            throw new \Exception("Response was not JSON\n" . $this->response->getBody(true));
        }
    }

    /**
     * @Given /^the type of the "([^"]*)" property is "([^"]*)"$/
     */
    public function theTypeOfThePropertyIs($propertyName,$typeString)
    {
        $data = json_decode($this->response->getBody(true));

        if (!empty($data)) {
            if (!isset($data->$propertyName)) {
                throw new \Exception("Property '".$propertyName."' is not set!\n");
            }
            // check our type
            switch (strtolower($typeString)) {
                case 'numeric':
                    if (!is_numeric($data->$propertyName)) {
                        throw new \Exception("Property '".$propertyName."' is not of the correct type: ".$theTypeOfThePropertyIsNumeric."!\n");
                    }
                    break;
                case 'url':
                    $urlConstraint = new Url();
                    $urlConstraint->message = 'Invalid url';
                    $errorList = $this->kernel->getContainer()->get('validator')->validateValue($data->$propertyName, $urlConstraint);
                    if (count($errorList) == 0) {
                        throw new \Exception("Property '".$propertyName."' is not of the correct type: ".$theTypeOfThePropertyIsNumeric."!\n");
                    }
                    break;
            }

        } else {
            throw new \Exception("Response was not JSON\n" . $this->response->getBody(true));
        }
    }

    /**
     * @Given /^the response status code is (\d+)$/
     */
    public function theResponseStatusCodeIs($httpStatus)
    {
        if ((string)$this->response->getStatusCode() !== $httpStatus) {
            throw new \Exception('HTTP code does not match '.$httpStatus.
                ' (actual: '.$this->response->getStatusCode().')');
        }
    }

     /**
     * @Then /^echo last response$/
     */
    public function echoLastResponse()
    {
        $this->printDebug(
            $this->requestUrl."\n\n".
            $this->response
        );
    }
}
