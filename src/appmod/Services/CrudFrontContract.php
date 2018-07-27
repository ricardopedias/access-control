<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

namespace Acl\Services;

use \Illuminate\Http\Request;

interface CrudFrontContract
{
    public function gridList(Request $request = null);

    public function gridTrash(Request $request = null);

    public function formCreate(Request $request = null);

    public function formEdit($id, Request $request = null);
}
