<?php

class Login extends Controller {

  function __construct() {
    parent::__construct();
  }

  public function index() {

    if (isset($_SESSION['scouty_email'])) {
      header('location: ' . _SITEROOT_ . 'feed');
      exit;
    }

    if(isset($_POST['username']) && isset($_POST['password'])) {
      $login = $this->model->process();
      echo json_encode($login);
      exit;
    }

    $this->bodyClass = 'fixed-header';    
    $this->title = 'Login';
    $this->noMenu = true;
    $this->loadPage();
    $this->render('index');
    $this->loadFooter();

  }

  public function logout() {
    
    unset($_SESSION['scouty_email']);
    unset($_SESSION['scouty_user_id']);
    unset($_SESSION['scouty_username']);
    unset($_SESSION['scouty_name']);
    unset($_SESSION['scouty_firstname']);
    unset($_SESSION['scouty_lastname']);
    unset($_SESSION['scouty_company_id']);
    unset($_SESSION['scouty_menu_status']);
    
    header( "refresh:0;". _SITEROOT_ . "login" );
    exit;
  }

}
?>
