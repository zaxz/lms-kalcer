<?php

namespace App\Support;

class MockLms
{
    public static function student(): array
    {
        return [
            'student_id' => '2305106',
            'full_name' => 'Dimas Prasetyo',
            'program' => 'Teknik Informatika',
            'concentration' => 'Software Engineering',
            'class' => '2P51',
            'cohort_year' => '2023',
            'academic_advisor' => 'Andi Setiawan, S.Kom., M.Kom.',
        ];
    }

    public static function academicSummary(): array
    {
        return [
            'ipk' => self::formatNumber(self::gradesSummary()['ipk']),
            'total_sks' => self::gradesSummary()['total_sks'],
            'semester' => '4 (Empat)',
            'tahun_akademik' => '2026/2027 Ganjil',
            'attendance_avg' => self::formatNumber(self::attendanceSummary()['avg'], 1),
        ];
    }

    public static function schedule(): array
    {
        return [
            'Senin' => [
                ['day' => 'Senin', 'start_time' => '07:30', 'end_time' => '09:10', 'room' => 'A1.1', 'course_code' => 'PK202', 'course_name' => 'Bahasa Inggris II', 'lecturer' => 'Rina Kartika, S.Pd., M.Hum.', 'type' => 'Teori', 'class_code' => '2P51', 'google_classroom_id' => null],
                ['day' => 'Senin', 'start_time' => '11:00', 'end_time' => '12:40', 'room' => 'A2.3', 'course_code' => 'KB089', 'course_name' => 'Metodologi Penelitian', 'lecturer' => 'Drs. Fajar Nugraha, M.Kom.', 'type' => 'Teori', 'class_code' => '2P51', 'google_classroom_id' => null],
            ],
            'Selasa' => [
                ['day' => 'Selasa', 'start_time' => '07:30', 'end_time' => '09:10', 'room' => 'A1.3', 'course_code' => 'KB082', 'course_name' => 'Kecerdasan Buatan', 'lecturer' => 'Cahyo Prabowo, S.Kom., M.Kom.', 'type' => 'Teori', 'class_code' => '2P51', 'google_classroom_id' => 'kx4f2a9'],
                ['day' => 'Selasa', 'start_time' => '13:00', 'end_time' => '15:40', 'room' => 'Lab. 2', 'course_code' => 'KB096', 'course_name' => 'Praktikum Pemrograman Web II', 'lecturer' => 'Cahyo Prabowo, S.Kom., M.Kom.', 'type' => 'Praktikum', 'class_code' => '2P51', 'google_classroom_id' => 'kx4f2a9'],
            ],
            'Rabu' => [
                ['day' => 'Rabu', 'start_time' => '08:40', 'end_time' => '10:20', 'room' => 'A1.2', 'course_code' => 'KB084', 'course_name' => 'Interaksi Manusia & Komputer', 'lecturer' => 'Sari Wulandari, S.Kom., M.Kom.', 'type' => 'Teori', 'class_code' => '2P51', 'google_classroom_id' => null],
            ],
            'Kamis' => [
                ['day' => 'Kamis', 'start_time' => '07:30', 'end_time' => '09:10', 'room' => 'A2.2', 'course_code' => 'KB090', 'course_name' => 'Matematika Diskrit II', 'lecturer' => 'Dewi Lestari, S.Si., M.Sc.', 'type' => 'Teori', 'class_code' => '2P51', 'google_classroom_id' => null],
                ['day' => 'Kamis', 'start_time' => '09:20', 'end_time' => '11:00', 'room' => 'A1.1', 'course_code' => 'KB095', 'course_name' => 'Sistem Operasi', 'lecturer' => 'Bimo Hartono, S.Kom., M.Cs.', 'type' => 'Teori', 'class_code' => '2P51', 'google_classroom_id' => null],
            ],
            'Jumat' => [
                ['day' => 'Jumat', 'start_time' => '10:30', 'end_time' => '11:30', 'room' => 'A1.3', 'course_code' => 'KB086', 'course_name' => 'Pemrograman Web II', 'lecturer' => 'Cahyo Prabowo, S.Kom., M.Kom.', 'type' => 'Teori', 'class_code' => '2P51', 'google_classroom_id' => 'kx4f2a9'],
            ],
            'Sabtu' => [],
        ];
    }

    public static function todayName(): string
    {
        $map = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        return $map[date('l')];
    }

