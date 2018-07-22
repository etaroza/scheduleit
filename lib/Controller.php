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

    public abstract function init();
}

