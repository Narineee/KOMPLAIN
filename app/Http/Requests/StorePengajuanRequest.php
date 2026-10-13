<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePengajuanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && (bool) $this->user()->is_aktif;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kategori_id' => ['required', 'exists:kategori,id'],
            'judul' => ['required', 'string', 'max:200'],
            'isi_laporan' => ['required', 'string', 'min:10'],
            'saran_penyelesaian' => ['nullable', 'string', 'max:1000'],
            'is_anonim' => ['nullable', 'boolean'],
            'lampiran.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kategori_id.required' => 'Kategori laporan wajib dipilih.',
            'kategori_id.exists' => 'Kategori laporan tidak valid.',
            'judul.required' => 'Judul laporan wajib diisi.',
            'judul.max' => 'Judul laporan maksimal 255 karakter.',
            'isi_laporan.required' => 'Isi laporan wajib diisi.',
            'isi_laporan.min' => 'Isi laporan minimal 10 karakter.',
            'lampiran.*.mimes' => 'Format berkas lampiran harus berupa JPG, PNG, PDF, atau DOC/DOCX.',
            'lampiran.*.max' => 'Ukuran setiap berkas lampiran maksimal 5MB.',
        ];
    }
}
