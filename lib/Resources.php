<?php
namespace Vox\Scheduleit;

include_once dirname(__FILE__)."/../config.php";

class Resources {
    const GROUP_ROOMS_ZURICH = 193;
    const GROUP_ROOMS_WINTERTHUR = 282;
    const GROUP_EXTERNAL_LOCATIONS = 806;

    const GROUP_TEACHERS = 10;
    const GROUP_LANGUAGES = 822;
    const GROUP_COURSES = 809;
    const GROUP_INTENSITIES = 200;
    const GROUP_LEARNING_MODES = 818;
    const GROUP_CUSTOMERS = 868;

    private $api;
    private $resources;

    private $resourceGroupCache;

    public function __construct()
    {
        $this->api = new Api(USER_ID, USERNAME, PASSWORD);
    }

    public function loadResources($groups) {
        $result = $this->api->getResources($groups);
        if ($result) {
            $this->resources = $result;
            return $result;
        } else {
            // Nothing loaded
            return false;
        }
    }

    public function getResource($groupId, $resourceId) {
        $allResources = $this->getResourcesByGroup();
        if(!isset($allResources[$groupId][$resourceId])) {
            return '';
        } else {
            return $allResources[$groupId][$resourceId];
        }
    }

    public function getResourcesByGroup($groupId = null) {
        if (!$this->resources) {
            return array();
        }

        if ($groupId) {
            return isset($this->resources[$groupId]) ? $this->resources[$groupId] : array();
        } else {
            return $this->resources;
        }
    }

    public function getGroupOfResource($resourceId){
        if (isset($this->resourceGroupCache[$resourceId])) {
            return $this->resourceGroupCache[$resourceId];
        }

        if (!$this->resources) {
            return false;
        }

        foreach ($this->getResourcesByGroup() as $groupId => $resources) {
            $idsOfResources = array_keys($resources);
            if (in_array($resourceId, $idsOfResources)) {
                $this->resourceGroupCache[$resourceId] = $groupId;
                break;
            }
        }

        if (isset($this->resourceGroupCache[$resourceId])) {
            return $this->resourceGroupCache[$resourceId];
        } else {
            // Nothing found
            return false;
        }
    }
}

