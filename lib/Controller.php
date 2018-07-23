<?php
namespace Vox\Scheduleit;

require("Api.php");
require("Resources.php");
include_once dirname(__FILE__)."/../config.php";

abstract class Controller {
    /**
     * @var Api
     */
    protected $api;
    /**
     * @var Resources
     */
    protected $resources;

    public function __construct()
    {
        $this->api = new Api(USER_ID, USERNAME, PASSWORD);
        $this->resources = new Resources();
        $this->init();
    }

    public function getRepresentativeDate() {
        $date = date("Y-m-d");
        if(isset($_GET['date'])) {
            $date = trim($_GET['date']);
            $date = stripslashes($date);
            $date = htmlspecialchars($date);
        }
        return $date;
    }

    public abstract function init();
}

