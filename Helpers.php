<?php
/**
 * Created by PhpStorm.
 * User: featf
 * Date: 2018-04-24
 * Time: 01:18
 */

class Helpers
{
    function formInputValueChecker($input)
    {
        if (isset($input)) {
            return $input;
        } else {
            return "";
        }
    }

    function formInputValidation($email)
    {
        $email = trim($email);
        $email = stripslashes($email);
        $email = htmlspecialchars($email);
        return $email;
    }

    function implode($resource)
    {
        foreach ($resource as $key => $value) {
            if (count($value) > 0) {
                $implode = implode(", ", $value);
                $resource[$key] = $implode;
            } else {
                $resource[$key] = '';
            }
        }

        return $resource;
    }

    function searchInArray($explodeOwnerIds, $resourceList)
    {
        $result = array();

        for ($i = 0; $i < count($explodeOwnerIds); $i++) {
            $result[] = array();
            for ($j = 0; $j < count($explodeOwnerIds[$i]); $j++) {

                $searchForResource = array_search($explodeOwnerIds[$i][$j], array_column($resourceList, "id"));

                if ($searchForResource !== false) {
                    $result[$i][] = trim($resourceList[$searchForResource]["name"]);
                }

            }
        }

        return $result;
    }

    /**
     * Create a string (date + hour) for checking if event has passed
     */
    function eventEndingDateAndLastHour($events)
    {
        $dateAndLastHour = array();

        for ($i = 0; $i < count($events); $i++) {
            for ($j = 0; $j < count($events[$i]); $j++) {
                $endingHour = substr($events[$i][$j][1], strpos($events[$i][$j][1], "- ") + 1);
                $dateAndLastHour[$i][$j][] = $events[$i][$j][0] . " " . $endingHour;
            }
        }

        return $dateAndLastHour;
    }

    function removeEventsFromPast($keepNrOfMonths, $eventDetails) {
        $result = array();
        foreach($eventDetails['dateEnd'] as $key => $end) {
            $month = substr($end, 5, 2) + 0;
            $now = new \DateTime('now');
            $nowMonth = $now->format('m') + 0;
            if ($nowMonth - $keepNrOfMonths + 1 <= $month) {
                foreach ($eventDetails as $a => $infoArray) {
                    $result[$a][] = $eventDetails[$a][$key];
                }
            }
        }

        return $result;
    }

    function removeEventsFromFuture($keepNrOfMonths, $eventDetails) {
        $result = array();
        foreach($eventDetails['dateEnd'] as $key => $end) {
            $month = substr($end, 5, 2) + 0;
            $now = new \DateTime('now');
            $nowMonth = $now->format('m') + 0;
            if ($month >= $nowMonth + $keepNrOfMonths - 1) {
                foreach ($eventDetails as $a => $infoArray) {
                    $result[$a][] = $eventDetails[$a][$key];
                }
            }
        }

        return $result;
    }

    function removeEventsFromPastAndFuture($eventDetails) {
        $result = array();
        foreach($eventDetails['dateEnd'] as $key => $end) {
            $month = substr($end, 5, 2) + 0;
            $now = new \DateTime('now');
            $nowMonth = $now->format('m') + 0;
            if ($month == $nowMonth || $nowMonth - 1 == $month || $month == $nowMonth + 1) {
                foreach ($eventDetails as $a => $infoArray) {
                    $result[$a][] = $eventDetails[$a][$key];
                }
            }
        }

        return $result;
    }

    /**
     * Shorten name from Name Surename to Name S.
     */
    function shortenNames($customerTemp)
    {
        $temp = array();
        $customersWithShortSurnames = array();

        foreach ($customerTemp as $key => $value) {

            foreach ($value as $innerKey => $nameString) {

                $wordByWord = explode(" ", $nameString);
                $tempStr = "";

                foreach ($wordByWord as $name) {

                    if( ($name != reset($wordByWord)) &&
                        ctype_upper(mb_substr($name, 0, 1, 'utf-8')) ) {
                        $tempStr .= mb_substr($name, 0, 1, 'utf-8') . ". ";
                    } else {
                        $tempStr .= $name . " ";
                    }

                }

                $removedSpaceAtEnd = rtrim($tempStr);
                array_push($temp, $removedSpaceAtEnd);

            }

            $customersWithShortSurnames[$key] = array_replace($value, $temp);
            $temp = array();

        }

        return $customersWithShortSurnames;
    }
}
