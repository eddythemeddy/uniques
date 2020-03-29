<?php
require_once('libs/vendor/autoload.php');

class Boot {

    function __construct() {

        global $equrl;
        global $config;
        global $invokingIndex;

        $equrl = isset($_GET['v']) ? $_GET['v'] : null;
        $equrl = rtrim($equrl, '/');
        $equrl = rtrim($equrl, '-');
        $equrl = explode('/', $equrl);
        new Config();
        $this->db = new Database();

        $equrl[0] = empty($equrl[0]) ? 'forecast' : $equrl[0];
        $newUrl   = $this->camelCaseUrl($equrl[0]);
       	$file     = 'plugins/' . $newUrl . '/Controller/' . strtolower($newUrl) . '.php';

        if(!empty($_POST['eqAjax'])) {
            $this->eqAjax();
            exit;
        }

        if (file_exists($file)) {
            require $file;

            if(class_exists(ucfirst($newUrl))) {

                $controller = new $newUrl;
                $controller->loadModel($equrl[0]);
                // calling methods, if the url has more than 2 parameters
                // run method with
                if (count($equrl) > 2) {
                    if (method_exists($controller, $this->camelCaseUrl($equrl[1]))) {
                        $method = $equrl;
                        unset($method[0], $method[1]);
                        $customUrl = implode("/", $method);
                        $controller->{$this->camelCaseUrl($equrl[1])}($customUrl);
                    } else {
                        $controller->index($equrl[1]);
                    }
                } else {
                    if (isset($equrl[1])) {
                        // if url has two parameters ie.e /test/abc
                        if (method_exists($controller, $this->camelCaseUrl($equrl[1]))) {
                            //if that method 'abc' exists in that controller, then run that method,
                            $controller->{$this->camelCaseUrl($equrl[1])}();
                        } else {
                            //if that method 'abc' doesn't exist, check first if the index() method requires parameters
                            $reflection = new ReflectionMethod($controller, 'index');
                            if(count($reflection->getParameters())) {
                                //if the index method requires parameters, then plug 'abc' as an parameteter in index() i.e. index('abc')
                                $controller->index($equrl[1]);
                                $invokingIndex = true;

                            } else { 
                                //if that index method doesn't require a parameter, then send our user to a 404.
                                $this->error();
                            }
                        }
                    } else {
                        //run index method
                        $controller->index();
                    }
                }
            } else {
                $this->error();
            }
        } else {
            $this->error();
        }
    }

    public function eqAjax() {
        if(!empty($_POST['menuPin'])) {
            $this->setMenuPin();
            exit;
        }

        if(!empty($_POST['mainInterval'])) {
            $this->mainInterval();
            exit;
        }
    }

    private function mainInterval() {

        global $equrl;
        // lets check if we are logged in.
        if(empty($_SESSION['scouty_user_id']) && $equrl[0] != 'login') {
            echo json_encode([
                'r' => 'eq',
                'redirect' => _SITEROOT_
            ]);

            exit;
        }

        if(!empty($_SESSION['scouty_user_id']) && $equrl[0] == 'login') {
            echo json_encode([
                'r' => 'eq',
                'redirect' => _SITEROOT_
            ]);

            exit;
        }

        echo json_encode(['r' => 'valid']);
    }

    public function setMenuPin() {

        global $eqDb;

        $menuPin = $eqDb->escape($_POST['menuPin']);
        $menuPin = $menuPin == 'true' ? 1 : 0;

        $_SESSION['scouty_menu_status'] = $menuPin;
        
        $eqDb->where('id', $_SESSION['scouty_user_id']);
        $eqDb->update('users', [
            'menu_pin' => $menuPin
        ]);
    }

    function error() {

        global $equrl;

        $equrl[0] = "whoops";
        
        require 'plugins/whoops/Controller/whoops.php';
        $error = new Whoops();
        $error->index();
        return false;
    }

    function camelCaseUrl($url) {
        $a = explode("-", $url);

        $str = "";
        foreach ($a as $key => $val) {
            $str .= ($key > 0 ? ucfirst($val) : $val);
        }
        return $str;
    }

    function checkIfIsUser($supposedUserName) {
        require_once('libs/helpers/MemberHelper.php');

        $mHelper = new MemberHelper();
        $mem = $mHelper->getMemberByUsername($supposedUserName);

        if(count($mem)) {
            return $mem;
        } else {
            return false;
        }
    }
}
