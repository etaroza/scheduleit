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
                    $result[$i][$key] = trim($resourceList[$searchForResource]["name"]);
                    $key++;
                }

            }
            $key = 0;
        }

        return $result;
    }
}
