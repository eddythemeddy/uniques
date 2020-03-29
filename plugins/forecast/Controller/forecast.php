<?php

class Forecast extends Controller {

    private $memberHelper;

    function __construct() {

        if (empty($_SESSION['scouty_email'])) {
            header('location: ' . _SITEROOT_);
            exit;
        }
    }

    public function calendarCallback(){
        
        $this->gcal = new gCalendarControllerHelper();
        if(!empty($_GET['index'])) {
            $this->gcal->index();
        }

        if(!empty($_GET['oauth'])) {
            $this->gcal->oauth();
        }
        
    }

    public function mail() {

        if(isset($_GET['to'])) {
            $this->model->sendMail();
            die;
        }

        $this->bodyClass = 'fixed-header';
        $this->loadPage();
        $this->render('mail');
        $this->loadFooter();
    }

    public function index($dateRange = null) {

        $this->range       = $dateRange;
        $this->rangeData   = $this->model->getForecastListView($dateRange);
        $this->rangeArray  = $this->model->getDateArrayFromRange($this->rangeData['rangeUgly']);
        $this->rangePretty = $this->model->prettifyDateRange($this->rangeData['rangeUgly']);

        if(!empty($_POST['fetchForecast'])) {
            echo json_encode($this->model->fetchForecast($this->rangeData['rangeUgly']));
            exit;
        }

        $this->bodyClass        = 'fixed-header';
        $this->loadPage();
        $this->render('list');
        $this->loadFooter();
    }

    public function prepSheet($dateRange = null) {
        $this->range       = $dateRange;
        $this->rangeData   = $this->model->getForecastListView($dateRange);

        
        $this->rangeArray  = $this->model->getDateArrayFromRange($this->rangeData['rangeUgly']);
        $this->rangePretty = $this->model->prettifyDateRange($this->rangeData['rangeUgly']);

        if(!empty($_POST['fetchForecast'])) {
            echo json_encode($this->model->fetchForecast($this->rangeData['rangeUgly']));
            exit;
        }

        $this->bodyClass        = 'fixed-header';
        $this->loadPage();
        $this->render('prep-sheet-2');
        $this->loadFooter();
    }

    public function all() {

        if(!empty($_POST['fetchForecast'])) {
            echo json_encode($this->model->fetchForecast());
            exit;
        }

        $this->bodyClass        = 'fixed-header';
        $this->loadPage();
        $this->render('list-all');
        $this->loadFooter();
    }

    public function event(string $eventId) {

        if(!empty($_POST['udpateForecastSubRecipe'])) {
            echo json_encode($this->model->udpateForecastSubRecipe());
            exit;
        }

        if(!empty($_POST['deleteForecastSubRecipe'])) {
            echo json_encode($this->model->deleteForecastSubRecipe());
            exit;
        }

        if(!empty($_POST['addRecipeToEvent'])) {
            echo json_encode($this->model->addRecipeToEvent($eventId));
            exit;
        }

        $this->subRecipes = $this->model->loadSubRecipes();
        $this->event      = $this->model->getEventDetails($eventId);
        $this->eventId    = $eventId;

        $this->bodyClass  = 'fixed-header';
        $this->loadPage();
        $this->render('event-view');
        $this->loadFooter();
    }

    public function calendar() {

        header("Access-Control-Allow-Origin: *");

        if(!empty($_POST['loadForecasts'])) {
            echo json_encode($this->model->loadForecasts());
            exit;
        } 

        if(!empty($_POST['updateForecastTime'])) {
            echo json_encode($this->model->updateForecastTime());
            exit;
        } 

        if(!empty($_POST['forecastViaChannel'])) {
            echo json_encode($this->model->forecastViaChannel());
            exit;
        } 

        if(!empty($_POST['saveEvent'])) {
            echo json_encode($this->model->updateForecastTime());
            exit;
        } 

        if(!empty($_POST['deleteEvent'])) {
            echo json_encode($this->model->deleteEvent());
            exit;
        } 

        $this->channels         = $this->model->loadChannels();
        $this->subRecipes       = $this->model->loadSubRecipes();

        $this->hasHeader        = false;
        $this->bodyClass        = 'no-header';
        $this->loadPage();
        $this->render('new-cal');
        $this->loadFooter();
    }

    public function purchaseOrder($range) {

        $this->pruchaseOrder = $this->model->createPurchaseOrder($range);

        $this->bodyClass = 'fixed-header';
        $this->loadPage();
        $this->render('purchase-order');
        $this->loadFooter();
    }

