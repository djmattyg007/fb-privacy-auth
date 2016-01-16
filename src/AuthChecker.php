<?php

namespace MattyG\FBPrivacyAuth;

class AuthChecker
{
    /**
     * @var array
     */
    protected $groups;

    /**
     * @var array
     */
    protected $resources;

    /**
     * @param array $groups
     */
    public function __construct(array $groups, array $resources)
    {
        $this->groups = $groups;
        $this->resources = $resources;
    }

    /**
     * @param int $resourceId
     * @param string $username
     * @return bool
     */
    public function check($resourceId, $username)
    {
        $authConfig = $this->resources[$resourceId];
        if (in_array($username, $authConfig["deny"]["users"])) {
            return false;
        } elseif (in_array($username, $authConfig["allow"]["users"])) {
            return true;
        }
        foreach ($authConfig["deny"]["groups"] as $group) {
            if (in_array($username, $this->groups[$group])) {
                return false;
            }
        }
        foreach ($authConfig["allow"]["groups"] as $group) {
            if (in_array($username, $this->groups[$group])) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $username
     * @return array
     */
    public function getAllowedResourceIds($username)
    {
        $allResourceIds = array_keys($this->resources);
        $check = array($this, "check");
        return array_values(array_filter($allResourceIds, function($resourceId) use ($check, $username) {
            return $check($resourceId, $username);
        }));
    }
}