    public static function exams(): array
    {
        return [
            'UTS' => [
                ['exam_type' => 'UTS', 'date' => '2026-10-12', 'day' => 'Senin', 'start_time' => '08:00', 'end_time' => '09:40', 'room' => 'A1.1', 'course_code' => 'PK202', 'course_name' => 'Bahasa Inggris II', 'class_code' => '2P51', 'lecturer' => 'Rina Kartika, S.Pd., M.Hum.', 'seat_number' => '12', 'question_status' => 'Sudah diunggah'],
                ['exam_type' => 'UTS', 'date' => '2026-10-13', 'day' => 'Selasa', 'start_time' => '10:00', 'end_time' => '11:40', 'room' => 'A1.3', 'course_code' => 'KB082', 'course_name' => 'Kecerdasan Buatan', 'class_code' => '2P51', 'lecturer' => 'Cahyo Prabowo, S.Kom., M.Kom.', 'seat_number' => '08', 'question_status' => '-'],
                ['exam_type' => 'UTS', 'date' => '2026-10-14', 'day' => 'Rabu', 'start_time' => '13:00', 'end_time' => '14:40', 'room' => 'A2.3', 'course_code' => 'KB089', 'course_name' => 'Metodologi Penelitian', 'class_code' => '2P51', 'lecturer' => 'Drs. Fajar Nugraha, M.Kom.', 'seat_number' => '21', 'question_status' => '-'],
            ],
            'UAS' => [
                ['exam_type' => 'UAS', 'date' => '2026-12-14', 'day' => 'Senin', 'start_time' => '08:00', 'end_time' => '09:40', 'room' => 'A1.3', 'course_code' => 'KB086', 'course_name' => 'Pemrograman Web II', 'class_code' => '2P51', 'lecturer' => 'Cahyo Prabowo, S.Kom., M.Kom.', 'seat_number' => '15', 'question_status' => '-'],
                ['exam_type' => 'UAS', 'date' => '2026-12-15', 'day' => 'Selasa', 'start_time' => '10:00', 'end_time' => '11:40', 'room' => 'A1.2', 'course_code' => 'KB084', 'course_name' => 'Interaksi Manusia & Komputer', 'class_code' => '2P51', 'lecturer' => 'Sari Wulandari, S.Kom., M.Kom.', 'seat_number' => '04', 'question_status' => '-'],
                ['exam_type' => 'UAS', 'date' => '2026-12-16', 'day' => 'Rabu', 'start_time' => '08:00', 'end_time' => '09:40', 'room' => 'A2.2', 'course_code' => 'KB090', 'course_name' => 'Matematika Diskrit II', 'class_code' => '2P51', 'lecturer' => 'Dewi Lestari, S.Si., M.Sc.', 'seat_number' => '19', 'question_status' => '-'],
                ['exam_type' => 'UAS', 'date' => '2026-12-17', 'day' => 'Kamis', 'start_time' => '13:00', 'end_time' => '14:40', 'room' => 'A1.1', 'course_code' => 'KB095', 'course_name' => 'Sistem Operasi', 'class_code' => '2P51', 'lecturer' => 'Bimo Hartono, S.Kom., M.Cs.', 'seat_number' => '11', 'question_status' => '-'],
            ],
        ];
    }

    public static function upcomingExams(int $limit = 2): array
    {
        $all = collect(self::exams())->flatten(1)
            ->filter(fn ($exam) => $exam['date'] >= date('Y-m-d'))
            ->sortBy('date')
            ->values()
            ->take($limit);

        return $all->all();
    }