    public function purchaseOrderEvent($event) {

        $this->pruchaseOrder = $this->model->createPurchaseOrder($event);

        $this->bodyClass = 'fixed-header';
        $this->loadPage();
        $this->render('purchase-order');
        $this->loadFooter();
    }

    public function test() {
        global $eqDb;

        $this->eqDb = $eqDb;

        $query = $this->eqDb->withTotalCount()->rawQuery(
            "SELECT SQL_CALC_FOUND_ROWS SQL_CALC_FOUND_ROWS
                fr.`forecast_id`, 
                fr.`total`, 
                f.`start_time`, 
                f.`end_time`, 
                f.`date`, 
                c.`name` AS cl,
                CASE
                    WHEN status IS NULL THEN \"Unpaid\"
                    WHEN status = 0 THEN \"Unpaid\"
                    WHEN status = 1 THEN \"Paid\"
                END AS status,
                CASE
                    WHEN status IS NULL THEN '<span class=\"label text-uppercase label-important\">Unpaid</span>'
                    WHEN status = 0 THEN '<span class=\"label text-uppercase label-important\">Unpaid</span>'
                    WHEN status = 1 THEN '<span class=\"label text-uppercase label-inverse\">Paid</span>'
                    WHEN status = 2 THEN '<span class=\"label text-uppercase label-warning\">Canceled</span>'
                END AS statusPretty
            from forecast as f
            inner join (
               select forecast_id, SUM(total * instantaneous_subrecipe_price) as total
               from forecast_recipes 
               group by forecast_id) as fr on f.`id` = fr.`forecast_id`
            LEFT JOIN (
                SELECT id, name
                FROM channels
            ) as c on c.`id` = f.`channel_id`
            WHERE 
                (
                    deleted = '0' AND 
                    date_range = '" . $dateRange . "' AND 
                    company_id = '" . $_SESSION['scouty_company_id'] . "'
                )
            ORDER BY " . $orderBy . " " . $orderDir . " LIMIT " . $currentPage . ", " . $_POST['length']
        );

        echo $this->eqDb->totalCount;
    }

    public function updateExisting() {

        // global $eqDb;

        // $this->eqDb = $eqDb;

        // $this->eqDb->rawQuery(
        //         'UPDATE forecast SET event_type = "public"'
        //     );

        // $forecastRecs = $this->eqDb->get('forecast_recipes', null, '*');

        // foreach($forecastRecs as $key => $val) {

        //     $subRecipeId    = $val['sub_recipe_id'];

        //     // $subs = $this->eqDb->subQuery ('i');
        //     // $subs->where('company_id', $_SESSION['scouty_company_id']);
        //     // $subs->get('ingredients', null, 'id, name');

        //     // $this->eqDb->join($subs, 'sr.ingredient_id = i.id', 'LEFT');

        //     $this->eqDb->where('sr.id', $subRecipeId);
        //     $sub = $this->eqDb->getOne('recipes_sub sr',null,' sr.id, sr.price');

        //     // echo $sub['price'] . '<br/>';

        //     // $instIngWeights = addslashes(json_encode($instantaneousIngredients));

        //     $this->eqDb->rawQuery(
        //         'UPDATE forecast_recipes SET instantaneous_subrecipe_price = "' . $sub['price'] . '" WHERE id = "' . $val['id'] . '"'
        //     );
        // }


        // $subRecipeId    = $key;
        // $subName        = $variation['sub_name'];
        // $amount         = $variation['amount'];

        // $subs = $this->eqDb->subQuery ('i');
        // $subs->where('company_id', $_SESSION['scouty_company_id']);
        // $subs->get('ingredients', null, 'id, name');

        // $this->eqDb->join($subs, 'sr.ingredient_id = i.id', 'LEFT');

        // $this->eqDb->where('sr.recipe_sub_id', $subRecipeId);
        // $instantaneousIngredients = $this->eqDb->get('recipes_sub_ingredients sr',null,'i.name AS ingredient_name, i.id, ingredient_weight, sr.recipe_sub_id');

        // $instIngWeights = addslashes(json_encode($instantaneousIngredients));

        // $this->eqDb->rawQuery(
        //     'INSERT INTO forecast_recipes (forecast_id, sub_recipe_name, recipe_name, sub_recipe_id, recipe_id, total, date_range, company_id, instantaneous_subrecipe_ing_weights)
        //         VALUES ("' . $id . '","' . $subName . '","' . $recipeName . '","' . $subRecipeId . '","' . $recipeId . '","' . $amount . '","' . $dateRange . '","' . $_SESSION['scouty_company_id'] . '","' . $instIngWeights . '")'
        // );
    }
}

?>