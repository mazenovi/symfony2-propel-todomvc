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

/**
 * @Route("todos")
 */
class ApiController extends Controller
{
    /**
     * For Rest Routing
     * see http://www.slideshare.net/Wombert/designing-http-interfaces-and-restful-web-services-phpday2012-20120520#65
     * see http://www.slideshare.net/Wombert/designing-http-interfaces-and-restful-web-services-phpday2012-20120520#66
     */

    /**
     * List all todos.
     *
     * @Route("/", defaults = { "_format" = "~" })
     * @Method({"GET"})
     * @View()
     */
    public function indexAction()
    {
        return TodoQuery::create()->find();
    }

    /**
     * @Route("/{id}", defaults = { "_format" = "~" })
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
     * @Route("/", defaults = { "_format" = "~" })
     * @Method({"POST"})
     * @View(statusCode=201)
     */
    public function createAction(Request $request)
    {
        $values = $request->request->all();

        $todo = new Todo();
        $todo->setContent($values['content']);
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
     * @Route("/{id}", defaults = { "_format" = "~" }, requirements = { "id" = "\d+" })
     * @Method({"PUT"})
     * @View(statusCode=200)
     * @View()
     */
    public function updateAction(Request $request, Todo $todo)
    {
        $values = $request->request->all();
        $todo->setContent($values['content']);
        $todo->setDone($values['done']);
        $todo->save();

        return $todo;
    }

    /**
     * delete an existing todo.
     *
     * @Route("/{id}", defaults = { "_format" = "~" }, requirements = { "id" = "\d+" })
     * @Method({"DELETE"})
     * @View(statusCode=200)
     */
    public function deleteAction(Request $request, Todo $todo)
    {
        $todo->delete();
    }
}
