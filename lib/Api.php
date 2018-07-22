<?php
namespace Vox\Scheduleit;

class Api {
    const STATUS_CODE_QUOTA_EXCEEDED = 429;

    private $userId;
    private $username;
    private $password;

    private $resourcesCache = array();

    public function __construct($userId, $username, $password)
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @param $groupId array or single id
     * @param bool $cache
     * @return array|bool false when error occured, otherwise array of resources mapped by group id
     */
    public function getResources($groupId, $cache = true) {
        $groups = !is_array($groupId) ? ($groupId ? array($groupId) : array()) : $groupId;

        $result = array();
        if ($cache) {
            $uncachedGroups = array();
            foreach($groups as $gid) {
                if(isset($this->resourcesCache[$gid])) {
                    $result[$gid] = $this->resourcesCache[$gid];
                } else {
                    $uncachedGroups[] = $gid;
                }
            }
            $groups = $uncachedGroups;
        }

        if(!empty($groups)) {
            // TODO: check pagination for "customers" resource group, as it may grow above 1k
            $resourceListEndpoint = "resources?fields=id,name,email,owner,data2&limit=1000&search_owner=".implode(',', $groups);
            $resourceListResponse = $this->apiCall($resourceListEndpoint);
            if ($resourceListResponse["status_code"] == self::STATUS_CODE_QUOTA_EXCEEDED) {
                return false;
            }

            $resourceListData = $resourceListResponse["_embedded"]["resources"]["_embedded"]["data"];
            foreach ($resourceListData as $resource) {
                foreach ($groups as $gid) {
                    $checkedResource = $this->checkResourceGroup($gid, $resource);
                    if ($checkedResource) {
                        if (!isset($this->resourcesCache[$gid])){
                            $this->resourcesCache[$gid] = array();
                        }
                        $this->resourcesCache[$gid][$resource['id']] = $resource;
                    }
                }
            }
        }

        foreach($groups as $gid) {
            if(isset($this->resourcesCache[$gid])) {
                $result[$gid] = $this->resourcesCache[$gid];
            }
        }

        return $result;
    }

    private function checkResourceGroup($groupId, &$resource){
        if (substr($resource['owner'], 1, -1) == $groupId) {
            if ($groupId == Resources::GROUP_LANGUAGES) {
                $resource['name'] = substr($resource['name'], 0, stripos($resource['name'], '('));
            }
            return $resource;
        } else {
            return false;
        }
    }

    public function findEvents($ownerResourceId, $dateFrom, $dateTo = null) {
        $owners = !is_array($ownerResourceId) ? ($ownerResourceId ? array($ownerResourceId) : array()) : $ownerResourceId;

        if (!$dateTo || $dateFrom == $dateTo) {
            $dateTo = new \DateTime($dateFrom);
            $dateTo->modify('+1 day');
            $dateTo = $dateTo->format('Y-m-d');
        }

        $result = array();

        if(!empty($owners)) {
            // TODO: check pagination for "customers" resource group, as it may grow above 1k
            $eventDataEndpoint = "events?search_owner=".implode(',', $owners)."&fields=id,title,date_start,date_end,owner&search_date_start={$dateFrom}&search_date_end={$dateTo}&limit=1000&sort=date_start";
            $eventDataResponse = $this->apiCall($eventDataEndpoint);

            if ($eventDataResponse["status_code"] == self::STATUS_CODE_QUOTA_EXCEEDED) {
                return false;
            }

            $result = $eventDataResponse["_embedded"]["events"]["_embedded"]["data"];
        }

        return $result;
    }

    protected function apiCall($endpoint)
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

