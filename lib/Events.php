<?php
namespace Vox\Scheduleit;

include_once dirname(__FILE__)."/../config.php";

class Events {
    private $api;
    private $events;

    private $resolved = false;

    public function __construct()
    {
        $this->api = new Api(USER_ID, USERNAME, PASSWORD);
    }

    public function loadEvents($owners, $from, $to = null) {
        $result = $this->api->findEvents($owners, $from, $to);
        if ($result !== false) {
            $this->events = $result;
            return $result;
        } else {
            // Nothing loaded
            return false;
        }
    }

    /**
     * @param Resources $resources
     */
    public function resolveResources($resources) {
        if ($this->events) {
            foreach ($this->events as &$event) {
                $owners = $event['owner'];
                foreach ($resources->getResourcesByGroup() as $groupId => $resourcesArray) {
                    if (!isset($event[$groupId])){
                        $event[$groupId] = array();
                    }

                    foreach ($resourcesArray as $resourceId => $resource) {
                        if (strpos($owners, ','.$resourceId.',') !== false) {
                           $event[$groupId][$resourceId] = $resource;
                        }

                    }
                }
            }
            $this->resolved = true;
        }
    }

    /**
     * @param array|int|string $resourceId which resources to check inside 'owner' field
     * @param Resources $resourcesReference
     * @param bool $returnGrouped when false retuns the flat list of resources, otherwise maps by group
     * @return array
     */
    public function getEventsContainingResources($resourceId, $resourcesReference, $returnGrouped = true) {
        if (!$this->resolved) {
            $this->resolveResources($resourcesReference);
        }

        $owners = !is_array($resourceId) ? ($resourceId ? array($resourceId) : array()) : $resourceId;

        $flat = array();
        $grouped = array();
        foreach ($owners as $owner) {
            $grouped[$owner] = array();
        }

        if (!$this->events) {
            return $returnGrouped ? $grouped : $flat;
        }

        if (!empty($owners)) {
            foreach ($owners as $owner){
                $groupId = $resourcesReference->getGroupOfResource($owner);
                foreach ($this->events as $event) {
                    if (isset($event[$groupId][$owner])) {
                        $grouped[$owner][] = $event;
                        $flat[] = $event;
                    }
                }
            }

            return $returnGrouped ? $grouped : $flat;
        } else {
            return array();
        }
    }

    public function getEventsGroupedByDate() {
        if (!$this->events) {
            return array();
        }

        $result = array();
        foreach ($this->events as $event) {
            $date = substr($event['date_start'], 0, 10);
            if (!isset($result[$date])) {
                $result[$date] = array();
            }
            $result[$date][$event['id']] = $event;
        }

        return $result;
    }

    public static function printTimes($event) {
        $from = new \DateTime($event['date_start']);
        $to = new \DateTime($event['date_end']);
        return $from->format('H:i') . ' - ' . $to->format('H:i');
    }

    public static function printLearningMode($event) {
        return self::resourceNamePrinter($event, Resources::GROUP_LEARNING_MODES);
    }

    public static function printLanguage($event) {
        return self::resourceNamePrinter($event, Resources::GROUP_LANGUAGES, true);
    }

    public static function printCourse($event) {
        return self::resourceNamePrinter($event, Resources::GROUP_COURSES);
    }

    public static function printIntensity($event) {
        return self::resourceNamePrinter($event, Resources::GROUP_INTENSITIES);
    }

    public static function printTeacher($event) {
        return self::resourceNamePrinter($event, Resources::GROUP_TEACHERS);
    }

    public static function printStudents($event) {
        return self::resourceNamePrinter($event, Resources::GROUP_CUSTOMERS);
    }

    public static function printRoom($event, $schoolGroupId) {
        return self::resourceNamePrinter($event, $schoolGroupId);
    }

    public static function printSchool($event) {
        if(isset($event[Resources::GROUP_EXTERNAL_LOCATIONS]) && count($event[Resources::GROUP_EXTERNAL_LOCATIONS]) > 0) {
            return self::resourceNamePrinter($event, Resources::GROUP_EXTERNAL_LOCATIONS);
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

    public static function printRoomInSchool($event) {
        if(isset($event[Resources::GROUP_EXTERNAL_LOCATIONS]) && count($event[Resources::GROUP_EXTERNAL_LOCATIONS]) > 0) {
            return self::resourceNamePrinter($event, Resources::GROUP_EXTERNAL_LOCATIONS);
        } else {
            if(isset($event[Resources::GROUP_ROOMS_WINTERTHUR]) && count($event[Resources::GROUP_ROOMS_WINTERTHUR]) > 0) {
                return self::resourceNamePrinter($event, Resources::GROUP_ROOMS_WINTERTHUR) . ', Winterthur';
            } else if (isset($event[Resources::GROUP_ROOMS_ZURICH]) && count($event[Resources::GROUP_ROOMS_ZURICH]) > 0){
                return self::resourceNamePrinter($event, Resources::GROUP_ROOMS_ZURICH) . ', Zurich';
            } else {
                return 'Room not assigned';
            }
        }
    }

    private static function resourceNamePrinter($event, $groupId, $onlyFirst = false, $separator = ', ') {
        if (isset($event[$groupId])) {
            $result = array();
            foreach($event[$groupId] as $resource) {
                $name = $resource['name'];
                if ($groupId == Resources::GROUP_TEACHERS) {
                    $namePieces = explode(" ", $name);
                    $newNamePieces = array();
                    $usedPiecesCount = 0;
                    foreach ($namePieces as $index => $nameValue) {
                        if (trim($nameValue) != ""){
                            $newNamePieces[] = trim($nameValue);
                            $usedPiecesCount++;
                        }
                        if ($usedPiecesCount >= 2) {
                            break;
                        }
                    }
                    $name = implode(' ', $newNamePieces);
                }

                if ($groupId == Resources::GROUP_CUSTOMERS) {
                    $namePieces = explode(" ", $name);
                    $newNamePieces = array();
                    $usedPiecesCount = 0;
                    foreach ($namePieces as $index => $nameValue) {
                        if (trim($nameValue) != ""){
                            if ($usedPiecesCount >= 1) {
                                $newNamePieces[] = substr(trim($nameValue), 0, 1). '.';
                            } else {
                                $newNamePieces[] = trim($nameValue);
                            }
                            $usedPiecesCount++;
                        }
                    }
                    $name = implode(' ', $newNamePieces);
                }

                $result[] = $name;
                if ($onlyFirst) {
                    break;
                }
            }
            return implode($separator, $result);
        } else {
            return '';
        }
    }
}

