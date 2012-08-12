<?php

namespace Mazenovi\TodoMVCBundle\Controller;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use FOS\RestBundle\Controller\Annotations\Prefix,
    FOS\RestBundle\Controller\Annotations\NamePrefix,
    FOS\RestBundle\Controller\Annotations\View,
    FOS\RestBundle\Controller\Annotations\QueryParam,
    FOS\RestBundle\Request\QueryFetcher;

use Mazenovi\TodoMVCBundle\Model\TodoQuery,
    Mazenovi\TodoMVCBundle\Model\Todo;

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
     * List all todos.
     *
     * @Route("/todos/", defaults = { "_format" = "~" }, name="mazenovi_todomvc_api_index", options={"expose"=true})
     * @Method({"GET"})
     * @View()
     */
    public function indexAction(Request $request)
    {
        //$request->headers->set('X-CSRF-Token',$this->get('form.csrf_provider')->generateCsrfToken('csrf'));
        return TodoQuery::create()->find();
    }

    /**
     * List all user's todos.
     *
     * @Route("/users/{id}/todos/", defaults = { "_format" = "~" }, requirements = { "id" = "\d+" }, name="mazenovi_todomvc_api_listuser", options={"expose"=true})
     * @Method({"GET"})
     * @View()
     */
    public function listUserAction(/*User $user*/)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if(is_object($user))
        {
            return $user->getTodos();
        }
        else
        {
            return TodoQuery::create()->filterByFosUserId(null)->find();
        }
    }

    /**
     * @Route("/todos/{id}", defaults = { "_format" = "~" })
     * @Method({"GET"})
     * @View()
     */
    public function showAction(Todo $todo)
    {
        return $todo;
    }

    /**
     * Create a new todo.
     *
     * @Route("/todos/", defaults = { "_format" = "~" })
     * @Method({"POST"})
     * @View(statusCode=201)
     */
    public function createAction(Request $request)
    {
        $values = $request->request->all();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $todo = new Todo();
        $todo->setTitle($values['title']);
        if(is_object($user))
        {
            $todo->setFosUserId($user->getId());
        }
        $todo->save();

        $url = $this->get('router')->generate(
            'mazenovi_todomvc_api_show',
            array('id' => $todo->getId()),
            true
        );

        return array('url' => $url);
    }

    /**
     * update an existing todo.
     *
     * @Route("/todos/{id}", defaults = { "_format" = "~" }, requirements = { "id" = "\d+" })
     * @Method({"PUT"})
     * @View(statusCode=200)
     * @View()
     */
    public function updateAction(Request $request, Todo $todo)
    {
        $values = $request->request->all();
        $todo->setTitle($values['title']);
        $todo->setCompleted($values['completed']);
        $todo->save();

        return $todo;
    }

    /**
     * delete an existing todo.
     *
     * @Route("/todos/{id}", defaults = { "_format" = "~" }, requirements = { "id" = "\d+" })
     * @Method({"DELETE"})
     * @View(statusCode=200)
     */
    public function deleteAction(Request $request, Todo $todo)
    {
        $todo->delete();
    }
}
