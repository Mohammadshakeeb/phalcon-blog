<?php

use Phalcon\Mvc\Controller;


class ConfigController extends Controller
{
    public function indexAction()
    {
        $config=$this->config->app->name;
        $this->view->appName=$config;
        //->get('app')->get('name');

       


        
    }

    public function testAction(){

        
    $test= new \App\Test\Test();
    echo $test->getdate();

    }
}