    public static function grades(): array
    {
        return [
            ['course_code' => 'KB001', 'course_name' => 'Algoritma & Pemrograman I', 'grade' => 'A', 'weight' => 4, 'credits' => 4, 'weighted_credits' => 16],
            ['course_code' => 'KB002', 'course_name' => 'Algoritma & Pemrograman II', 'grade' => 'A', 'weight' => 4, 'credits' => 3, 'weighted_credits' => 12],
            ['course_code' => 'KB003', 'course_name' => 'Struktur Data', 'grade' => 'B', 'weight' => 3, 'credits' => 3, 'weighted_credits' => 9],
            ['course_code' => 'KB031', 'course_name' => 'Basis Data I', 'grade' => 'A', 'weight' => 4, 'credits' => 3, 'weighted_credits' => 12],
            ['course_code' => 'KB032', 'course_name' => 'Basis Data II', 'grade' => 'B', 'weight' => 3, 'credits' => 3, 'weighted_credits' => 9],
            ['course_code' => 'KB041', 'course_name' => 'Pemrograman Web I', 'grade' => 'A', 'weight' => 4, 'credits' => 3, 'weighted_credits' => 12],
            ['course_code' => 'KB042', 'course_name' => 'Jaringan Komputer', 'grade' => 'B', 'weight' => 3, 'credits' => 3, 'weighted_credits' => 9],
            ['course_code' => 'KB051', 'course_name' => 'Sistem Digital', 'grade' => 'A', 'weight' => 4, 'credits' => 3, 'weighted_credits' => 12],
            ['course_code' => 'KB061', 'course_name' => 'Arsitektur Komputer', 'grade' => 'B', 'weight' => 3, 'credits' => 3, 'weighted_credits' => 9],
            ['course_code' => 'KB062', 'course_name' => 'Logika Informatika', 'grade' => 'A', 'weight' => 4, 'credits' => 3, 'weighted_credits' => 12],
            ['course_code' => 'PK101', 'course_name' => 'Pendidikan Agama', 'grade' => 'A', 'weight' => 4, 'credits' => 2, 'weighted_credits' => 8],
            ['course_code' => 'PK102', 'course_name' => 'Pendidikan Pancasila', 'grade' => 'B', 'weight' => 3, 'credits' => 2, 'weighted_credits' => 6],
            ['course_code' => 'PK103', 'course_name' => 'Bahasa Indonesia', 'grade' => 'A', 'weight' => 4, 'credits' => 2, 'weighted_credits' => 8],
            ['course_code' => 'PK104', 'course_name' => 'Kewirausahaan', 'grade' => 'B', 'weight' => 3, 'credits' => 2, 'weighted_credits' => 6],
        ];
    }

    public static function gradesSummary(): array
    {
        $grades = self::grades();
        $totalSks = array_sum(array_column($grades, 'credits'));
        $totalNxk = array_sum(array_column($grades, 'weighted_credits'));

        return [
            'ipk' => round($totalNxk / $totalSks, 2),
            'total_sks' => $totalSks,
            'total_nxk' => $totalNxk,
        ];
    }

    public static function khs(): array
    {
        $grade = fn ($g) => collect(self::grades())->firstWhere('course_code', $g);

        $semesters = [
            'Semester 1' => ['KB001', 'PK101', 'PK102', 'KB051', 'PK103', 'KB061'],
            'Semester 2' => ['KB002', 'KB031', 'KB062', 'KB003', 'PK104'],
            'Semester 3' => ['KB032', 'KB041', 'KB042'],
        ];

        $result = [];
        foreach ($semesters as $label => $codes) {
            $items = array_values(array_map($grade, $codes));
            $sks = array_sum(array_column($items, 'credits'));
            $nxk = array_sum(array_column($items, 'weighted_credits'));
            $result[] = [
                'semester' => $label,
                'items' => $items,
                'total_sks' => $sks,
                'total_nxk' => $nxk,
                'ips' => round($nxk / $sks, 2),
            ];
        }

        return $result;
    }

    public static function attendance(): array
    {
        $rows = [
            ['course_code' => 'KB086', 'course_name' => 'Pemrograman Web II', 'class_code' => '2P51', 'present' => 13, 'sick' => 0, 'permission' => 0, 'absent' => 1],
            ['course_code' => 'KB082', 'course_name' => 'Kecerdasan Buatan', 'class_code' => '2P51', 'present' => 10, 'sick' => 1, 'permission' => 0, 'absent' => 1],
            ['course_code' => 'KB084', 'course_name' => 'Interaksi Manusia & Komputer', 'class_code' => '2P51', 'present' => 12, 'sick' => 0, 'permission' => 0, 'absent' => 0],
            ['course_code' => 'PK202', 'course_name' => 'Bahasa Inggris II', 'class_code' => '2P51', 'present' => 9, 'sick' => 0, 'permission' => 1, 'absent' => 2],
            ['course_code' => 'KB089', 'course_name' => 'Metodologi Penelitian', 'class_code' => '2P51', 'present' => 13, 'sick' => 0, 'permission' => 1, 'absent' => 0],
            ['course_code' => 'KB090', 'course_name' => 'Matematika Diskrit II', 'class_code' => '2P51', 'present' => 8, 'sick' => 1, 'permission' => 0, 'absent' => 3],
            ['course_code' => 'KB095', 'course_name' => 'Sistem Operasi', 'class_code' => '2P51', 'present' => 11, 'sick' => 1, 'permission' => 0, 'absent' => 0],
        ];

        return array_map(function ($row) {
            $meetings = $row['present'] + $row['sick'] + $row['permission'] + $row['absent'];
            $percentage = round(($row['present'] / $meetings) * 100, 1);

            return $row + [
                'student_total_attendance' => $row['present'],
                'lecturer_total_meetings' => $meetings,
                'percentage' => $percentage,
                'status' => $percentage >= 85 ? 'safe' : ($percentage >= 75 ? 'warning' : 'critical'),
            ];
        }, $rows);
    }

