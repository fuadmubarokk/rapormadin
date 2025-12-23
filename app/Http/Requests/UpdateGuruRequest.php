<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGuruRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $guruId = $this->route('id'); 

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($guruId),
            ],
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
            'nip' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users')->ignore($guruId),
            ],
            'no_hp' => 'nullable|string|max:15',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'ttd_wali_kelas' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'sometimes|required|string|min:8|confirmed',
        ];
    }
}