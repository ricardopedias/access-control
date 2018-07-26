<?php
namespace Acl\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupPost extends FormRequest
{
    /**
     * Determina se o usuário tem autorização para fazer esta requisição.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Regras de validação aplicadas na requisição.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('group');

        return [
            'name' => "required|max:100|unique:acl_groups,name,{$id}"
        ];
    }
}
