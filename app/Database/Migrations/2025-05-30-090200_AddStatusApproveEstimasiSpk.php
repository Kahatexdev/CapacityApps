<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusApproveEstimasiSpk extends Migration
{
    public function up()
    {
        // Ubah kolom status ENUM dari ['belum', 'sudah'] menjadi ['belum', 'sudah', 'approved']
        $this->db->query("ALTER TABLE estimasi_spk MODIFY status ENUM('belum', 'sudah', 'approved') NOT NULL DEFAULT 'belum'");
    }

    public function down()
    {
        // Kembalikan ENUM ke dua pilihan awal: ['belum', 'sudah']
        $this->db->query("ALTER TABLE estimasi_spk MODIFY status ENUM('belum', 'sudah') NOT NULL DEFAULT 'belum'");
    }
}
