<?php
/**
 * Created by PhpStorm.
 * User: featf
 * Date: 2018-05-07
 * Time: 16:00
 */

class Reports extends Scheduleit
{
    function countHoursTillLastDayOfPrevMonth()
    {
        $eventList = $this->eventList();
        $currentDate = new DateTime();

        $workedHours = 0;
        $workedMinutes = 0;

        $lastDayOfPrevMonth = $currentDate->modify("last day of previous month")->format("Y-m-d H:i");

        foreach ($eventList["dateStart"] as $key => $date) {

            if ($lastDayOfPrevMonth > $date) {
                $dateStart = new DateTime($eventList["dateStart"][$key]);
                $dateEnd = new DateTime($eventList["dateEnd"][$key]);

                $workedHours += $dateEnd->diff($dateStart)->h;
                $workedMinutes += $dateEnd->diff($dateStart)->i;
            }
        }

        $workedHours += round(($workedMinutes / 60), 2);

        return $workedHours;
    }

    function countHoursTillToday()
    {
        $eventList = $this->eventList();
        $currentDate = new DateTime();

        $workedHours = 0;
        $workedMinutes = 0;

        foreach ($eventList["dateEnd"] as $key => $date) {

            if ( $currentDate > new DateTime($eventList["dateEnd"][$key]) ) {

                $dateStart = new DateTime($eventList["dateStart"][$key]);
                $dateEnd = new DateTime($eventList["dateEnd"][$key]);

                $workedHours += $dateEnd->diff($dateStart)->h;
                $workedMinutes += $dateEnd->diff($dateStart)->i;
            }

        }

        $workedHours += round(($workedMinutes / 60), 2);

        return $workedHours;
    }
}
