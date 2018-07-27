<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

namespace Acl\Services;

use \Illuminate\Http\Request;

interface CrudBackContract
{
    public function dataInsert(array $data);

    public function dataUpdate($id, array $data);

    public function dataDelete($id, array $data  = null);

    public function dataRestore($id, array $data = null);
}
