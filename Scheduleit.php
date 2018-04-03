<?php

class Scheduleit
{
    private $limit = "1000";
    private $teacherGroupId = "10";
    private $languageGroupId = "822";
    private $courseGroupId = "809";
    private $intensityGroupId = "200";
    private $modeGroupId = "818";
    private $zurichRoomGroupId = "193";
    private $winterthurRoomGroupId = "282";
    private $customerGroupId = "868";

    private $userId;
    private $username;
    private $password;
    private $resourceList = [];

    public function __construct($userId, $username, $password)
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->password = $password;
        $this->resourceList = $this->getResourceList();
    }

    function prepareTeacherEventsData()
    {
        $languageList = $this->resourceList["languages"];
        $courseList = $this->resourceList["courses"];
        $intensityList = $this->resourceList["intensities"];
        $modeList = $this->resourceList["modes"];
        $zurichRoomList = $this->resourceList["zurichRooms"];
        $winterthurRoomList = $this->resourceList["winterthurRooms"];
        $customerList = $this->resourceList["customers"];

        $eventList = $this->eventList();

        $explodeOwnerIds = [];

        foreach ($eventList["owners"] as $key => $value) {
            array_push($explodeOwnerIds, explode(',', $value));
        }
        unset($value);

        $languageTemp = $this->searchInArray($explodeOwnerIds, $languageList);
        $courseTemp = $this->searchInArray($explodeOwnerIds, $courseList);
        $intensityTemp = $this->searchInArray($explodeOwnerIds, $intensityList);
        $modeTemp = $this->searchInArray($explodeOwnerIds, $modeList);
        $zurichRoomTemp = $this->searchInArray($explodeOwnerIds, $zurichRoomList);
        $winterthurRoomTemp = $this->searchInArray($explodeOwnerIds, $winterthurRoomList);
        $customerTemp = $this->searchInArray($explodeOwnerIds, $customerList);

        $language = $this->implode($languageTemp);
        $course = $this->implode($courseTemp);
        $intensity = $this->implode($intensityTemp);
        $mode = $this->implode($modeTemp);
        $zurichRoom = $this->implode($zurichRoomTemp);
        $winterthurRoom = $this->implode($winterthurRoomTemp);
        $customer = $this->implode($customerTemp);

        $messages = [];

        for ($i = 0; $i < $eventList["amountOfEvents"]; $i++) {

            if (count($zurichRoom) == 0) {
                $message =
                    "<b>" . $eventList["date"][$i] . " " . $eventList["startEndTime"][$i] . " " . $language[$i] . " " .
                    $course[$i] . " " . $intensity[$i] . "</b> | Winterthur - " . $mode[$i] . " - " .
                    $eventList["title"][$i] . "<br><b>" . $winterthurRoom[$i] . "</b>: " . $customer[$i] . "<br>";
            } else {
                $message =
                    "<b>" . $eventList["date"][$i] . " " .$eventList["startEndTime"][$i] . " " . $language[$i] . " " .
                    $course[$i] . " " . $intensity[$i] . "</b> | Zurich - " . $mode[$i] . " - " .
                    $eventList["title"][$i] . "<br><b>" . $zurichRoom[$i] . "</b>: " . $customer[$i] . "<br>";
            }

            array_push($messages, $message);

        }

//        $messagesWithDates = array(
//            "message" => $messages,
//            "month" => $eventList["month"]
//        );

//        return $messagesWithDates;
        return $messages;
    }

    function implode($resource)
    {
        foreach ($resource as $key => $value) {
            if (count($value) > 1) {
                $implode = implode(", ", $value);
                $resource[$key] = $implode;
            } else {
                //reduce array by one dimension
                $resource[$key] = $value[0];
            }
        }

        return $resource;
    }

    function searchInArray($explodeOwnerIds, $resourceList)
    {
        $result = array();
        $key = 0;

        for ($i = 0; $i < count($explodeOwnerIds); $i++) {

            for ($j = 0; $j < count($explodeOwnerIds[$i]); $j++) {

                $searchForResource = array_search($explodeOwnerIds[$i][$j], array_column($resourceList, "id"));

                if ($searchForResource !== false) {
                    $result[$i][$key] = $resourceList[$searchForResource]["name"];
                    $key++;
                }

            }
            $key = 0;
        }
        return $result;
    }

    function eventList()
    {
        $teacherId = $this->getSingleTeacherData()["id"];

        try{
            $eventDataEndpoint = "events?search_owner=$teacherId&fields=id,title,date_start,date_end,owner&limit=$this->limit&sort=date_start";
            $eventDataResponse = $this->apiCall($eventDataEndpoint);

            $eventData = $eventDataResponse["_embedded"]["events"]["_embedded"]["data"];
        } catch (Exception $e) {
            echo "Error while performing API call: " . $e;
            die;
        }

        $eventId = [];
        $eventTitle = [];
        $eventStartEndTime = [];
        $eventDates = [];
        $eventDateEnd = [];
        $eventMonths = [];
        $eventOwners = [];

        foreach ($eventData as $value) {
            array_push($eventId, $value["id"]);
            array_push($eventTitle, $value["title"]);

            $startTime = new DateTime($value["date_start"]);
            $endTime = new DateTime($value["date_end"]);
            $startEndTime = $startTime->format("H:i") . " - " .
                $endTime->format("H:i");
            array_push($eventStartEndTime, $startEndTime);

            $date = $startTime->format("o-m-d");
            array_push($eventDates, $date);

            $month = $startTime->format("F");
            array_push($eventMonths, $month);

            array_push($eventDateEnd, $value["date_end"]);

            array_push($eventOwners, $value["owner"]);
        }
        unset($value);

        //remove commas at the beginning and end of owner string
        //(,1039,810,205,819,) to (1039,810,205,819)
        foreach ($eventOwners as $key => $value){
            if ( ($value[0] == ",") && (substr($value, -1) == ",") ) {
                $removeCommaAtStartAndEnd = substr($value, 1, -1);
                $eventOwners[$key] = $removeCommaAtStartAndEnd;
            }
        }
        unset($value);

        $uniqueMonths = array_unique($eventMonths, SORT_REGULAR);

        $eventDetails = array(
            "amountOfEvents" => count($eventData),
            "id" => $eventId,
            "title" => $eventTitle,
            "dateEnd" => $eventDateEnd,
            "startEndTime" => $eventStartEndTime,
            "date" => $eventDates,
            "month" => $eventMonths,
            "uniqueMonth" => $uniqueMonths,
            "owners" => $eventOwners
        );

        return $eventDetails;
    }

    function getSingleTeacherData()
    {
        $teacherTypedInEmail = $_GET["email"];

        $resources = $this->resourceList["teachers"];

        $teacherKeyInList = array_search($teacherTypedInEmail, array_column($resources, "email"));

        if ($teacherKeyInList != null) {
            $teacherId = $resources[$teacherKeyInList]["id"];
            $teacherName = $resources[$teacherKeyInList]["name"];
            $teacherEmail = $resources[$teacherKeyInList]["email"];

            $singleTeacherData = array(
                "id" => $teacherId,
                "name" => $teacherName,
                "email" => $teacherEmail
            );

            return $singleTeacherData;
        } else {
            return null;
        }
    }

    function populateResourceArrays($valueOwner, $groupId, $resourceArray, $value)
    {
        if (substr($valueOwner, 1, -1) === $groupId) {
            if ($value["email"] == "") {
                $tempArray = array(
                    "id" => $value["id"],
                    "name" => $value["name"]
                );
            } else {
                $tempArray = array(
                    "id" => $value["id"],
                    "name" => $value["name"],
                    "email" => $value["email"]
                );
            }
            array_push($resourceArray, $tempArray);
        }

        return $resourceArray;
    }

    function groupList()
    {
        try{
            $groupListEndpoint = "groups?fields=id,name";
            $groupListResponse = $this->apiCall($groupListEndpoint);

            $groupListData = $groupListResponse["_embedded"]["groups"]["_embedded"]["data"];
        } catch (Exception $e) {
            echo "Error while performing API call: " . $e;
            die;
        }

        foreach ($groupListData as $value) {
            array_push($resourceId, $value["id"]);
            array_push($resourceName, $value["name"]);
            array_push($resourceEmail, $value["email"]);
            array_push($resourceOwner, $value["owner"]);
        }
        unset($value);

        $groupId = [];
        $groupName = [];

        $groupData = array(
            "id" => $groupId,
            "name" => $groupName
        );

        return $groupData;
    }

    function getResourceList()
    {
        try{
            $resourceListEndpoint = "resources?fields=id,name,email,owner&limit=$this->limit";
            $resourceListResponse = $this->apiCall($resourceListEndpoint);

            $resourceListData = $resourceListResponse["_embedded"]["resources"]["_embedded"]["data"];
        } catch (Exception $e) {
            echo "Error while performing API call: " . $e;
            die;
        }

        $teachers = [];
        $languages = [];
        $courses = [];
        $intensities = [];
        $modes = [];
        $customers = [];
        $zurichRooms = [];
        $winterthurRooms = [];

        foreach ($resourceListData as $value) {
            $teachers = $this->populateResourceArrays($value["owner"], $this->teacherGroupId, $teachers, $value);
            $languages = $this->populateResourceArrays($value["owner"], $this->languageGroupId, $languages, $value);
            $courses = $this->populateResourceArrays($value["owner"], $this->courseGroupId, $courses, $value);
            $intensities = $this->populateResourceArrays($value["owner"], $this->intensityGroupId, $intensities, $value);
            $modes = $this->populateResourceArrays($value["owner"], $this->modeGroupId, $modes, $value);
            $customers = $this->populateResourceArrays($value["owner"], $this->customerGroupId, $customers, $value);
            $zurichRooms = $this->populateResourceArrays($value["owner"], $this->zurichRoomGroupId, $zurichRooms, $value);
            $winterthurRooms = $this->populateResourceArrays($value["owner"], $this->winterthurRoomGroupId, $winterthurRooms, $value);
        }
        unset($value);

        $resourceData = array(
            "teachers" => $teachers,
            "languages" => $languages,
            "courses" => $courses,
            "intensities" => $intensities,
            "modes" => $modes,
            "customers" => $customers,
            "zurichRooms" => $zurichRooms,
            "winterthurRooms" => $winterthurRooms
        );

        return $resourceData;
    }

    private function apiCall($endpoint)
    {
        $authCredentials = $this->userId . "_" . $this->username . ":" . $this->password;

        $url = "https://www.scheduleit.co.uk/api/$endpoint";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $authCredentials);
        curl_setopt($curl, CURLOPT_URL, $url);

        $data = json_decode(curl_exec($curl), true);

        return $data;
    }
}

