<?php
namespace Acl\Services;

use \Illuminate\Http\Request;

interface CrudBackContract
{
    public function dataInsert(array $data);

    public function dataUpdate(array $data, int $id);

    public function dataDelete(array $data, int $id = null);

    public function dataRestore(array $data, int $id = null);
}
