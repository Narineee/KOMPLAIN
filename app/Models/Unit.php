<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_unit', 'keterangan'])]
#[Table('unit')]
class Unit extends Model
{
    use HasFactory;

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function petugas(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'petugas');
    }

    public function pengajuans(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'assigned_unit_id');
    }
}
