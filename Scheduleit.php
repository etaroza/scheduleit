<?php


class Scheduleit extends Helpers
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

    protected $resourceList = array();
    protected $eventList = array();

    public function __construct($userId, $username, $password)
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->password = $password;
        $this->resourceList = $this->getResourceList();
        $this->eventList = $this->eventList();
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

        $explodeOwnerIds = [];

        foreach ($this->eventList["owners"] as $key => $value) {
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

        $customerTemp = $this->shortenNames($customerTemp);

        $language = $this->implode($languageTemp);
        $course = $this->implode($courseTemp);
        $intensity = $this->implode($intensityTemp);
        $mode = $this->implode($modeTemp);
        $zurichRoom = $this->implode($zurichRoomTemp);
        $winterthurRoom = $this->implode($winterthurRoomTemp);
        $customer = $this->implode($customerTemp);

        $groupMessagesByDate = $this->groupMessagesByDate($this->eventList["date"], $language, $course,
            $intensity, $mode, $zurichRoom, $winterthurRoom, $customer);

        return $groupMessagesByDate;
    }

    function reorganizeEventMonths($groupMessagesByDate)
    {
        $reorganizedEventMonths = array();

        foreach ($groupMessagesByDate as $array) {
            $reorganizedEventMonths[] = date("F", strtotime($array[0][0]));
        }

        return $reorganizedEventMonths;
    }

    function groupMessagesByDate($dates, $language, $course,
                                 $intensity, $mode, $zurichRoom, $winterthurRoom, $customer)
    {
        $uniqueDates = array_unique($dates);

        $groupedDates = array();


        $i = 0;
        $j = 0;

        foreach ($uniqueDates as $uniqueDate) {

            foreach ($dates as $key => $date) {

                if($uniqueDate == $date) {

                    $groupedDates[$i][$j][] = $date;
                    $groupedDates[$i][$j][] = $this->eventList["startEndTime"][$key];
                    $groupedDates[$i][$j][] = $language[$key];
                    $groupedDates[$i][$j][] = $course[$key];
                    $groupedDates[$i][$j][] = $intensity[$key];
                    $groupedDates[$i][$j][] = $mode[$key];
                    $groupedDates[$i][$j][] = $this->eventList["title"][$key];

                    if (count($zurichRoom) == 0) {
                        $groupedDates[$i][$j][] = "Winterthur";

                        !isset($winterthurRoom[$key]) || trim($winterthurRoom[$key])=== "" ?
                            $groupedDates[$i][$j][] = "Room not assigned" : $groupedDates[$i][$j][] = $winterthurRoom[$key];
                    } else {
                        $groupedDates[$i][$j][] = "Zurich";

                        !isset($zurichRoom[$key]) || trim($zurichRoom[$key])=== "" ?
                            $groupedDates[$i][$j][] = "Room not assigned" : $groupedDates[$i][$j][] = $zurichRoom[$key];
                    };

                    $groupedDates[$i][$j][] = $customer[$key];

                    $j++;
                }

            }

            $i++;
            $j = 0;
        }

        return $groupedDates;
    }

    function generateDateRange()
    {
        $currentDate = new DateTime();
        $startDate = new DateTime();
        $endDate = new DateTime();

        $yearFromTest = date("o", strtotime( $currentDate->format("Y-m-d") ));
        $monthFromTest = date("m", strtotime( $currentDate->format("Y-m-d") ));
        $dayFromTest = date("d", strtotime( $currentDate->format("Y-m-d") ));

        $startDateMonth = (int) $monthFromTest - 2;
        $endDateMonth = (int) $monthFromTest + 6;

        $startDate = $startDate->setDate($yearFromTest, $startDateMonth, $dayFromTest)->format('Y-m-d');
        $endDate = $endDate->setDate($yearFromTest, $endDateMonth, $dayFromTest)->format('Y-m-d');

        $dateRange = "date_range_from=" . $startDate . "&date_range_to=" . $endDate;

        return $dateRange;
    }

    function eventList()
    {
        if (isset($this->eventList) && count($this->eventList) > 0) {

            return $this->eventList;

        } else {

            $teacherId = $this->getSingleTeacherData()["id"];
            $dateRange = $this->generateDateRange();

            try {
                $eventDataEndpoint = "events?search_owner=$teacherId&fields=id,title,date_start,date_end,owner&$dateRange&limit=$this->limit&sort=date_start";
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

            /**
             * remove commas at the beginning and end of owner string
             * (,1039,810,205,819,) to (1039,810,205,819)
             */
            foreach ($eventOwners as $key => $value) {
                if (($value[0] == ",") && (substr($value, -1) == ",")) {
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
    }

    function getSingleTeacherData()
    {
        $teacherTypedInEmail = $this->formInputValidation($_GET["email"]);

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

    function getResourceList()
    {
        if (isset($this->resourceList) && count($this->resourceList) > 0) {

            return $this->resourceList;

        } else {

            try {
                $resourceListEndpoint = "resources?fields=id,name,email,owner&limit=$this->limit";
                $resourceListResponse = $this->apiCall($resourceListEndpoint);

                /**
                 * if too many requests, return status code
                 */
                if ($resourceListResponse["status_code"] === "429") {
                    return $resourceListResponse["status_code"];
                }

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

