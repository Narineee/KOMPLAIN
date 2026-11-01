<?php

namespace App\Console\Commands;

use App\Models\Pengajuan;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('pengaduan:auto-confirm')]
#[Description('Mengonfirmasi selesai secara otomatis pengaduan yang telah melewati batas 3 hari tanpa respons pelapor.')]
class AutoConfirmPengajuanCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memeriksa pengaduan yang melewati batas waktu konfirmasi 3 hari...');

        $jumlah = Pengajuan::prosesAutoKonfirmasiKadaluarsa();

        if ($jumlah > 0) {
            $this->info("Berhasil mengonfirmasi {$jumlah} pengaduan secara otomatis.");
        } else {
            $this->info('Tidak ada pengaduan kadaluarsa yang perlu dikonfirmasi.');
        }

        return self::SUCCESS;
    }
}
