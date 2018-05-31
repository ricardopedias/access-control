<?php

namespace Laracl\Tests\Libs;

use Laracl\Http\Controllers\IPermissionsController;

class IPermissionsControllerAccessor extends IPermissionsController
{
    public function accessGetRoles()
    {
        return $this->roles;
    }

    public function accessGetRolesStructure()
    {
        return $this->getRolesStructure();
    }

    public function accessPopulateStructure($collection)
    {
        return $this->populateStructure($collection);
    }

    public function accessGetSyncedRole($slug)
    {
        return $this->getSyncedRole($slug);
    }
}
