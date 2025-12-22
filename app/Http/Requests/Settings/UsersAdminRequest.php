<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UsersAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Opsional: mapping "nama" -> "name" + trim
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'  => $this->input('name', $this->input('nama')),
            'email' => trim((string)$this->input('email')),
        ]);
    }

    public function rules(): array
    {
        // ✅ Cara benar ambil nama tabel
        $table = (new User)->getTable(); // "users"

        // Ambil id untuk update (bisa numeric id atau model binding)
        $id = $this->route('id') ?? ($this->route('user')->id ?? null);

        if ($this->isMethod('post')) {
            // CREATE
            return [
                'name'     => ['required','string','max:255'],
                'email'    => ['required','email:rfc,dns','max:190', Rule::unique($table, 'email')],
                'password' => ['required','string','min:8'],
                // kalau form kirim role:
                'role'     => ['nullable','in:admin,pokdarwis'],
            ];
        }

        // UPDATE (PUT/PATCH)
        return [
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email:rfc,dns','max:190', Rule::unique($table, 'email')->ignore($id)],
            'password' => ['nullable','string','min:8'], // boleh kosong saat update
            'role'     => ['nullable','in:admin,pokdarwis'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Nama pengguna harus diisi.',
            'name.string'    => 'Nama pengguna harus berupa string.',
            'name.max'       => 'Nama pengguna maksimal 255 karakter.',
            'email.required' => 'Email pengguna harus diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah terdaftar.',
            'password.required' => 'Password harus diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
            'role.in'           => 'Role harus admin atau pokdarwis.',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        \Alert::error('Gagal', 'Data user tidak berhasil diproses. Silahkan cek kembali data yang diisi.')->persistent();
        session()->flash('swal', false);
        parent::failedValidation($validator);
    }
}
