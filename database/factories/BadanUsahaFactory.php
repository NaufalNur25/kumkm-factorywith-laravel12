<?php

namespace Database\Factories;

use App\Enums\GenderEnum;
use App\Enums\StatusPengusahaEnum;
use App\Models\BadanUsaha;
use App\Models\JenisTempatUsaha;
use App\Models\Pengusaha;
use App\Models\StatusBadanUsaha;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BadanUsaha>
 */
class BadanUsahaFactory extends Factory
{
    const TABLE_MASTER_DAERAH = "ref_kelurahan";
    const JENIS_TEMPAT_USAHA_LAINNYA = 5;
    #Kewilayahan
    const PROVINCE = 32; #Jawa Barat
    const LINK_TO_GET_COORDINATION = "https://data.jabarprov.go.id/api-backend//bigdata/diskominfo/od_kode_wilayah_dan_nama_wilayah_desa_kelurahan";

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        #Pengusaha
        $pengusaha = Pengusaha::factory()->create();

        #Daerah
        $province = self::PROVINCE;
        $daerah = DB::table(self::TABLE_MASTER_DAERAH)
            ->where('id_kel', 'like', "{$province}%")
            ->inRandomOrder()
            ->value('id_kel');

        #Tanggal Mendaftar
        $tanggalDaftar = $this->generateTanggalDaftar();
        $tahunDaftar = now()->year - $tanggalDaftar->year;

        #Jenis Kelamin
        $genderEnum = collect(GenderEnum::cases())->random();
        $genderLabel = $genderEnum->label();

        #Kategori Produk dan Kegiatan
        $produk = [
            'Produksi Makanan' => ['Keripik Pisang', 'Kue Kering', 'Sambal Botolan', 'Roti Manis', 'Susu Kedelai'],
            'Produksi Minuman' => ['Jus Buah', 'Kopi Bubuk', 'Teh Organik', 'Minuman Herbal', 'Air Mineral'],
            'Jasa IT' => ['Aplikasi Keuangan', 'Website Perusahaan', 'Sistem POS', 'Aplikasi Mobile', 'Software HR'],
            'Jasa Kreatif' => ['Logo Perusahaan', 'Desain Brosur', 'Video Animasi', 'Fotografi Produk', 'Branding'],
            'Pengolahan Kayu' => ['Meja Kayu', 'Kursi Rotan', 'Lemari Jati', 'Pintu Ukir', 'Rak Dinding'],
        ];
        $kegiatanUtama = array_rand($produk);

        #Jenis Tempat Usaha
        $jenisTempatUsaha = JenisTempatUsaha::query()->inRandomOrder()->value('id_jenis_tempat_usaha');

        #Kontak HP
        $kontakHp = $this->faker->numerify('+628#########');

        #Banyak Data Daftar
        $count = (BadanUsaha::where('bulan_mulai_operasi', $tanggalDaftar->format('m'))->count()) + 1;

