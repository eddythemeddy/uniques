<?php

class Whoops extends Controller {

  function __construct(){
    parent::__construct();
  }

  public function index(){
    $this->title = "Whoops ";
    $this->noMenu = true;
    $this->noCrumbs = true;
    $this->package = 'sidebar';
    $this->loadPage();
    $this->render('index');
    $this->loadFooter();

  }

}
?>
