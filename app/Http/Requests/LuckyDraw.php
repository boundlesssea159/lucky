<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class LuckyDraw extends FormRequest
{
    public function rules()
    {
        return [
            'phone' => 'required|int',
        ];
    }

    public function attributes()
    {
        return [
            'phone' => '手机号',
        ];
    }
}
