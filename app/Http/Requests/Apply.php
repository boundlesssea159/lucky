<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class Apply extends FormRequest
{
    public function rules()
    {
        return [
            'phone' => 'required|int',
            'text'  => 'required|string|max:500',
        ];
    }

    public function attributes()
    {
        return [
            'phone' => '手机号',
            'text'  => '文本内容',
        ];
    }
}
