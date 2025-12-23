<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'        => 'Isian :attribute harus diterima.',
    'accepted_if'     => 'Isian :attribute harus diterima ketika :other adalah :value.',
    'active_url'      => ':attribute bukan URL yang valid.',
    'after'           => ':attribute harus tanggal setelah :date.',
    'after_or_equal'  => ':attribute harus tanggal setelah atau sama dengan :date.',
    'alpha'           => ':attribute hanya boleh berisi huruf.',
    'alpha_dash'      => ':attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
    'alpha_num'       => ':attribute hanya boleh berisi huruf dan angka.',
    'array'           => ':attribute harus berupa array.',
    'before'          => ':attribute harus tanggal sebelum :date.',
    'before_or_equal' => ':attribute harus tanggal sebelum atau sama dengan :date.',
    'between'         => [
        'array'   => ':attribute harus memiliki :min - :max item.',
        'file'    => ':attribute harus berukuran antara :min - :max kilobita.',
        'numeric' => ':attribute harus antara :min - :max.',
        'string'  => ':attribute harus antara :min - :max karakter.',
    ],
    'boolean'         => 'Isian :attribute harus benar atau salah.',
    'confirmed'       => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Sandi salah.',
    'date'            => ':attribute bukan tanggal yang valid.',
    'date_equals'     => ':attribute harus tanggal yang sama dengan :date.',
    'date_format'     => ':attribute tidak cocok dengan format :format.',
    'declined'        => 'Isian :attribute harus ditolak.',
    'declined_if'     => 'Isian :attribute harus ditolak ketika :other adalah :value.',
    'different'       => ':attribute dan :other harus berbeda.',
    'digits'          => ':attribute harus berisi :digits digit.',
    'digits_between'  => ':attribute harus antara :min dan :max digit.',
    'dimensions'      => ':attribute memiliki dimensi gambar yang tidak valid.',
    'distinct'        => ':attribute memiliki nilai yang duplikat.',
    'email'           => ':attribute harus berupa alamat email yang valid.',
    'ends_with'       => ':attribute harus diakhiri dengan salah satu dari: :values.',
    'enum'            => 'Isian :attribute tidak valid.',
    'exists'          => ':attribute yang dipilih tidak valid.',
    'file'            => ':attribute harus berupa file.',
    'filled'          => 'Isian :attribute harus memiliki nilai.',
    'gt'              => [
        'array'   => ':attribute harus memiliki lebih dari :value item.',
        'file'    => ':attribute harus lebih besar dari :value kilobita.',
        'numeric' => ':attribute harus lebih besar dari :value.',
        'string'  => ':attribute harus lebih dari :value karakter.',
    ],
    'gte'             => [
        'array'   => ':attribute harus memiliki :value item atau lebih.',
        'file'    => ':attribute harus lebih besar atau sama dengan :value kilobita.',
        'numeric' => ':attribute harus lebih besar atau sama dengan :value.',
        'string'  => ':attribute harus lebih besar atau sama dengan :value karakter.',
    ],
    'image'           => ':attribute harus berupa gambar.',
    'in'              => 'Isian :attribute tidak valid.',
    'in_array'        => 'Isian :attribute tidak ada di :other.',
    'integer'         => ':attribute harus berupa angka.',
    'ip'              => ':attribute harus berupa alamat IP yang valid.',
    'ipv4'            => ':attribute harus berupa alamat IPv4 yang valid.',
    'ipv6'            => ':attribute harus berupa alamat IPv6 yang valid.',
    'json'            => ':attribute harus berupa string JSON yang valid.',
    'lowercase'       => ':attribute harus berupa huruf kecil.',
    'lt'              => [
        'array'   => ':attribute harus memiliki kurang dari :value item.',
        'file'    => ':attribute harus kurang dari :value kilobita.',
        'numeric' => ':attribute harus kurang dari :value.',
        'string'  => ':attribute harus kurang dari :value karakter.',
    ],
    'lte'             => [
        'array'   => ':attribute harus memiliki :value item atau kurang.',
        'file'    => ':attribute harus kurang dari atau sama dengan :value kilobita.',
        'numeric' => ':attribute harus kurang dari atau sama dengan :value.',
        'string'  => ':attribute harus kurang dari atau sama dengan :value karakter.',
    ],
    'mac_address'     => ':attribute harus berupa alamat MAC yang valid.',
    'max'             => [
        'array'   => ':attribute tidak boleh lebih dari :max item.',
        'file'    => ':attribute tidak boleh lebih dari :max kilobita.',
        'numeric' => ':attribute tidak boleh lebih dari :max.',
        'string'  => ':attribute tidak boleh lebih dari :max karakter.',
    ],
    'mimes'           => ':attribute harus berupa file tipe: :values.',
    'mimetypes'       => ':attribute harus berupa file tipe: :values.',
    'min'             => [
        'array'   => ':attribute harus memiliki minimal :min item.',
        'file'    => ':attribute harus minimal :min kilobita.',
        'numeric' => ':attribute harus minimal :min.',
        'string'  => ':attribute harus minimal :min karakter.',
    ],
    'multiple_of'     => ':attribute harus kelipatan dari :value.',
    'not_in'          => 'Isian :attribute tidak valid.',
    'not_regex'       => 'Format :attribute tidak valid.',
    'numeric'         => ':attribute harus berupa angka.',
    'password'        => 'Sandi salah.',
    'present'         => 'Isian :attribute harus ada.',
    'prohibited'      => 'Isian :attribute dilarang.',
    'prohibited_if'    => 'Isian :attribute dilarang ketika :other adalah :value.',
    'prohibited_unless' => 'Isian :attribute dilarang kecuali :other ada di :values.',
    'prohibits'       => 'Isian :attribute melarang :other ada.',
    'regex'           => 'Format :attribute tidak valid.',
    'required'        => 'Isian :attribute wajib diisi.',
    'required_array_keys' => 'Isian :attribute wajib memiliki entri untuk: :values.',
    'required_if'     => 'Isian :attribute wajib diisi ketika :other adalah :value.',
    'required_unless' => 'Isian :attribute wajib diisi kecuali :other ada di :values.',
    'required_with'   => 'Isian :attribute wajib diisi ketika :values ada.',
    'required_with_all' => 'Isian :attribute wajib diisi ketika :values ada.',
    'required_without' => 'Isian :attribute wajib diisi ketika :values tidak ada.',
    'required_without_all' => 'Isian :attribute wajib diisi ketika tidak ada dari :values yang ada.',
    'same'            => ':attribute dan :other harus sama.',
    'size'            => [
        'array'   => ':attribute harus memiliki :size item.',
        'file'    => ':attribute harus berukuran :size kilobita.',
        'numeric' => ':attribute harus berukuran :size.',
        'string'  => ':attribute harus berukuran :size karakter.',
    ],
    'starts_with'     => ':attribute harus diawali dengan salah satu dari: :values.',
    'string'          => ':attribute harus berupa string.',
    'timezone'        => ':attribute harus berupa zona waktu yang valid.',
    'unique'          => ':attribute sudah ada.',
    'uploaded'        => ':attribute gagal diunggah.',
    'uppercase'       => ':attribute harus berupa huruf besar.',
    'url'             => ':attribute bukan URL yang valid.',
    'uuid'            => ':attribute harus UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];