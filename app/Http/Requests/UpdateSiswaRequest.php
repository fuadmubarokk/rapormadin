<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSiswaRequest extends FormRequest
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
        $siswaId = $this->route('id');

        return [
            'nisn' => [
                'required',
                'string',
                'max:20',
                Rule::unique('siswa')->ignore($siswaId),
            ],
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'required|string|max:50',
            'alamat' => 'required|string',
            'nama_ayah' => 'required|string|max:255',
            'nama_ibu' => 'required|string|max:255',
            'pekerjaan_ayah' => 'required|string|max:100',
            'pekerjaan_ibu' => 'required|string|max:100',
            'no_hp_ortu' => 'required|string|max:15',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kelas_id' => 'required|exists:kelas,id',
            'diterima_tanggal' => 'required|date',
            'status_keluarga' => 'required|string|max:255',
        ];
    }
}