<?php

declare(strict_types=1);


use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model;
use Users;

class UsersController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        //
        $users = Users::find();
        $this->view->users = $users;
    }

    /**
     * Searches for users
     */
    public function searchAction()
    {
        $numberPage = 1;

        if ($this->request->isPost()) {

            $query = Criteria::fromInput($this->di, "Users", $_POST);
        } else {
            $numberPage = $this->request->getQuery("page", "int");
            if ($numberPage <= 0) {
                $numberPage = 1;
            }
        }

        $parameters = array();
        // if ($this->session->conditions) {
        $parameters["conditions"] = $query->getConditions();
        // }

        // $parameters["order"] = "id"; 
        $users = Users::find($parameters);
        var_dump($users);
        die();
        if (count($users) == 0) {
            $this->flash->notice("The search did not find any users");

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }

        $paginator = new \Phalcon\Paginator\Adapter\Model(array(
            "data" => $users,
            "limit" => 10,
            "page" => $numberPage
        ));

        $page = $paginator->getPaginate();
        $this->view->setVar("page", $page);
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {
        //
    }

    /**
     * Edits a user
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {
            $user = Users::findFirstByid($id);
            if (!$user) {
                $this->flash->error("user was not found");

                $this->dispatcher->forward([
                    'controller' => "users",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $user->id;

            $this->tag->setDefault("id", $user->id);
            $this->tag->setDefault("email", $user->email);
            $this->tag->setDefault("name", $user->name);
            $this->tag->setDefault("address", $user->address);
        }
    }

    /**
     * Creates a new user
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'index'
            ]);

            return;
        }

        $user = new Users();
        $user->email = $this->request->getPost("email", "email");
        $user->name = $this->request->getPost("name");
        $user->address = $this->request->getPost("address");


        if (!$user->save()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error($message->getMessage());
            }
            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("user was created successfully");

        $this->response->redirect('users');
        $this->view->disable();
    }

    /**
     * Saves a user edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $user = Users::findFirstByid($id);

        if (!$user) {
            $this->flash->error("user does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'index'
            ]);

            return;
        }

        $user->email = $this->request->getPost("email", "email");
        $user->name = $this->request->getPost("name");
        $user->address = $this->request->getPost("address");


        if (!$user->save()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'edit',
                'params' => [$user->id]
            ]);

            return;
        }

        $this->flash->success("user was updated successfully");

        $this->response->redirect('users');
        $this->view->disable();
    }

    /**
     * Deletes a user
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $user = Users::findFirstByid($id);
        if (!$user) {
            $this->flash->error("user was not found");

            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'index'
            ]);

            return;
        }

        if (!$user->delete()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("user was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "users",
            'action' => "index"
        ]);
    }
}
