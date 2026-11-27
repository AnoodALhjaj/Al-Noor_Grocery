<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
//first layer validate the required fileds and the type of data required 
    public function rules(): array
    {
        return [
            'customer'      => ['required', 'string', 'max:255'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.name'  => ['required', 'string', 'max:255'],
            'items.*.qty'   => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer'             =>'يجب اضافة اسم العميل',
            'items.*.name'    => 'يجب اضافة اسم المنتج او رقمه المميز',
            'items.required'            => 'يجب أن تحتوي الفاتورة على صنف واحد على الأقل.',
            'items.min'                 => 'يجب أن تحتوي الفاتورة على صنف واحد على الأقل.',
            'items.*.qty.min'      => 'الكمية يجب أن تكون 1 على الأقل.',
            'items.*.qty.required'      => 'الكمية يجب أأن لا تكون فارغة.',
             'items.*.price.required'    => 'سعر الصنف يجب أن لا يكون فارغ.',
            'items.*.price.min'    => 'سعر الصنف يجب أن يكون أكبر من صفر.',
        ];
    }
}
?>