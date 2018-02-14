<?php 

namespace Laracl;

/**
 * ...
 */
class Accessor
{
    protected $log = [];

    public function setCurrentPermissions($role, $permission, $granted)
    {
        $this->log[] = [
            'role'       => $role,
            'permission' => $permission,
            'granted'    => $granted,
            ];
    }

    public function getCurrentPermissions()
    {
        return $this->log;
    }
}
