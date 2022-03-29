<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use Phalcon\Escaper;
//use Phalcon\Http\Response\Cookies;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;

class UserController extends Controller
{
    public function indexAction()
    {
        //return '<h1>Hello!!!</h1>';

    }

    public function signupAction()
    {

        if ($this->request->isPost('name') || $this->request->isPost('email')) {

            $user = new Users();
            // $escaper=new Escaper();

            // $escaper->escapeHtml($title);
            // $userdata=array(
            //     'name'=>$escaper->escapeHtml($this->request->getpost('name')),
            //     'email'=>$escaper->escapeHtml($this->request->getpost('email')),
            //     'password'=>$escaper->escapeHtml($this->request->getpost('password'))

            // );
            $data = $this->request->getpost();
            $escaper = new \App\Component\Escape();
            $sanitizedArray = $escaper->sanitize($data);

            //assign value from the form to $user
            $user->assign(
                $sanitizedArray,
                [
                    'username',
                    'email',
                    'password'
                ]
            );
            $user->status = "Restricted";
            $user->role = "Customer";
            // Store and check for errors
            $success = $user->save();

            // passing the result to the view
            $this->view->success = $success;

            if ($success) {
                $message = "Thanks for registering!";
            } else {
                $message = "Sorry, the following problems were generated:<br>"
                    . implode('<br>', $user->getMessages());
                $this->view->message = $message;
                $adapter = new Stream('../app/logs/signup.log');
                $logger  = new Logger(
                    'messages',
                    [
                        'signup' => $adapter,
                    ]
                );
                $logger->alert("failed to login");
            }

            // passing a message to the view

        }
    }

    public function loginAction()
    {

        // $data=$this->request->getpost();
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // die();
        if ($this->cookies->has("cookies")) {
            header('location: http://localhost:8080/user/home');
        } else {



            // $data = $_POST ?? array();
            // $email = $this->request->getpost('email');
            //  $password = $this->request->getpost('password');
            $data = $this->request->getpost();
            $escaper = new \App\Component\Escape();
            $sanitizedArray = $escaper->sanitizer($data);
            $email = $sanitizedArray['email'];
            $password = $sanitizedArray['password'];
            //$email = $_POST["email"];
            //$password = $_POST["password"];
            // $data = Users::query()
            //     ->where("email = '$email'")
            //     ->andwhere("password = '$password'")
            //     ->execute();
            $data = Users::find(

                [
                    'conditions' => 'email=:email: and password= :password:',
                    'bind' => [
                        'email' => $email,
                        'password' => $password
                    ]

                ]
            );

            // echo "<pre>";
            // echo($data[0]->email);
            // echo "</pre>";

            if (count($data) > 0) {

                $userdata = array(
                    'name' => $data[0]->username,
                    'id' => $data[0]->userid,
                    'email' => $data[0]->email,
                    'password' => $data[0]->password,

                );
                $this->session->login = $userdata;
                //  print_r ($this->session->get('login[name]'));
                //  print_r($this->di->get(''));
                global $container;
                //  $cookies = $container->get('cookies');
                if (isset($_POST['remember-me'])) {
                    $this->cookies->set(
                        "cookies",
                        json_encode([
                            "email" => $email,
                            "password" => $password
                        ]),
                        time() + 3600
                    );
                }
                if ($data[0]->role == 'Admin') {
                    header('location: http://localhost:8080/user/dashboard');
                } else {
                    header('location: http://localhost:8080/user/home');
                }
            } else {

                $this->session->set("msg", "Wrong credentials");
                // $response = new Response();
                // $response->setStatusCode(404, 'Not Found');
                // $response->setContent("Sorry, Wrong credentials");
                //  $response->redirect('user/error');
                // $p = $response->getContent();
                // $c = $response->getStatusCode();
                // $a = $response->getReasonPhrase();
                $adapter = new Stream('../app/logs/login.log');
                $logger  = new Logger(
                    'messages',
                    [
                        'signup' => $adapter,
                    ]
                );
                $logger->alert("This is an alert message");


                // passing a message to the view
                // $this->view->message = $message;
                // $response->send();

                // echo "<h1>".$c."</h1>";
                // echo "<h1>".$a."</h1>";


                echo $this->tag->linkTo("user/login", "Click here to Login");
            }
        }
    }


    public function dashboardAction()
    {

        //     global $container;
        //     echo "<h1>" . "DASHBOARD" . "</h1>";
        //     print_r($this->session->get('login'));
        //     // echo $this->session->get('login');
        //     echo '<form method="post" action="logout"><input type="submit" value="logout" name="logout"></form>';
        //     // $response=new Response();
        //     //print_r($response->getCookies());
        //     // header('location:http://localhost:8080/user/logout');
        //   //  $this->$container->get('datetime');
        //     $datetime = $container->get('datetime');
        //     foreach ((array)$datetime as $key => $value) {
        //         echo "<br>".$key." : ".$value;
        //     }


    }

    public function logoutAction()
    {
        $this->session->destroy();
        $this->cookies->get('cookies')->delete();

        header('location:http://localhost:8080/user/login');
    }

    public function accessAction()
    {

        // echo "<pre>";
        // print_r($_POST);
        // echo "</pre>";

        $id = $_POST['userid'];
        $status = $_POST['status'];
        // $data=$this->model('Users')::find(array('userid'=>$id));
        //$data = new Users();
        $data = Users::find(

            [
                'conditions' => 'userid=:userid:',
                'bind' => [
                    'userid' => $id,
                ]

            ]
        );
        // echo $data[0]->status;
        // die();

        if ($data[0]->status == "Approved") {
            $data[0]->status = "Restricted";
            $data[0]->save();
            //     [
            //         'status' => 'Restricted',
            //     ]
            // );
        } else {
            $data[0]->status = "Approved";
            $data[0]->save();
            //     [
            //         'status' => 'Approved',
            //     ]
            // );
        }
        header('location:http://localhost:8080/user/dashboard');
    }

    public function blogAction()
    {
        $post = new Posts();
        $data = $this->request->getpost();
        echo "<pre>";
        print_r($data);
        echo "</pre>";

        $post->assign(
            $data,
            [
                'name',
                'content',
                'title'
            ]
        );
        $post->save();
        header('location:http://localhost:8080/user/myblog');
    }

    public function homeAction()
    {
    }

    public function myblogAction()
    {
    }

    public function editAction()
    {

        $id = $this->request->getpost('id');

        //  echo "<pre>";
        //  print_r($this->request->getpost());
        //  echo "</pre>";
        //  die();
        $this->view->id = $id;
    }

    public function editblogAction()
    {

        // echo "<pre>";
        // print_r($this->request->getpost());
        // echo "</pre>";

        $id = $this->request->getpost('id');
        if (isset($_POST['name']) && isset($_POST['content']) && isset($_POST['title'])) {
            // $posts=$this->model('Posts')::find_by_id($id);

            // $posts->id=$_POST['id'];
            $posts = Posts::find(

                [
                    'conditions' => 'id=:id:',
                    'bind' => [
                        'id' => $id,
                    ]

                ]
            );

            $posts[0]->name = $_POST['name'];
            $posts[0]->title = $_POST['title'];
            $posts[0]->content = $_POST['content'];
            $posts[0]->save();
        }
        header('location:http://localhost:8080/user/myblog');
    }

    public function sessAction()
    {

        $this->session->remove('login');
        $this->cookies->get('cookies')->delete();
        header('location:http://localhost:8080/user/login');
    }
}