    public static function attendanceSummary(): array
    {
        $items = self::attendance();

        return [
            'avg' => round(array_sum(array_column($items, 'percentage')) / count($items), 1),
            'total_courses' => count($items),
            'critical' => count(array_filter($items, fn ($i) => $i['status'] === 'critical')),
        ];
    }

    public static function announcements(): array
    {
        return [
            [
                'date' => '2026-08-22',
                'category' => 'Akademik',
                'title' => 'Pengisian KHS semester ganjil 2026/2027 dibuka sampai 30 Agustus 2026',
                'excerpt' => 'Mahasiswa wajib memeriksa dan mengonfirmasi KHS masing-masing sebelum batas waktu berakhir.',
            ],
            [
                'date' => '2026-08-20',
                'category' => 'Info',
                'title' => 'Maintenance sistem LMS pada 25 Agustus 2026 pukul 22:00 - 02:00 WIB',
                'excerpt' => 'Selama jadwal maintenance, layanan LMS tidak dapat diakses sementara waktu.',
            ],
            [
                'date' => '2026-08-18',
                'category' => 'Akademik',
                'title' => 'Pergeseran jadwal pengganti kelas Matematika Diskrit II minggu ini',
                'excerpt' => 'Kelas pengganti diadakan Sabtu pagi di ruang A2.2. Cek jadwal kuliah untuk detailnya.',
            ],
            [
                'date' => '2026-08-15',
                'category' => 'Layanan',
                'title' => 'Pembukaan layanan surat keterangan aktif kuliah melalui BAAK online',
                'excerpt' => 'Pengajuan surat kini dilakukan sepenuhnya secara daring melalui layanan BAAK.',
            ],
            [
                'date' => '2026-08-10',
                'category' => 'Info',
                'title' => 'Kuliah umum AI di Industri bersama praktisi, Jumat 28 Agustus 2026',
                'excerpt' => 'Terbuka untuk seluruh mahasiswa Teknik Informatika. Registrasi melalui himpunan.',
            ],
        ];
    }

    public static function pointBook(): array
    {
        $entries = [
            ['date' => '2025-09-01', 'activity' => 'PKKMB 2025', 'points' => 25, 'note' => 'Peserta Kegiatan'],
            ['date' => '2025-08-17', 'activity' => 'Upacara Hari Kemerdekaan RI ke-81', 'points' => 25, 'note' => 'Peserta Kegiatan'],
            ['date' => '2025-11-01', 'activity' => 'Inagurasi 2025', 'points' => 50, 'note' => 'Peserta Kegiatan 50'],
            ['date' => '2025-12-01', 'activity' => 'Donor Darah 2 2025', 'points' => 25, 'note' => 'Peserta Kegiatan'],
            ['date' => '2026-02-01', 'activity' => 'Diklat & Makrab UKM-PK', 'points' => 25, 'note' => 'Peserta Kegiatan'],
            ['date' => '2026-03-01', 'activity' => 'Donor Darah', 'points' => 25, 'note' => 'Peserta Kegiatan'],
            ['date' => '2026-04-01', 'activity' => 'Deep Talk: Membangun Kesadaran Beragama dan Cinta Tanah Air di Era Perkembangan Teknologi', 'points' => 25, 'note' => 'Peserta Kegiatan'],
        ];

        return [
            'entries' => $entries,
            'total_points' => array_sum(array_column($entries, 'points')),
        ];
    }

    public static function updatedAt(): string
    {
        return 'Diperbarui pukul ' . now()->format('H:i') . ' WIB';
    }

    public static function formatNumber(float|int $number, int $decimals = 2): string
    {
        return number_format($number, $decimals, ',', '.');
    }
}
