<?php

namespace App\Component;
use Phalcon\Escaper;

class Escape
{

    public function sanitize($data){

        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // die();
       $escaper=new Escaper();
    //    print_r($escaper);
    //    die();
    $arr=array(
        'username' => $escaper->escapeHtml($data['username']),
         'email' => $escaper->escapeHtml($data['email']),
         'password'=> $escaper->escapeHtml($data['password'])
    );
    //   $logger->alert("This is an alert message");
        return $arr;

        

    }

    public function sanitizer($data){

        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // die();
       $escaper=new Escaper();
    //    print_r($escaper);
    //    die();
    $arr=array(
         'email' => $escaper->escapeHtml($data['email']),
         'password'=> $escaper->escapeHtml($data['password'])
    );
    //   $logger->alert("This is an alert message");
        return $arr;

        
    }


}