<?php
namespace Vox\Scheduleit;

require("Controller.php");
require("Events.php");

class RoomsController extends Controller {
    /**
     * @var Events
     */
    private $events;

    public function __construct()
    {
        $this->events = new Events();
        parent::__construct();
    }

    public function init()
    {
        $school = $this->getSchoolResourceGroupId();
        if ($school) {
            $resourceGroups = array(
                $school,
                Resources::GROUP_LANGUAGES,
                Resources::GROUP_COURSES,
                Resources::GROUP_TEACHERS,
                Resources::GROUP_CUSTOMERS,
                Resources::GROUP_LEARNING_MODES
            );
            $this->resources->loadResources($resourceGroups);

            $rooms = array_column($this->getRooms(), 'id');
            $this->events->loadEvents($rooms, $this->getRepresentativeDate());
            $this->events->resolveResources($this->resources);

        }
    }

    public function getSchoolResourceGroupId(){
        if(isset($_GET['school'])) {
            $school = trim($_GET['school']);
            $school = stripslashes($school);
            $school = htmlspecialchars($school);
            return $school;
        } else {
            return false;
        }
    }

    public function getSchoolName() {
        $school = $this->getSchoolResourceGroupId();
        if($school) {
            switch ($school){
                case Resources::GROUP_ROOMS_WINTERTHUR:
                    return 'Winterthur, Archstrasse 6';
                case Resources::GROUP_ROOMS_ZURICH:
                    return 'Zurich, Zähringerstrasse 51';
            }
        } else {
            return '';
        }
    }

    public function printRoom($event) {
        $school = $this->getSchoolResourceGroupId();
        return Events::printRoom($event, $school);
    }


    public function getRooms() {
        $school = $this->getSchoolResourceGroupId();
        return $this->resources->getResourcesByGroup($school);
    }

    public function getEventsByRoom() {
        $rooms = $this->getRooms();
        $events =  $this->events->getEventsContainingResources(array_keys($rooms), $this->resources, true);
        return $events;
    }
}

