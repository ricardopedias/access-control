<?php
namespace Acl\Services;

use \Illuminate\Http\Request;

interface CrudFrontContract
{
    public function gridList(Request $request = null);

    public function gridTrash(Request $request = null);

    public function formCreate(Request $request = null);

    public function formEdit($id, Request $request = null);
}
