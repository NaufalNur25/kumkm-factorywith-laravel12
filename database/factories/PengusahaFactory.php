<?php

namespace Database\Factories;

use App\Enums\GenderEnum;
use App\Enums\StatusPengusahaEnum;
use App\Models\PendidikanFormal;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pengusaha>
 */
class PengusahaFactory extends Factory
{
    const TABLE_MASTER_DAERAH = "ref_kelurahan";

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        #Jenis Kelamin
        $genderEnum = collect(GenderEnum::cases())->random();
        $genderValue = $genderEnum->value;
        $genderLabel = $genderEnum->label();

        #Tanggal Lahir
        $tanggalLahir = $this->generateTanggalLahir();
        $umur = now()->year - $tanggalLahir->year;

        #Daerah
        $daerah = DB::table(self::TABLE_MASTER_DAERAH)
            ->select('id_kel')
            ->inRandomOrder()
            ->value('id_kel');

        #Kontak HP
        $kontakHp = $this->faker->numerify('+628#########');

        #Status Pengusaha
        $statusPengusaha = collect(StatusPengusahaEnum::cases())->random();
        $statusPengusahaValue = $statusPengusaha->value;

        return [
            'nik_pengusaha' => $this->generateNik($tanggalLahir),
            'npwp_pengusaha' => $this->generateNpwp(),
            'nama_pengusaha' => $this->faker->firstName($genderLabel) . ' ' . $this->faker->lastName($genderLabel),
            'id_jenis_kelamin' => $genderValue, #1: Laki-laki; 2: Perempuan
            'umur' => $umur,
            'id_status_wirausaha' => null,
            'kontak_telepon' => $this->faker->numerify('021#######'),
            'kontak_telepon_ext' => $this->faker->optional(0.5)->numerify('###'),
            'kontak_hp' => $kontakHp,
            'kontak_whatsapp' => $this->faker->boolean(80) ? $kontakHp : fake()->numerify('+628#########'),
            'id_pendidikan_formal' => PendidikanFormal::query()->inRandomOrder()->value('id_pendidikan_formal'),
            'is_anggota_koperasi' => false,
            'id_lapangan_usaha_koperasi' => null,
            'id_kategori_kelahiran' => null,
            'id_lokasi_tempat_lahir' => null,
            'tanggal_lahir' => $tanggalLahir->format('Y-m-d'),
            'alamat_id_prov' => intval(substr($daerah, 0, 2)),
            'alamat_id_kabkot' => intval(substr($daerah, 0, 4)),
            'alamat_id_kec' => intval(substr($daerah, 0, 6)),
            'alamat_id_desa_kel' => intval($daerah),
            'alamat_rt' => intval(str_pad(rand(1, 20), 3, '0', STR_PAD_LEFT)),
            'alamat_rw' => intval(str_pad(rand(1, 10), 3, '0', STR_PAD_LEFT)),
            'alamat_jalan_no' => $this->faker->streetAddress(),
            'alamat_kode_pos' => $this->faker->postcode(),
            'kontak_email' => $this->faker->unique()->safeEmail,
            'kontak_instagram' => '@' . substr($this->faker->userName, 0, 28),
            'kontak_facebook' => 'https://facebook.com/' . $this->faker->userName,
            'kontak_website' => $this->faker->optional(0.7)->url,
            'id_status_pengusaha' => $statusPengusahaValue, #1: Pemilik; 2: Pemilik dan Penanggungjawab
            'is_entried_by_enumerator' => null,
            'flag_tanggal_update' => null,
            'flag_checksum' => null,
            'nama_koperasi' => null,
            'id_enumerator' => null,
            'file_ktp' => null,
            'file_npwp' => null,
            'is_kerja_lain' => null,
            'teks_lapangan_usaha_koperasi' => null,
            'is_disabilitas' => false
        ];
    }

    private function generateTanggalLahir(): Carbon
    {
        return Carbon::createFromDate(rand(1990, 2007), rand(1, 12), rand(1, 28));
    }

    private function generateNik(Carbon $tanggalLahir): string
    {
        $province = str_pad(mt_rand(11, 94), 2, '0', STR_PAD_LEFT);
        $city = str_pad(mt_rand(1, 99), 2, '0', STR_PAD_LEFT);
        $district = str_pad(mt_rand(1, 99), 2, '0', STR_PAD_LEFT);
        $birthDate = $tanggalLahir->format('dmy');
        $uniqueNumber = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return $province . $city . $district . $birthDate . $uniqueNumber;
    }

    private function generateNpwp(): string
    {
        $uniqueNumber = str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        $status = mt_rand(0, 1); # 1 digit status (0: pribadi, 1: badan usaha)
        $kpp = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        $branch = '000';

        return substr($uniqueNumber, 0, 2) .
            substr($uniqueNumber, 2, 3) .
            substr($uniqueNumber, 5, 3) .
            $status . '-' .
            $kpp .
            $branch;
    }
}
