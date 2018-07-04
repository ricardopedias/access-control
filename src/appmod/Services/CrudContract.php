<?php
namespace Laracl\Services;

use \Illuminate\Http\Request;

interface CrudContract
{
    public function gridList(Request $request, string $view);

    public function gridTrash(Request $request, string $view);

    public function formCreate(Request $request, string $view);

    public function formEdit(Request $request, string $view, $id);

    public function dataInsert(Request $request);

    public function dataUpdate(Request $request, int $id = null);

    public function dataDelete(Request $request, int $id = null);
}
