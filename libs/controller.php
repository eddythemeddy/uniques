<?php

class Controller extends View {
    
    function __construct() {
    }

    /*
    * Open the Model for this controller if it exists.
    */
    public function loadModel($name) {

        global $url;
        $this->Apps = new Apps();
        
        $path = 'plugins/' . $name . '/Model/' . $name . '_model.php';

        if (file_exists($path)) {

            require $path;
            $modelName   = ucfirst($name) . '_Model';
            $this->model = new $modelName();
        }
    }
}
