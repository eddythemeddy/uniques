<?php

class View extends Apps {

    public $title;
    public $meta_desc;
    public $meta_keywords;
    public $search_index;
    public $html_string;
    public $css_string;
    public $js_string;
    public $head_js_script;
    public $final_js_string = '';
    public $bodyClass;
    public $pagecontentclass;
    public $contentclass;
    public $templateclass;
    public $containerClass;
    public $hasHeader = true;

    public function __construct() {

        global $equrl;
    }

    public function loadHeader() {

        global $equrl;

        $meta_keywords    = '';
        $meta_description = '';
        $search_index     = '';

        $this->title = !empty($this->title) ? $this->title . " | " . _SITETITLE_ : _SITETITLE_;

        $this->html_string = "<!DOCTYPE html>\n";
        $this->html_string .= "<html>";
        $this->html_string .= "<head>\n";
        $this->html_string .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n";
        $this->html_string .= "<meta charset=\"utf-8\">\n";
        $this->html_string .= "<title>" . $this->title . "</title>\n";
        $this->html_string .= "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no\" />\n";
        $this->html_string .= "<meta name=\"apple-mobile-web-app-capable\" content=\"yes\">\n";
        $this->html_string .= "<meta name=\"apple-touch-fullscreen\" content=\"yes\">\n";
        $this->html_string .= "<meta name=\"apple-mobile-web-app-status-bar-style\" content=\"default\">\n";
        $this->html_string .= "<meta name=\"description\" content=\"" . $this->meta_desc . "\">\n";
        $this->html_string .= "<meta name=\"keywords\" content=\"" . $this->meta_keywords . "\">\n";
        $this->html_string .= "<meta name=\"robots\" content=\"index," . $this->search_index . "\" />\n";
        $this->html_string .= "<link rel=\"shortcut icon\" href=\"" . _RES_  . "public/img/favicon.png\">\n";
        
        echo $this->html_string;
    }

    public function loadBody() {

        global $equrl;
        
        echo $this->head_js_script;
        $this->page   = $equrl[0];
        $this->isMenu = !empty($_SESSION['scouty_menu_status']) ? ($_SESSION['scouty_menu_status'] == 1 ? 'menu-pin sidebar-visible' : '') : '';
        $this->crumbs = empty($this->noCrumbs) ? true : false;
        include(_SITEBODY_);
    }

    public function loadFooter() {

        global $equrl;
        
        include(_SITEFOOT_);
        echo "\n</body>\n";
        echo $this->js_string;
        echo $this->final_js_string;
        echo "</html>";
    }

    public function render($name, $args = array()) {
        //renders the view along with passed data
        global $equrl;

        $newUrl = $this->camelCaseUrl($equrl[0]);
        $this->Apps = new Apps();

        if (file_exists('plugins/' . $newUrl . '/views/' . $name . '.phtml')) {
            require 'plugins/' . $newUrl . '/views/' . $name . '.phtml';
        } else {
            die('File: "plugins/' . $newUrl . '/views/' . $name . '.phtml" does not exist!');
        }
    }

    public function getHtml($name, $args = array()) {
        //renders the view along with passed data
        global $equrl;

        $newUrl = $this->camelCaseUrl($equrl[0]);

        if (file_exists('plugins/' . $newUrl . '/views/' . $name . '.phtml')) {
            return file_get_contents('plugins/' . $newUrl . '/views/' . $name . '.phtml');
        } else {
            ob_clean();
            die('File: "plugins/' . $newUrl . '/views/' . $name . '.phtml" does not exist!');
        }
    }

    public function template($name, $args = false) {
        //renders the view along with passed data
        global $equrl;

        $this->templateArgs = $args;
        if (file_exists('template/' . $name . '.phtml')) {
            require 'template/' . $name . '.phtml';
        } else {
            ob_clean();
            die('File: "template/' . $name . '.phtml" does not exist!');
        }
    }


    public function loadPage() {

        global $equrl;
        
        $this->loadHeader();
        $bundleGlo = $this->getBundle('libs/config/bundle.config.php');
        $bundleMod = $this->getBundle('plugins/' . $equrl[0] . '/config/bundle.config.php');
        $this->buildGlobalResources($bundleGlo, $equrl);
        $this->buildModularResources($bundleMod, $equrl);
        echo $this->css_string;
        $this->loadBody();
    }

    public function buildGlobalResources($res) {
        if (!empty($res)) {
            $this->resBuild($res['global_res']);
        }
    }

    public function getBundle($file) {
        if (file_exists($file)) {
            return include($file);
        }
    }

    public function buildModularResources($res, $url) {

        global $invokingIndex;

        if (!empty($res)) {
            if ($res['module_name'] == $url[0]) {
                //means that the module name is correct
                //run core files first
                $this->resBuild($res['resources']['core_pages'], $url[0]);
            }
            if(isset($url[1])){

                $url[0] = empty($url[0]) ? 'home' : $url[0];
                $newUrl   = $this->camelCaseUrl($url[0]);
                $controller = new $newUrl;
                if (method_exists($controller, $this->camelCaseUrl($url[1]))) {
                    //if there is a sub page  then run all subpage resources
                    if(!empty($res['resources']['sub_pages']) && array_key_exists($url[1],$res['resources']['sub_pages'])){
                        $this->resBuild($res['resources']['sub_pages'][$url[1]], $url[0]);
                    }
                } else {
                    //if there is a sub page  then run all subpage resources
                    if($invokingIndex == true) {

                        if(!empty($res['resources']['sub_pages']) && array_key_exists(1,$res['resources']['sub_pages'])){
                            $this->resBuild($res['resources']['sub_pages'][1], $url[0]);
                        }
                    } else {

                        if(!empty($res['resources']['sub_pages']) && array_key_exists(0,$res['resources']['sub_pages'])){
                            $this->resBuild($res['resources']['sub_pages'][0], $url[0]);
                        }   
                    }

                }
            } else {
                //if there is a sub page  then run all subpage resources
                if(!empty($res['resources']['sub_pages']) && array_key_exists(0,$res['resources']['sub_pages'])){
                    $this->resBuild($res['resources']['sub_pages'][0], $url[0]);
                }
            }
        }
    }

    public function resBuild($files, $module = false) {

        if (array_key_exists('css', $files)) {
            $pre = $module ? _PLUG_ . $module . '/' : _RES_;
            foreach ($files['css'] as $file) {
                if (!empty($file)) {
                    $this->css_string .= "<link href=\"" . (strpos($file, "http://") !== false || strpos($file, "https://") !== false ? '' : $pre) . $file . "\" rel=\"stylesheet\">\n";
                }
            }
        }
        if (array_key_exists('js', $files)) {
            $pre = $module ? _PLUG_ . $module . '/' : _RES_;
            foreach ($files['js'] as $file) {
                if (!empty($file)) {
                    $this->js_string .= "<script type=\"text/javascript\" src=\"" . (strpos($file, "http://") !== false || strpos($file, "https://") !== false ? '' : $pre) . $file . "\"></script>\n";
                }
            }
        }
    }

    public function addFinalJs($file) {
        $this->final_js_string .= "<script type=\"text/javascript\" src=\"" . $file . "\"></script>\n";
    }


    public function addJsHeadScript($str) {
        $this->head_js_script = "<script type=\"text/javascript\">\n" . $str . "</script>\n";
    }

    public function camelCaseUrl($url) {
        $a = explode("-", $url);

        $str = "";
        foreach ($a as $key => $val) {
            $str .= ($key > 0 ? ucfirst($val) : $val);
        }

        return $str;
    }
}
