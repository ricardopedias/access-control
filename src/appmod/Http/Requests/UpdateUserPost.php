<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

namespace Acl\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserPost extends FormRequest
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
        $id = $this->route('user');

        return [
            'name'  => 'required|max:100',
            'email' => "required|unique:users,email,{$id}|max:150"
        ];
    }
}