        return [
            'id_status_badan_usaha' => StatusBadanUsaha::query()->inRandomOrder()->value('id_status_badan_usaha'),
            'nama_lengkap'
            => $pengusaha->id_status_pengusaha == StatusPengusahaEnum::PEMILIKPENANGGUNGJAWAB->value
                ? $pengusaha->nama_pengusaha
                : $this->faker->firstName($genderLabel) . ' ' . $this->faker->lastName($genderLabel),
            'nama_komersil' => $this->faker->company,
            'nib' => $this->generateNib((int) substr($daerah, 0, 2), $tahunDaftar),
            'npwp_badan_usaha' => $this->generateNpwp(),
            'nik_pengusaha' => $pengusaha->nik_pengusaha,
            'bulan_mulai_operasi' => $tanggalDaftar->month,
            'tahun_mulai_operasi' => $tanggalDaftar->year,
            'kegiatan_utama' => $this->faker->randomElement($produk[$kegiatanUtama]),
            'id_jenis_kegiatan' => null,
            'produk_utama' => array_rand($produk),
            'id_kbli' => null,
            'alamat_id_prov' => (int) intval(substr($daerah, 0, 2)),
            'alamat_id_kabkot' => (int) intval(substr($daerah, 0, 4)),
            'alamat_id_kec' => (int) intval(substr($daerah, 0, 6)),
            'alamat_id_desa_kel' => (int) intval($daerah),
            'id_jenis_tempat_usaha' => $jenisTempatUsaha,
            'jenis_tempat_usaha_lain' => $jenisTempatUsaha === self::JENIS_TEMPAT_USAHA_LAINNYA ? $this->faker->sentence(3) : null,
            'alamat_lengkap' => $this->faker->streetAddress(),
            'alamat_rt' => (int) intval(str_pad(rand(1, 20), 3, '0', STR_PAD_LEFT)),
            'alamat_rw' =>  (int) intval(str_pad(rand(1, 10), 3, '0', STR_PAD_LEFT)),
            'kontak_telepon' => $this->faker->numerify('021#######'),
            'kontak_telepon_ext' => $this->faker->optional(0.5)->numerify('###'),
            'kontak_hp' => $kontakHp,
            'kontak_fax' => $this->faker->optional(0.3)->numerify('021#######'),
            'kontak_email' => $this->faker->unique()->safeEmail,
            'kontak_website' => $this->faker->optional(0.7)->url,
            'catatan_pendataan' => $this->faker->word,
            'kontak_whatsapp' => $this->faker->boolean(80) ? $kontakHp : fake()->numerify('+628#########'),
            'modal_pendirian' => rand(5_000_000, 1_000_000_000),
            'pj_nama' => $pengusaha->id_status_pengusaha == StatusPengusahaEnum::PEMILIKPENANGGUNGJAWAB->value
                ? $pengusaha->nama_pengusaha
                : $this->faker->firstName($genderLabel) . ' ' . $this->faker->lastName($genderLabel),
            'pj_nik' => $pengusaha->id_status_pengusaha == StatusPengusahaEnum::PEMILIKPENANGGUNGJAWAB->value
                ? $pengusaha->nik_pengusaha
                : $this->generateNik(),
            'pj_wa_hp' => $pengusaha->id_status_pengusaha == StatusPengusahaEnum::PEMILIKPENANGGUNGJAWAB->value
                ? $pengusaha->kontak_whatsapp
                : ($this->faker->boolean(80) ? $kontakHp : fake()->numerify('+628#########')),
            'pj_jabatan' => 'Penanggung Jawab',
            'pj_email' => $pengusaha->id_status_pengusaha == StatusPengusahaEnum::PEMILIKPENANGGUNGJAWAB->value
                ? $pengusaha->kontak_email
                : $this->faker->unique()->safeEmail,
            'tgl_pendataan_awal' => null,
            'tgl_pendataan_sampai' => null,
            'tgl_enum_awal' => null,
            'tgl_enum_sampai' => null,
            'id_current_status' => null,
            'file_nib' => null,
            'file_npwp_usaha' => null,
            'file_akta_pendirian' => null,
            'id_si_kumkm' => sprintf("KUMKM-%s-%03d", $tanggalDaftar->format('Ymd'), $count),
            'is_alamat_sama' => false,
            'teks_status_badan_usaha' => $this->faker->word,
            'file_bukti_wawancara' => null,
            'file_geotaging' => null,
            'is_terima_pembinaan_modal' => $this->faker->boolean(30),
            'verified_at' => null,
            'verified_by' => null,
            'total_mandatory' => null,
            'total_non_mandatory' => null,
            'is_check_by_verif_pusat' => $this->faker->boolean(60),
            'source' => null,
            'platform' => null,
            'is_pl_flag' => false,
            'related_id' => null,
            ...$this->generateCoordinate($daerah)
        ];
    }

    public function findByLocationCode(string $parameter): static
    {
        if (!ctype_digit($parameter)) {
            throw new \InvalidArgumentException("Invalid Regency ID format.");
        }

        if (self::PROVINCE !== (int) substr($parameter, 0, 2)) {
            throw new \InvalidArgumentException("Invalid Province code you have to use CODE: " . self::PROVINCE);
        }

        $daerah = DB::table(self::TABLE_MASTER_DAERAH)
            ->where('id_kel', 'like', "{$parameter}%")
            ->inRandomOrder()
            ->value('id_kel');

        if (!$daerah) {
            throw new \Exception("No matching region found for ID: {$parameter}");
        }

        return $this->state(fn(array $attributes) => [
            'alamat_id_prov' => (int) substr($daerah, 0, 2),
            'alamat_id_kabkot' => (int) substr($daerah, 0, 4),
            'alamat_id_kec' => (int) substr($daerah, 0, 6),
            'alamat_id_desa_kel' => (int) $daerah,
            ...$this->generateCoordinate($daerah)
        ]);
    }

    private function generateCoordinate(string $daerah): array
    {
        try {
            $daerahFormat = substr($daerah, 0, 2) . '.' . substr($daerah, 2, 2) . '.' . substr($daerah, 4, 2) . '.' . substr($daerah, 6);

            $response = Http::acceptJson()->get(self::LINK_TO_GET_COORDINATION, [
                'limit' => 10,
                'skip' => 0,
                'where_or' => json_encode(['kemendagri_kelurahan_kode' => $daerahFormat])
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data['data']) && isset($data['data'][0]['latitude'], $data['data'][0]['longitude'])) {
                    return [
                        'alamat_kode_pos' => (int) $data['data'][0]['kode_pos'],
                        'alamat_latitude' => (float) $data['data'][0]['latitude'],
                        'alamat_longitude' => (float) $data['data'][0]['longitude']
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch coordinates: ' . $e->getMessage());
        }


        return [
            'alamat_latitude' => $this->faker->randomFloat(6, -7.9, -6.2), # JAWA_BARAT (-7.9 hingga -6.2)
            'alamat_longitude' => $this->faker->randomFloat(6, 106.4, 108.9), # JAWA_BARAT (106.4 hingga 108.9)
        ];
    }

    private function generateNib(int $kodeWilayah, int $tahunDaftar): string
    {
        $nomorUrut = str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);

        return "{$kodeWilayah}{$tahunDaftar}{$nomorUrut}";
    }

    private function generateTanggalDaftar(): Carbon
    {
        return Carbon::createFromDate(rand(2020, 2024), rand(1, 12), rand(1, 28));
    }

    private function generateNpwp(): string
    {
        $part1 = str_pad(mt_rand(1, 99), 2, '0', STR_PAD_LEFT);
        $part2 = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        $part3 = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        $part4 = mt_rand(0, 1); #0: pusat; 1: cabang
        $part5 = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        $part6 = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);

        return "{$part1}{$part2}{$part3}{$part4}-{$part5}{$part6}";
    }

    private function generateNik(): string
    {
        $province = str_pad(mt_rand(11, 94), 2, '0', STR_PAD_LEFT);
        $city = str_pad(mt_rand(1, 99), 2, '0', STR_PAD_LEFT);
        $district = str_pad(mt_rand(1, 99), 2, '0', STR_PAD_LEFT);
        $birthDate = now()->subYears(rand(17, 60))->format('dmy');
        $uniqueNumber = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return $province . $city . $district . $birthDate . $uniqueNumber;
    }
}
