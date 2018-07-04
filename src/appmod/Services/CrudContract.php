<?php
namespace Laracl\Services;

use \Illuminate\Http\Request;

interface CrudContract
{
    public function gridList(string $view, Request $request = null);

    public function gridTrash(string $view, Request $request = null);

    public function formCreate(string $view, Request $request = null);

    public function formEdit(string $view, $id, Request $request = null);

    public function dataInsert(array $data);

    public function dataUpdate(array $data, int $id);

    public function dataDelete(array $data, int $id = null);
}
