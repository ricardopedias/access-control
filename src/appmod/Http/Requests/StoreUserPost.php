<?php
namespace Acl\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserPost extends FormRequest
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
        return [
            'name'     => 'required|max:100',
            'email'    => 'required|unique:users|max:150',
            'password' => 'required',
        ];
    }
}
