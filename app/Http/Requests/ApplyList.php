<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class ApplyList extends FormRequest
{
    public function rules()
    {
        return [
            'page'     => 'required|int',
            'pageSize' => 'required|int|max:1000',
        ];
    }

    public function attributes()
    {
        return [
            'page'     => '页',
            'pageSize' => '每页大小',
        ];
    }
}
