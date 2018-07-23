<?php
namespace Vox\Scheduleit;

require("Controller.php");
require("Events.php");

class PersonController extends Controller {
    /**
     * @var Events
     */
    private $events;
    private $currentTeacherId;
    private $currentCustomerId;
    private $eventsLoaded;

    public function __construct()
    {
        $this->events = new Events();
        parent::__construct();
    }

    public function init()
    {
        $email = $this->getPersonEmail();
        if ($email) {
            $resourceGroups = array(
                Resources::GROUP_ROOMS_ZURICH,
                Resources::GROUP_ROOMS_WINTERTHUR,
                Resources::GROUP_EXTERNAL_LOCATIONS,
                Resources::GROUP_LANGUAGES,
                Resources::GROUP_COURSES,
                Resources::GROUP_TEACHERS,
                Resources::GROUP_CUSTOMERS,
                Resources::GROUP_LEARNING_MODES,
                Resources::GROUP_INTENSITIES,
            );

            $this->resources->loadResources($resourceGroups);

            $personId = $this->findTeacherResourceIdByEmail();
            if (!$personId) {
                $personId = $this->findCustomerResourceIdByEmail();
                if($personId){
                    $this->currentCustomerId = $personId;
                }
            } else {
                $this->currentTeacherId = $personId;
            }

            if ($personId){
                $now = new \DateTime($this->getRepresentativeDate());
                $from = $now->modify("first day of previous month");
                $now = new \DateTime($this->getRepresentativeDate());
                $to = $now->modify("first day of +2 months");
                $this->eventsLoaded = $this->events->loadEvents($personId, $from->format('Y-m-d'), $to->format('Y-m-d'));
                $this->events->resolveResources($this->resources);
            }
        }
    }

    public function didEventsLoadSuccessfully() {
        return $this->eventsLoaded !== false;
    }

    public function getPersonEmail(){
        if(isset($_GET['email'])) {
            $email = trim($_GET['email']);
            $email = stripslashes($email);
            $email = htmlspecialchars($email);
            return $email;
        } else {
            return false;
        }
    }

    public function getPersonId(){
        if($this->isTeacher()) {
            return $this->currentTeacherId;
        }

        if($this->isCustomer()) {
            return $this->currentCustomerId;
        }

        return false;
    }

    public function findTeacherResourceIdByEmail($email = null) {
        if (!$email) {
            $email = $this->getPersonEmail();
        }
        if (!$email) {
            return false;
        }

        $teachers = $this->resources->getResourcesByGroup(Resources::GROUP_TEACHERS);
        foreach($teachers as $id => $resource) {
            if (isset($resource['email']) && $resource['email'] == $email || isset($resource['data2']) && $resource['data2'] == $email) {
                return $resource['id'];
            }
        }
        return false;
    }

    public function findCustomerResourceIdByEmail($email = null) {
        if (!$email) {
            $email = $this->getPersonEmail();
        }
        if (!$email) {
            return false;
        }

        $customers = $this->resources->getResourcesByGroup(Resources::GROUP_CUSTOMERS);
        foreach($customers as $id => $resource) {
            if (isset($resource['email']) && $resource['email'] == $email) {
                return $resource['id'];
            }
        }
        return false;
    }


    public function getSchoolId($event){
        if(isset($event[Resources::GROUP_EXTERNAL_LOCATIONS]) && count($event[Resources::GROUP_EXTERNAL_LOCATIONS]) > 0) {
            return 'External';
        } else {
            if(isset($event[Resources::GROUP_ROOMS_WINTERTHUR]) && count($event[Resources::GROUP_ROOMS_WINTERTHUR]) > 0) {
                return 'Winterthur';
            } else if (isset($event[Resources::GROUP_ROOMS_ZURICH]) && count($event[Resources::GROUP_ROOMS_ZURICH]) > 0){
                return 'Zurich';
            } else {
                return '';
            }
        }
    }

    public function isTeacher() {
        return isset($this->currentTeacherId);
    }

    public function isCustomer() {
        return isset($this->currentCustomerId);
    }

    public function getEventsGroupedByDate() {
        return $this->events->getEventsGroupedByDate();
    }
}

