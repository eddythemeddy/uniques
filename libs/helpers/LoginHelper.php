<?php

class LoginHelper
{

  public function render() {
    if(isset($_SESSION['scouty_user_id'])) {
      return $this->renderLoggedIn();
    }

    return $this->renderLoggedOut();
  }

  protected $pages = [
    '*home' => [
      'icon' => 'fa fa-bell',
      'label' => 'Home'
    ],
    'about' => [
      'icon' => 'fa fa-bell',
      'label' => 'About'
    ]
  ];


  public function rend2er() {

    global $equrl;

    $search = '';

    if($equrl[0] == 'search' && !empty($_GET['p'])) {
      $search = ' value="' . $_GET['p'] . '"';
    }

    $str = '<ul class="">';
    $str .= '<form class="form-inline mt-2 mt-md-0">';
    $str .= '<input class="form-control mr-sm-2" id="nav-search" type="text" placeholder="Search" aria-label="Search" ' . $search . '>';
    $str .= '</form>';

      foreach($this->pages as $key => $val):

        $keyCount = explode('*', $key);
        if(count($keyCount)) {
          $key = $keyCount[0];
          if(in_array('home', $keyCount)) {
            $key = $keyCount[1];
          }
        }

        $str .= '<li class="nav-item ' . $this->current($key) . '">' .
          '<a class="nav-link" href="/'. $key . '">' .
            $val .
            '<i class="far fa-bell"></i>' .
          '</a>' .
        '</li>';

      endforeach;
    $str .= '</ul>';

    return $str;
  }

  public function current($currentPage) {

    global $equrl;

    if($currentPage == $equrl[0]){
      return 'active';
    }
  }

  public function renderLoggedOut() {

    $str = '<ul class="ml-auto right-nav pull-right">
              <li class="nav-item">
                  <a class="nav-link" href="/login">Login</a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="/register">Register</a>
              </li>
          </ul>';

    return $str;
  }

  public function renderLoggedIn() {

    $str = '<div class="dropdown pull-right p-t-5 m-l-20 b-l b-dashed p-l-20">
              <button class="profile-dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="thumbnail-wrapper d32 circular inline">
                  <img src="/uploads/' . $_SESSION["scouty_username"] . '/profile.jpg" alt="" data-src="/uploads/' . $_SESSION["scouty_username"] . '/profile.jpg" data-src-retina="/uploads/' . $_SESSION["scouty_username"] . '/profile.jpg" width="32" height="32">
                </span>
              </button>
              <ul class="dropdown-menu profile-dropdown pull-right" role="menu">
                <li><a href="/in"><i class="flaticon flaticon-user"></i> Profile</a>
                </li>
                <li><a href="#"><i class="flaticon flaticon-settings-work-tool"></i> Settings</a>
                </li>
                <li class="p-b-2"><a href="#"><i class="flaticon flaticon-questions-circular-button"></i> Help</a>
                </li>
                <li class="bg-master-lighter p-b-0">
                  <a href="/login/logout" class="clearfix">
                    <span class="pull-left">Logout</span>
                    <span class="pull-right m-l-5"><i class="flaticon flaticon-power-button"></i></span>
                  </a>
                </li>
              </ul>
            </div>';

    return $str;
  }
}