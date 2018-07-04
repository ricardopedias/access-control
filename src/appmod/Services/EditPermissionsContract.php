<?php
namespace Laracl\Services;

use \Illuminate\Http\Request;

interface EditPermissionsContract
{
    public function formEdit(string $view, $id, Request $request = null);

    public function dataUpdate(array $data, int $id);

    public function getStructure(int $id, bool $allows_null = false);
}
