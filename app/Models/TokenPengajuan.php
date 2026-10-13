<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pengajuan_id', 'token_hash', 'terakhir_digunakan'])]
#[Hidden(['token_hash'])]
class TokenPengajuan extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'terakhir_digunakan' => 'datetime',
        ];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }
}
