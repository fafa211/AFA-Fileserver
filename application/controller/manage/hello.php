<?php

class Hello_Admin_Controller extends Admin_Admin_Controller{
    
    /**
     * hello world
     */
    public function index_Action(){
        $this->test();
        echo 'Admin, Hello World!';
    }
}