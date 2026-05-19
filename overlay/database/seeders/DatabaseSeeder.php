<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Enrollment;
use App\Models\Tagihan;
use App\Models\PengingatTemplate;
use App\Models\PengingatSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        // Admin
        $admin = User::create([
            'name' => 'Admin Si Kursus',
            'email' => 'admin@sikursus.local',
            'password' => Hash::make('admin12345'),
            'role' => 'admin',
            'nohp' => '081200000000',
            'aktif' => true,
        ]);

        // Guru
        $guruBaca = User::create([
            'name' => 'Bu Sari (Guru Baca)',
            'email' => 'guru.baca@sikursus.local',
            'password' => Hash::make('guru12345'),
            'role' => 'guru',
            'nohp' => '081200000011',
            'aktif' => true,
        ]);
        $guruHitung = User::create([
            'name' => 'Pak Adi (Guru Hitung)',
            'email' => 'guru.hitung@sikursus.local',
            'password' => Hash::make('guru12345'),
            'role' => 'guru',
            'nohp' => '081200000012',
            'aktif' => true,
        ]);
        $guruBahasa = User::create([
            'name' => 'Ms Linda (Guru Bahasa)',
            'email' => 'guru.bahasa@sikursus.local',
            'password' => Hash::make('guru12345'),
            'role' => 'guru',
            'nohp' => '081200000013',
            'aktif' => true,
        ]);

        // Kelas
        $kls1 = Kelas::create(['nama' => 'Les Baca Pemula', 'kode' => 'BACA-1', 'tarif' => 150000, 'guru_id' => $guruBaca->id, 'deskripsi' => 'Mengenal huruf, suku kata, kalimat sederhana', 'aktif' => true]);
        $kls2 = Kelas::create(['nama' => 'Les Berhitung Dasar', 'kode' => 'HITUNG-1', 'tarif' => 175000, 'guru_id' => $guruHitung->id, 'deskripsi' => 'Penjumlahan, pengurangan, perkalian, pembagian', 'aktif' => true]);
        $kls3 = Kelas::create(['nama' => 'Les Bahasa Inggris Anak', 'kode' => 'BING-1', 'tarif' => 200000, 'guru_id' => $guruBahasa->id, 'deskripsi' => 'Vocabulary, basic conversation', 'aktif' => true]);

        // Siswa
        $siswaData = [
            ['Andi Pratama', 'andi@siswa.local', '081200000101', 'L'],
            ['Budi Santoso', 'budi@siswa.local', '081200000102', 'L'],
            ['Citra Dewi', 'citra@siswa.local', '081200000103', 'P'],
            ['Dini Anggraini', 'dini@siswa.local', '081200000104', 'P'],
            ['Eko Saputra', 'eko@siswa.local', '081200000105', 'L'],
        ];
        $siswa = [];
        foreach ($siswaData as $s) {
            $siswa[] = User::create([
                'name' => $s[0],
                'email' => $s[1],
                'password' => Hash::make('siswa12345'),
                'role' => 'siswa',
                'nohp' => $s[2],
                'gender' => $s[3],
                'aktif' => true,
            ]);
        }

        // Enrollments
        $today = Carbon::now()->startOfMonth();
        Enrollment::create(['siswa_id' => $siswa[0]->id, 'kelas_id' => $kls1->id, 'tgl_mulai' => $today, 'status' => 'aktif']);
        Enrollment::create(['siswa_id' => $siswa[0]->id, 'kelas_id' => $kls2->id, 'tgl_mulai' => $today, 'status' => 'aktif']);
        Enrollment::create(['siswa_id' => $siswa[1]->id, 'kelas_id' => $kls1->id, 'tgl_mulai' => $today, 'status' => 'aktif']);
        Enrollment::create(['siswa_id' => $siswa[2]->id, 'kelas_id' => $kls3->id, 'tgl_mulai' => $today, 'status' => 'aktif']);
        Enrollment::create(['siswa_id' => $siswa[3]->id, 'kelas_id' => $kls2->id, 'tgl_mulai' => $today, 'status' => 'aktif']);
        Enrollment::create(['siswa_id' => $siswa[3]->id, 'kelas_id' => $kls3->id, 'tgl_mulai' => $today, 'status' => 'aktif']);
        Enrollment::create(['siswa_id' => $siswa[4]->id, 'kelas_id' => $kls1->id, 'tgl_mulai' => $today, 'status' => 'aktif']);

        // Tagihan untuk bulan ini
        $periode = $today->format('Y-m');
        $jatuhTempo = $today->copy()->day(10);
        foreach (Enrollment::with('kelas')->where('status','aktif')->get() as $e) {
            Tagihan::create([
                'siswa_id' => $e->siswa_id,
                'kelas_id' => $e->kelas_id,
                'enrollment_id' => $e->id,
                'periode' => $periode,
                'nominal' => $e->kelas->tarif,
                'jatuh_tempo' => $jatuhTempo,
                'status' => 'belum_bayar',
            ]);
        }

        // Pengingat templates
        $tplH3 = PengingatTemplate::create([
            'nama' => 'Pengingat 3 Hari Sebelum Jatuh Tempo',
            'judul' => 'Pengingat: Tagihan {kelas} jatuh tempo {jatuh_tempo}',
            'body' => "Halo {nama},\n\nTagihan kursus {kelas} periode {periode} sebesar Rp {nominal} akan jatuh tempo pada {jatuh_tempo}.\n\nSilakan lakukan pembayaran sebelum tanggal tersebut. Terima kasih.",
            'aktif' => true,
        ]);
        $tplH0 = PengingatTemplate::create([
            'nama' => 'Pengingat Jatuh Tempo Hari Ini',
            'judul' => 'Tagihan {kelas} jatuh tempo HARI INI',
            'body' => "Halo {nama},\n\nTagihan kursus {kelas} periode {periode} sebesar Rp {nominal} jatuh tempo HARI INI ({jatuh_tempo}).\n\nMohon segera lakukan pembayaran. Terima kasih.",
            'aktif' => true,
        ]);
        $tplH7 = PengingatTemplate::create([
            'nama' => 'Tagihan Lewat Jatuh Tempo 7 Hari',
            'judul' => 'Tagihan {kelas} sudah lewat jatuh tempo',
            'body' => "Halo {nama},\n\nTagihan kursus {kelas} periode {periode} sebesar Rp {nominal} sudah lewat jatuh tempo {jatuh_tempo}.\n\nMohon segera diselesaikan. Terima kasih.",
            'aktif' => true,
        ]);

        // Settings
        PengingatSetting::create(['template_id' => $tplH3->id, 'trigger_type' => 'before', 'hari_offset' => 3, 'aktif' => true]);
        PengingatSetting::create(['template_id' => $tplH0->id, 'trigger_type' => 'on', 'hari_offset' => 0, 'aktif' => true]);
        PengingatSetting::create(['template_id' => $tplH7->id, 'trigger_type' => 'after', 'hari_offset' => 7, 'aktif' => true]);

        echo "Seed selesai.\n";
        echo "Login: admin@sikursus.local / admin12345\n";
        echo "Login guru: guru.baca@sikursus.local / guru12345\n";
        echo "Login siswa: andi@siswa.local / siswa12345\n";
    }
}
