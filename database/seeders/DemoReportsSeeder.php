<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\Submission;
use App\Models\User;
use App\Services\SubmissionAnswerValidator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoReportsSeeder extends Seeder
{
    private const FIELD_REPORTING_MONTH = '1617779234-f0oy-phln-ppl0u1qx5';

    private const FIELD_QUARTER = '1617779337-7t2y-gmj9-0nid0p04n';

    private const FIELD_INCIDENT_DATE = '1617779372-kgyo-zi4q-a3cxrkbox';

    private const FIELD_DISCOVERED_DATE = '1617779391-zrmm-v4bx-t4eg9g8sq';

    private const FIELD_DESCRIPTION = '1617779413-vfec-odq8-8m3nm00u7';

    private const FIELD_ROOT_CAUSE = '1617779435-k7j8-6aai-ma0ye5989';

    private const FIELD_AFFECTED_DIVISIONS = '1617779535-70rw-phgu-z775zzpti';

    private const FIELD_ACTUAL_LOSS = '1619062854-ec0x-333o-6m0mla3t7';

    private const FIELD_POTENTIAL_LOSS = '1619062883-xsov-yboj-1hjgvgasn';

    private const FIELD_STATUS = '1619062915-kswm-8e5b-y9rvij69s';

    private const FIELD_NON_FINANCIAL_LOSS = '1619062939-9vfj-cdbo-0qq6omq7h';

    private const MONTH_OCTOBER = '1617779308-67lv-na3n-7bgnnq6ne';

    private const QUARTER_FOUR = '1617779361-8sm6-xjoo-sakjhr9iz';

    public function run(SubmissionAnswerValidator $answerValidator): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Demo reports may only be seeded in local or testing environments.');
        }

        $form = Form::where('slug', 'operational-risk')->first();

        if (! $form) {
            $this->call(DatabaseSeeder::class);
            $form = Form::where('slug', 'operational-risk')->firstOrFail();
        }

        $user = User::firstOrCreate(
            ['email' => 'user@vesperia.test'],
            [
                'name' => 'Vesperia Demo User',
                'password' => Hash::make('UserDemo!2026'),
                'role' => 'user',
            ]
        );
        $user->forceFill(['role' => 'user'])->save();

        $examples = [
            [
                'days_ago' => 1,
                'answers' => [
                    self::FIELD_REPORTING_MONTH => self::MONTH_OCTOBER,
                    self::FIELD_QUARTER => self::QUARTER_FOUR,
                    self::FIELD_INCIDENT_DATE => now()->subDays(4)->toDateString(),
                    self::FIELD_DISCOVERED_DATE => now()->subDays(1)->toDateString(),
                    self::FIELD_DESCRIPTION => 'Rekonsiliasi transaksi pembayaran tertunda sehingga terdapat selisih pencatatan sementara pada laporan harian.',
                    self::FIELD_ROOT_CAUSE => 'Proses sinkronisasi antar sistem terlambat berjalan setelah pemeliharaan terjadwal.',
                    self::FIELD_AFFECTED_DIVISIONS => [
                        '1617779587-p21t-cv59-eoh4ses9b',
                        '1617779590-9spy-03fw-xcejzvd5m',
                    ],
                    self::FIELD_ACTUAL_LOSS => '0',
                    self::FIELD_POTENTIAL_LOSS => '2500000',
                    self::FIELD_STATUS => 'Dalam investigasi',
                    self::FIELD_NON_FINANCIAL_LOSS => 'Keterlambatan rekonsiliasi berpotensi memengaruhi ketepatan laporan operasional harian.',
                ],
            ],
            [
                'days_ago' => 3,
                'answers' => [
                    self::FIELD_REPORTING_MONTH => self::MONTH_OCTOBER,
                    self::FIELD_QUARTER => self::QUARTER_FOUR,
                    self::FIELD_INCIDENT_DATE => now()->subDays(7)->toDateString(),
                    self::FIELD_DISCOVERED_DATE => now()->subDays(3)->toDateString(),
                    self::FIELD_DESCRIPTION => 'Dokumen pendukung untuk proses pengadaan belum tersimpan lengkap pada repositori unit kerja.',
                    self::FIELD_ROOT_CAUSE => 'Daftar periksa penutupan pengadaan tidak mencakup verifikasi unggah seluruh dokumen.',
                    self::FIELD_AFFECTED_DIVISIONS => [
                        '1617779556-3mvh-d9iv-kh72qxxtu',
                        '1617779562-yv38-5sn1-s5yzkhmfp',
                    ],
                    self::FIELD_ACTUAL_LOSS => '0',
                    self::FIELD_POTENTIAL_LOSS => '750000',
                    self::FIELD_STATUS => 'Menunggu kelengkapan dokumen',
                    self::FIELD_NON_FINANCIAL_LOSS => 'Kelengkapan dokumentasi dan kesiapan audit dapat terdampak apabila dokumen tidak segera dipenuhi.',
                ],
            ],
            [
                'days_ago' => 6,
                'answers' => [
                    self::FIELD_REPORTING_MONTH => self::MONTH_OCTOBER,
                    self::FIELD_QUARTER => self::QUARTER_FOUR,
                    self::FIELD_INCIDENT_DATE => now()->subDays(10)->toDateString(),
                    self::FIELD_DISCOVERED_DATE => now()->subDays(6)->toDateString(),
                    self::FIELD_DESCRIPTION => 'Akses ke folder kerja bersama tetap aktif untuk satu akun yang sudah berpindah unit.',
                    self::FIELD_ROOT_CAUSE => 'Pencabutan akses belum otomatis terhubung dengan proses perubahan penempatan pegawai.',
                    self::FIELD_AFFECTED_DIVISIONS => [
                        '1617779578-2qt5-r0o8-dlk9qxlsv',
                        '1617779592-tb7a-ev7e-7ga8o3f5z',
                    ],
                    self::FIELD_ACTUAL_LOSS => '0',
                    self::FIELD_POTENTIAL_LOSS => '0',
                    self::FIELD_STATUS => 'Akses telah ditinjau',
                    self::FIELD_NON_FINANCIAL_LOSS => 'Terdapat potensi paparan informasi internal; tidak ditemukan indikasi perubahan atau pengunduhan data.',
                ],
            ],
        ];

        foreach ($examples as $example) {
            $answers = $answerValidator->validate($form, $example['answers']);
            $description = $answers[self::FIELD_DESCRIPTION];
            $alreadySeeded = $form->submissions()
                ->where('user_id', $user->id)
                ->where('answers->'.self::FIELD_DESCRIPTION, $description)
                ->exists();

            if ($alreadySeeded) {
                continue;
            }

            $createdAt = now()->subDays($example['days_ago']);

            Submission::create([
                'form_id' => $form->id,
                'user_id' => $user->id,
                'answers' => $answers,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $this->command?->info('Three demo operational-risk reports are available for the Vesperia demo user.');
    }
}
