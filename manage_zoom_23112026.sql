/*
 Navicat Premium Dump SQL

 Source Server         : Local 3306 - Xampp
 Source Server Type    : MySQL
 Source Server Version : 100424 (10.4.24-MariaDB)
 Source Host           : localhost:3306
 Source Schema         : manage_zoom

 Target Server Type    : MySQL
 Target Server Version : 100424 (10.4.24-MariaDB)
 File Encoding         : 65001

 Date: 23/11/2026 11:10:31
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for activity_logs
-- ----------------------------
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `activity_logs_user_id_foreign`(`user_id` ASC) USING BTREE,
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 84 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of activity_logs
-- ----------------------------
INSERT INTO `activity_logs` VALUES (1, 1, 'create_booking', 'Pengajuan booking baru: \'Enek\' (ID: 1)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-26 09:41:18', '2026-10-26 09:41:18');
INSERT INTO `activity_logs` VALUES (2, 1, 'update_status', 'Status booking \'Enek\' diubah menjadi: Disetujui (ACC)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-26 09:41:23', '2026-10-26 09:41:23');
INSERT INTO `activity_logs` VALUES (3, 1, 'delete_booking', 'Menghapus booking \'Enek\' (ID: 1)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-26 11:38:50', '2026-10-26 11:38:50');
INSERT INTO `activity_logs` VALUES (4, 167, 'create_booking', 'Pengajuan booking baru: \'Tes\' (ID: 2)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-26 15:14:17', '2026-10-26 15:14:17');
INSERT INTO `activity_logs` VALUES (5, 1, 'delete_booking', 'Menghapus booking \'Tes\' (ID: 2)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-26 15:30:22', '2026-10-26 15:30:22');
INSERT INTO `activity_logs` VALUES (6, 1, 'create_booking', 'Pengajuan booking baru: \'Tes\' (ID: 3)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-26 15:31:09', '2026-10-26 15:31:09');
INSERT INTO `activity_logs` VALUES (7, 1, 'update_status', 'Status booking \'Tes\' diubah menjadi: Link Aktif / Siap', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-26 15:31:36', '2026-10-26 15:31:36');
INSERT INTO `activity_logs` VALUES (8, 1, 'delete_booking', 'Menghapus booking \'Tes\' (ID: 3)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-26 16:26:30', '2026-10-26 16:26:30');
INSERT INTO `activity_logs` VALUES (9, 1, 'create_booking', 'Pengajuan booking baru: \'Tes2\' (ID: 4)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-26 16:29:59', '2026-10-26 16:29:59');
INSERT INTO `activity_logs` VALUES (10, 1, 'update_status', 'Status booking \'Tes2\' diubah menjadi: Link Aktif / Siap', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-26 16:32:56', '2026-10-26 16:32:56');
INSERT INTO `activity_logs` VALUES (11, 1, 'delete_booking', 'Menghapus booking \'Tes2\' (ID: 4)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-26 16:33:40', '2026-10-26 16:33:40');
INSERT INTO `activity_logs` VALUES (12, 1, 'create_booking', 'Pengajuan booking baru: \'Tes Pagi\' (ID: 5)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 11:10:58', '2026-10-27 11:10:58');
INSERT INTO `activity_logs` VALUES (13, 1, 'update_status', 'Status booking \'Tes Pagi\' diubah menjadi: Link Aktif / Siap', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 11:22:37', '2026-10-27 11:22:37');
INSERT INTO `activity_logs` VALUES (14, 1, 'delete_booking', 'Menghapus booking \'Tes Pagi\' (ID: 5)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 13:21:52', '2026-10-27 13:21:52');
INSERT INTO `activity_logs` VALUES (15, 1, 'create_booking', 'Pengajuan booking baru: \'Tes2\' (ID: 6)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 16:13:50', '2026-10-27 16:13:50');
INSERT INTO `activity_logs` VALUES (16, 1, 'update_status', 'Status booking \'Tes2\' diubah menjadi: Link Aktif / Siap', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 16:18:09', '2026-10-27 16:18:09');
INSERT INTO `activity_logs` VALUES (17, 1, 'delete_booking', 'Menghapus booking \'Tes2\' (ID: 6)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 16:43:28', '2026-10-27 16:43:28');
INSERT INTO `activity_logs` VALUES (18, 167, 'create_booking', 'Pengajuan booking baru: \'Kick Of Meeting Pelaksanaan Jasa Konsultan HR Profesional Penilaian dan Rekrutmen Staf Inti PR untuk Transisi GC7 Menuju GC8 di PR Konsorsium Komunitas Penabulu-STPI\' (ID: 7) atas nama HERI SUSANTO', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-28 10:03:30', '2026-11-02 16:58:15');
INSERT INTO `activity_logs` VALUES (19, 167, 'update_status', 'Status booking \'Kick Of Meeting Pelaksanaan Jasa Konsultan HR Profesional Penilaian dan Rekrutmen Staf Inti PR untuk Transisi GC7 Menuju GC8 di PR Konsorsium Komunitas Penabulu-STPI\' diubah menjadi: Link Aktif / Siap', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-28 10:04:27', '2026-10-28 10:04:27');
INSERT INTO `activity_logs` VALUES (20, 1, 'create_booking', 'Pengajuan booking baru: \'Surveilen 1 - 29 Oktober 2026\' (ID: 8) atas nama DWI CAHYO PURNOMO', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-29 08:00:33', '2026-11-02 16:58:15');
INSERT INTO `activity_logs` VALUES (21, 1, 'update_status', 'Status booking \'Surveilen 1 - 29 Oktober 2026\' diubah menjadi: Link Aktif / Siap', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-29 08:00:44', '2026-10-29 08:00:44');
INSERT INTO `activity_logs` VALUES (22, 1, 'create_booking', 'Pengajuan booking baru: \'Kick Of Meeting Pelaksanaan Jasa Konsultan HR Profesional Penilaian dan Rekrutmen Staf Inti PR untuk Transisi GC7 Menuju GC8 di PR Konsorsium Komunitas Penabulu-STPI\' (ID: 9) atas nama HERI SUSANTO', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-29 08:05:07', '2026-11-02 16:58:15');
INSERT INTO `activity_logs` VALUES (23, 1, 'update_status', 'Status booking \'Kick Of Meeting Pelaksanaan Jasa Konsultan HR Profesional Penilaian dan Rekrutmen Staf Inti PR untuk Transisi GC7 Menuju GC8 di PR Konsorsium Komunitas Penabulu-STPI\' diubah menjadi: Link Aktif / Siap', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-29 08:05:27', '2026-10-29 08:05:27');
INSERT INTO `activity_logs` VALUES (24, 1, 'create_booking', 'Pengajuan booking baru: \'Psikotes dan Wawancara Kandidat Karyawan PT. Kimpai Dyna Tube\' (ID: 10) atas nama Moh Rizal Kurniawan', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-30 07:53:57', '2026-11-02 16:58:15');
INSERT INTO `activity_logs` VALUES (25, 1, 'update_status', 'Status booking \'Psikotes dan Wawancara Kandidat Karyawan PT. Kimpai Dyna Tube\' diubah menjadi: Link Aktif / Siap', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-30 07:54:05', '2026-10-30 07:54:05');
INSERT INTO `activity_logs` VALUES (26, 1, 'create_booking', 'Pengajuan booking baru: \'Meeting Tim Sertifikasi\' (ID: 11) atas nama Odetta Aulia Bulantrisna', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-02 09:39:23', '2026-11-02 16:58:15');
INSERT INTO `activity_logs` VALUES (27, 1, 'update_status', 'Status booking \'Meeting Tim Sertifikasi\' diubah menjadi: Link Aktif / Siap', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-02 09:39:37', '2026-11-02 09:39:37');
INSERT INTO `activity_logs` VALUES (28, 1, 'create_booking', 'Pengajuan booking baru: \'Asesmen Potensi Level Jabatan Staf/BOD-4 di Lingkungan PG Rajawali II Tahun 2026\' (ID: 12) atas nama Muhammad Cahyo Hermansyah', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-02 13:49:54', '2026-11-02 16:58:15');
INSERT INTO `activity_logs` VALUES (29, 1, 'create_booking', 'Pengajuan booking baru: \'Sertifikasi Auditor Mutu Internal Batch 92\' (ID: 13) atas nama Rachmad Andre Ramadhani', '118.137.51.27', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 15:01:49', '2026-11-03 15:01:49');
INSERT INTO `activity_logs` VALUES (30, 1, 'update_status', 'Status booking \'Sertifikasi Auditor Mutu Internal Batch 92\' diubah menjadi: Link Aktif / Siap (Pemohon: Rachmad Andre Ramadhani)', '118.137.51.27', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 15:01:59', '2026-11-03 15:01:59');
INSERT INTO `activity_logs` VALUES (31, 1, 'create_booking', 'Pengajuan booking baru: \'Diskusi Quantum dan Kemenhub\' (ID: 14) atas nama HERI SUSANTO', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 16:31:26', '2026-11-03 16:31:26');
INSERT INTO `activity_logs` VALUES (32, 1, 'update_status', 'Status booking \'Diskusi Quantum dan Kemenhub\' diubah menjadi: Link Aktif / Siap (Pemohon: HERI SUSANTO)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 16:47:24', '2026-11-03 16:47:24');
INSERT INTO `activity_logs` VALUES (33, 1, 'create_booking', 'Pengajuan booking baru: \'Webinar Hari Pahlawan: \"Wawasan Kebangsaan dan Bela Negara Momentum Memperkuat Nasionalisme dan Mengisi Mindset Karakter Bangsa\"\' (ID: 15) atas nama DWI CAHYO PURNOMO', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-04 09:20:01', '2026-11-04 09:20:01');
INSERT INTO `activity_logs` VALUES (34, 1, 'create_booking', 'Pengajuan booking baru: \'Meeting Akhir Asesor_Asesmen PT PG Rajawali I_BOD-4_4.11.2026\' (ID: 16) atas nama Muhammad Cahyo Hermansyah', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-04 09:23:22', '2026-11-04 09:23:22');
INSERT INTO `activity_logs` VALUES (35, 1, 'create_booking', 'Pengajuan booking baru: \'Ujian susulan dan Remedial Operator X-Ray Fluorescence Spectrometry Batch 11\' (ID: 17) atas nama Muhammad Cahyo Hermansyah', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-04 09:26:28', '2026-11-04 09:26:28');
INSERT INTO `activity_logs` VALUES (36, 1, 'update_status', 'Status booking \'Ujian susulan dan Remedial Operator X-Ray Fluorescence Spectrometry Batch 11\' diubah menjadi: Link Aktif / Siap (Pemohon: Muhammad Cahyo Hermansyah)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-04 09:26:36', '2026-11-04 09:26:36');
INSERT INTO `activity_logs` VALUES (37, 1, 'create_booking', 'Pengajuan booking baru: \'Paparan Sertifikasi LMS Politeknik Keselamatan Transportasi Jalan\' (ID: 18) atas nama Rachmad Andre Ramadhani', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-04 09:44:02', '2026-11-04 09:44:02');
INSERT INTO `activity_logs` VALUES (38, 1, 'update_status', 'Status booking \'Paparan Sertifikasi LMS Politeknik Keselamatan Transportasi Jalan\' diubah menjadi: Link Aktif / Siap (Pemohon: Rachmad Andre Ramadhani)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-04 09:44:14', '2026-11-04 09:44:14');
INSERT INTO `activity_logs` VALUES (39, 1, 'create_booking', 'Pengajuan booking baru: \'Sertifikasi LMS Politeknik Keselamatan Transportasi Jalan\' (ID: 19) atas nama Rachmad Andre Ramadhani', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-04 09:45:38', '2026-11-04 09:45:38');
INSERT INTO `activity_logs` VALUES (40, 1, 'update_status', 'Status booking \'Sertifikasi LMS Politeknik Keselamatan Transportasi Jalan\' diubah menjadi: Link Aktif / Siap (Pemohon: Rachmad Andre Ramadhani)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-04 09:45:49', '2026-11-04 09:45:49');
INSERT INTO `activity_logs` VALUES (41, 1, 'update_status', 'Status booking \'Webinar Hari Pahlawan: \"Wawasan Kebangsaan dan Bela Negara Momentum Memperkuat Nasionalisme dan Mengisi Mindset Karakter Bangsa\"\' diubah menjadi: Link Aktif / Siap (Pemohon: DWI CAHYO PURNOMO)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-04 09:54:06', '2026-11-04 09:54:06');
INSERT INTO `activity_logs` VALUES (42, 1, 'create_booking', 'Pengajuan booking baru: \'Sertifikasi Manajemen Risiko Batch 7\' (ID: 20) atas nama Rachmad Andre Ramadhani', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-09 13:24:49', '2026-11-09 13:24:49');
INSERT INTO `activity_logs` VALUES (43, 1, 'create_booking', 'Pengajuan booking baru: \'Sertifikasi Manajemen Risiko Batch 8\' (ID: 21) atas nama Rachmad Andre Ramadhani', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-09 13:25:20', '2026-11-09 13:25:20');
INSERT INTO `activity_logs` VALUES (44, 1, 'create_booking', 'Pengajuan booking baru: \'Sertifikasi Manajemen Risiko Batch 9\' (ID: 22) atas nama Rachmad Andre Ramadhani', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-09 13:25:40', '2026-11-09 13:25:40');
INSERT INTO `activity_logs` VALUES (45, 1, 'update_status', 'Status booking \'Sertifikasi Manajemen Risiko Batch 7\' diubah menjadi: Link Aktif / Siap (Pemohon: Rachmad Andre Ramadhani)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-09 13:25:58', '2026-11-09 13:25:58');
INSERT INTO `activity_logs` VALUES (46, 1, 'update_status', 'Status booking \'Sertifikasi Manajemen Risiko Batch 8\' diubah menjadi: Link Aktif / Siap (Pemohon: Rachmad Andre Ramadhani)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-09 13:26:21', '2026-11-09 13:26:21');
INSERT INTO `activity_logs` VALUES (47, 1, 'update_status', 'Status booking \'Sertifikasi Manajemen Risiko Batch 9\' diubah menjadi: Link Aktif / Siap (Pemohon: Rachmad Andre Ramadhani)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-09 13:26:30', '2026-11-09 13:26:30');
INSERT INTO `activity_logs` VALUES (48, 1, 'create_booking', 'Pengajuan booking baru: \'Meeting Awal Asesor\' (ID: 23) atas nama Muhammad Cahyo Hermansyah', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-10 10:15:39', '2026-11-10 10:15:39');
INSERT INTO `activity_logs` VALUES (49, 1, 'create_booking', 'Pengajuan booking baru: \'LGD, Wawancara dan Presentasi\' (ID: 24) atas nama Muhammad Cahyo Hermansyah', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-10 10:16:44', '2026-11-10 10:16:44');
INSERT INTO `activity_logs` VALUES (50, 1, 'create_booking', 'Pengajuan booking baru: \'Wawancara, Presentasi dan In Basket\' (ID: 25) atas nama Muhammad Cahyo Hermansyah', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-10 10:40:33', '2026-11-10 10:40:33');
INSERT INTO `activity_logs` VALUES (51, 1, 'create_booking', 'Pengajuan booking baru: \'Psikotes dan Wawancara Kandidat Karyawan Asuransi Astra Cabang Palu 12.11.2026\' (ID: 26) atas nama Noven Anggraini Permatasari', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-12 07:53:01', '2026-11-12 07:53:01');
INSERT INTO `activity_logs` VALUES (52, 1, 'update_status', 'Status booking \'Psikotes dan Wawancara Kandidat Karyawan Asuransi Astra Cabang Palu 12.11.2026\' diubah menjadi: Link Aktif / Siap (Pemohon: Noven Anggraini Permatasari)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-12 07:53:11', '2026-11-12 07:53:11');
INSERT INTO `activity_logs` VALUES (53, 1, 'create_booking', 'Pengajuan booking baru: \'Koordinasi Sertifikasi Wawasan Kebangsaan dan Bela Negara Quantum dan Komunitas Peduli Obyek Vital Nasional\' (ID: 27) atas nama Rachmad Andre Ramadhani', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-12 08:45:06', '2026-11-12 08:45:06');
INSERT INTO `activity_logs` VALUES (54, 1, 'update_status', 'Status booking \'Koordinasi Sertifikasi Wawasan Kebangsaan dan Bela Negara Quantum dan Komunitas Peduli Obyek Vital Nasional\' diubah menjadi: Link Aktif / Siap (Pemohon: Rachmad Andre Ramadhani)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-12 08:45:15', '2026-11-12 08:45:15');
INSERT INTO `activity_logs` VALUES (55, 1, 'create_booking', 'Pengajuan booking baru: \'Meeting Diskusi Rencana Asesmen\' (ID: 28) atas nama HERI SUSANTO', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-12 08:49:46', '2026-11-12 08:49:46');
INSERT INTO `activity_logs` VALUES (56, 1, 'update_status', 'Status booking \'Meeting Diskusi Rencana Asesmen\' diubah menjadi: Link Aktif / Siap (Pemohon: HERI SUSANTO)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-12 08:49:53', '2026-11-12 08:49:53');
INSERT INTO `activity_logs` VALUES (57, 1, 'create_booking', 'Pengajuan booking baru: \'Sertifikasi Wawasan Kebangsaan dan Bela Negara Batch 7\' (ID: 29) atas nama Rachmad Andre Ramadhani', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-12 14:24:12', '2026-11-12 14:24:12');
INSERT INTO `activity_logs` VALUES (58, 1, 'update_status', 'Status booking \'Sertifikasi Wawasan Kebangsaan dan Bela Negara Batch 7\' diubah menjadi: Link Aktif / Siap (Pemohon: Rachmad Andre Ramadhani)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-12 14:24:27', '2026-11-12 14:24:27');
INSERT INTO `activity_logs` VALUES (59, 1, 'create_booking', 'Pengajuan booking baru: \'Sertifikasi Estate Management Batch 24\' (ID: 30) atas nama Rachmad Andre Ramadhani', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-12 14:25:33', '2026-11-12 14:25:33');
INSERT INTO `activity_logs` VALUES (60, 1, 'update_status', 'Status booking \'Sertifikasi Estate Management Batch 24\' diubah menjadi: Link Aktif / Siap (Pemohon: Rachmad Andre Ramadhani)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-12 14:25:40', '2026-11-12 14:25:40');
INSERT INTO `activity_logs` VALUES (61, 1, 'create_booking', 'Pengajuan booking baru: \'Sertifikasi Public Speaker Batch 5\' (ID: 31) atas nama Rachmad Andre Ramadhani', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-12 14:26:26', '2026-11-12 14:26:26');
INSERT INTO `activity_logs` VALUES (62, 1, 'update_status', 'Status booking \'Sertifikasi Public Speaker Batch 5\' diubah menjadi: Link Aktif / Siap (Pemohon: Rachmad Andre Ramadhani)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-12 14:26:32', '2026-11-12 14:26:32');
INSERT INTO `activity_logs` VALUES (63, 1, 'create_booking', 'Pengajuan booking baru: \'Wawancara Rekrutmen\' (ID: 32) atas nama Noven Anggraini Permatasari', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-12 15:44:28', '2026-11-12 15:44:28');
INSERT INTO `activity_logs` VALUES (64, 1, 'update_status', 'Status booking \'Wawancara Rekrutmen\' diubah menjadi: Link Aktif / Siap (Pemohon: Noven Anggraini Permatasari)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-12 15:44:36', '2026-11-12 15:44:36');
INSERT INTO `activity_logs` VALUES (65, 1, 'create_booking', 'Pengajuan booking baru: \'Asesmen SKK Migas\' (ID: 33) atas nama Muhammad Cahyo Hermansyah', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-16 10:03:19', '2026-11-16 10:03:19');
INSERT INTO `activity_logs` VALUES (66, 1, 'create_booking', 'Pengajuan booking baru: \'Remedial Wawasan Kebangsaan dan Bela Negara Universitas Merdeka Malang\' (ID: 34) atas nama Rachmad Andre Ramadhani', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-16 15:03:24', '2026-11-16 15:03:24');
INSERT INTO `activity_logs` VALUES (67, 1, 'update_status', 'Status booking \'Remedial Wawasan Kebangsaan dan Bela Negara Universitas Merdeka Malang\' diubah menjadi: Link Aktif / Siap (Pemohon: Rachmad Andre Ramadhani)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-16 15:03:34', '2026-11-16 15:03:34');
INSERT INTO `activity_logs` VALUES (68, 1, 'create_booking', 'Pengajuan booking baru: \'Ujian Susulan dan Remedial Manajemen Risiko IPB\' (ID: 35) atas nama Rachmad Andre Ramadhani', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 09:31:32', '2026-11-18 09:31:32');
INSERT INTO `activity_logs` VALUES (69, 1, 'create_booking', 'Pengajuan booking baru: \'Ujian Susulan dan Remedial Manajemen Risiko IPB\' (ID: 36) atas nama Rachmad Andre Ramadhani', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 09:31:57', '2026-11-18 09:31:57');
INSERT INTO `activity_logs` VALUES (70, 1, 'update_status', 'Status booking \'Ujian Susulan dan Remedial Manajemen Risiko IPB\' diubah menjadi: Link Aktif / Siap (Pemohon: Rachmad Andre Ramadhani)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 09:32:16', '2026-11-18 09:32:16');
INSERT INTO `activity_logs` VALUES (71, 1, 'update_status', 'Status booking \'Ujian Susulan dan Remedial Manajemen Risiko IPB\' diubah menjadi: Link Aktif / Siap (Pemohon: Rachmad Andre Ramadhani)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 09:32:29', '2026-11-18 09:32:29');
INSERT INTO `activity_logs` VALUES (72, 1, 'create_booking', 'Pengajuan booking baru: \'Assessment PT Pop Properti\' (ID: 37) atas nama Muhammad Cahyo Hermansyah', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 10:18:17', '2026-11-18 10:18:17');
INSERT INTO `activity_logs` VALUES (73, 1, 'create_booking', 'Pengajuan booking baru: \'Wawancara PT Pop\' (ID: 38) atas nama Muhammad Cahyo Hermansyah', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 10:19:24', '2026-11-18 10:19:24');
INSERT INTO `activity_logs` VALUES (74, 1, 'create_booking', 'Pengajuan booking baru: \'Wawancara Tugsus Padinakes\' (ID: 39) atas nama Muhammad Cahyo Hermansyah', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 10:20:33', '2026-11-18 10:20:33');
INSERT INTO `activity_logs` VALUES (75, 1, 'create_booking', 'Pengajuan booking baru: \'Pelatihan AMI ITERA Hari Pertama\' (ID: 40) atas nama DWI CAHYO PURNOMO', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-19 13:44:13', '2026-11-19 13:44:13');
INSERT INTO `activity_logs` VALUES (76, 1, 'create_booking', 'Pengajuan booking baru: \'Pelatihan AMI ITERA Hari Kedua\' (ID: 41) atas nama DWI CAHYO PURNOMO', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-19 13:44:48', '2026-11-19 13:44:48');
INSERT INTO `activity_logs` VALUES (77, 1, 'create_booking', 'Pengajuan booking baru: \'Pelatihan AMI ITERA Hari Ketiga\' (ID: 42) atas nama DWI CAHYO PURNOMO', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-19 13:45:22', '2026-11-19 13:45:22');
INSERT INTO `activity_logs` VALUES (78, 1, 'update_status', 'Status booking \'Pelatihan AMI ITERA Hari Pertama\' diubah menjadi: Link Aktif / Siap (Pemohon: DWI CAHYO PURNOMO)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-19 13:46:04', '2026-11-19 13:46:04');
INSERT INTO `activity_logs` VALUES (79, 1, 'update_status', 'Status booking \'Pelatihan AMI ITERA Hari Kedua\' diubah menjadi: Link Aktif / Siap (Pemohon: DWI CAHYO PURNOMO)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-19 13:46:17', '2026-11-19 13:46:17');
INSERT INTO `activity_logs` VALUES (80, 1, 'update_status', 'Status booking \'Pelatihan AMI ITERA Hari Ketiga\' diubah menjadi: Link Aktif / Siap (Pemohon: DWI CAHYO PURNOMO)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-19 13:46:28', '2026-11-19 13:46:28');
INSERT INTO `activity_logs` VALUES (81, 1, 'create_booking', 'Pengajuan booking baru: \'CornerStone\' (ID: 43) atas nama Muhammad Cahyo Hermansyah', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-20 10:58:27', '2026-11-20 10:58:27');
INSERT INTO `activity_logs` VALUES (82, 1, 'create_booking', 'Pengajuan booking baru: \'Briefing Tim Panitia Psikotes Poltekpel Surabaya\' (ID: 44) atas nama Moh Rizal Kurniawan', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-20 10:59:53', '2026-11-20 10:59:53');
INSERT INTO `activity_logs` VALUES (83, 1, 'update_status', 'Status booking \'Briefing Tim Panitia Psikotes Poltekpel Surabaya\' diubah menjadi: Link Aktif / Siap (Pemohon: Moh Rizal Kurniawan)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-20 11:00:23', '2026-11-20 11:00:23');

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`) USING BTREE,
  INDEX `cache_expiration_index`(`expiration` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache
-- ----------------------------

-- ----------------------------
-- Table structure for cache_locks
-- ----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`) USING BTREE,
  INDEX `cache_locks_expiration_index`(`expiration` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache_locks
-- ----------------------------

-- ----------------------------
-- Table structure for departments
-- ----------------------------
DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_departemen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_departemen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rumpun_direksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `pejabat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `departments_kode_departemen_unique`(`kode_departemen` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 23 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of departments
-- ----------------------------
INSERT INTO `departments` VALUES (1, 'DP-001', 'DIREKTUR UTAMA', 'Direksi', '12067122', '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (2, 'DP-002', 'DIREKTUR EKSEKUTIF', 'Direksi', '17075104', '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (3, 'DP-003', 'DIREKTUR KEUANGAN', 'Direksi', '19116708', '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (4, 'DP-004', 'HRD', 'Keuangan', '15019519', '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (5, 'DP-005', 'MUTU', 'Eksekutif', '16098920', '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (6, 'DP-006', 'PRA PRODUKSI DAN MARKETING', 'Keuangan', '07039516', '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (7, 'DP-007', 'KEUANGAN DAN AKUNTANSI', 'Keuangan', '10039413', '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (8, 'DP-008', 'ASESMEN & PRODUKSI', 'Eksekutif', '02109121', '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (9, 'DP-009', 'PERANGKAT ASESMEN', 'Eksekutif', '26057614', '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (10, 'DP-010', 'MR', 'Eksekutif', '20068111', '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (11, 'DP-011', 'SERTIFIKASI PERSON', 'Eksekutif', '12048714', '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (12, 'DP-012', 'PELATIHAN', 'Keuangan', '09109619', '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (13, 'DP-013', 'SMAP', 'Eksekutif', '07039516', '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (14, 'DP-014', 'PENGADAAN', 'Keuangan', '31077214', '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (15, 'DP-015', 'CABANG JAKARTA', 'Keuangan', NULL, '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (16, 'DP-016', 'LMM', 'Keuangan', NULL, '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (17, 'DP-017', 'QUANTUM LANDPARK', 'Keuangan', NULL, '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (18, 'DP-018', 'MEDIA', 'Eksekutif', NULL, '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (19, 'DP-019', 'SMOP', 'Eksekutif', '16098920', '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (20, 'DP-020', 'RESEARCH & DEVELOPMENT', 'Eksekutif', '12048714', '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (21, 'DP-021', 'UMUM', 'Keuangan', '20066906', '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `departments` VALUES (22, 'DP-022', 'LMM', 'Keuangan', '12345678', '2026-10-31 15:58:53', '2026-10-31 15:58:53');

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `failed_jobs_uuid_unique`(`uuid` ASC) USING BTREE,
  INDEX `failed_jobs_connection_queue_failed_at_index`(`connection` ASC, `queue` ASC, `failed_at` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------
INSERT INTO `failed_jobs` VALUES (1, '97ef2825-bccf-47a9-92e7-30a0519b276a', 'database', 'whatsapp-bulk', '{\"uuid\":\"97ef2825-bccf-47a9-92e7-30a0519b276a\",\"displayName\":\"App\\\\Jobs\\\\SendBulkWhatsAppMessage\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":0,\"maxExceptions\":null,\"failOnTimeout\":true,\"countCrashesAsExceptions\":false,\"backoff\":null,\"timeout\":45,\"retryUntil\":1795055628,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendBulkWhatsAppMessage\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\SendBulkWhatsAppMessage\\\":6:{s:9:\\\"messageId\\\";i:5;s:9:\\\"expiresAt\\\";i:1795055628;s:10:\\\"connection\\\";s:8:\\\"database\\\";s:5:\\\"queue\\\";s:13:\\\"whatsapp-bulk\\\";s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-11-18 09:33:48.686569\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:11:\\\"afterCommit\\\";b:1;}\",\"batchId\":null},\"createdAt\":1794969228,\"delay\":0}', 'Illuminate\\Queue\\MaxAttemptsExceededException: App\\Jobs\\SendBulkWhatsAppMessage has been attempted too many times. in C:\\laragon\\www\\manage-zoom\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\MaxAttemptsExceededException.php:24\nStack trace:\n#0 C:\\laragon\\www\\manage-zoom\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(1147): Illuminate\\Queue\\MaxAttemptsExceededException::forJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob))\n#1 C:\\laragon\\www\\manage-zoom\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(742): Illuminate\\Queue\\Worker->maxAttemptsExceededException(Object(Illuminate\\Queue\\Jobs\\DatabaseJob))\n#2 C:\\laragon\\www\\manage-zoom\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(623): Illuminate\\Queue\\Worker->markJobAsFailedIfAlreadyExceedsMaxAttempts(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), 0)\n#3 C:\\laragon\\www\\manage-zoom\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(580): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#4 C:\\laragon\\www\\manage-zoom\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(469): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#5 C:\\laragon\\www\\manage-zoom\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(152): Illuminate\\Queue\\Worker->runNextJob(\'database\', \'whatsapp-bulk\', Object(Illuminate\\Queue\\WorkerOptions))\n#6 C:\\laragon\\www\\manage-zoom\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(135): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'whatsapp-bulk\')\n#7 C:\\laragon\\www\\manage-zoom\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#8 C:\\laragon\\www\\manage-zoom\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#9 C:\\laragon\\www\\manage-zoom\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#10 C:\\laragon\\www\\manage-zoom\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#11 C:\\laragon\\www\\manage-zoom\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(803): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#12 C:\\laragon\\www\\manage-zoom\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(292): Illuminate\\Container\\Container->call(Array)\n#13 C:\\laragon\\www\\manage-zoom\\vendor\\symfony\\console\\Command\\Command.php(285): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Laravel\\Pao\\Laravel\\PaoOutputStyle))\n#14 C:\\laragon\\www\\manage-zoom\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(261): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Laravel\\Pao\\Laravel\\PaoOutputStyle))\n#15 C:\\laragon\\www\\manage-zoom\\vendor\\symfony\\console\\Application.php(1152): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#16 C:\\laragon\\www\\manage-zoom\\vendor\\symfony\\console\\Application.php(380): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#17 C:\\laragon\\www\\manage-zoom\\vendor\\symfony\\console\\Application.php(219): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#18 C:\\laragon\\www\\manage-zoom\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(205): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#19 C:\\laragon\\www\\manage-zoom\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php(1243): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#20 C:\\laragon\\www\\manage-zoom\\artisan(16): Illuminate\\Foundation\\Application->handleCommand(Object(Symfony\\Component\\Console\\Input\\ArgvInput))\n#21 {main}', '2026-11-19 15:34:58');

-- ----------------------------
-- Table structure for job_batches
-- ----------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `cancelled_at` int NULL DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of job_batches
-- ----------------------------

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED NULL DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `jobs_queue_index`(`queue` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jobs
-- ----------------------------

-- ----------------------------
-- Table structure for memos
-- ----------------------------
DROP TABLE IF EXISTS `memos`;
CREATE TABLE `memos`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `zoom_booking_id` bigint UNSIGNED NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `memo_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `memo_date` date NULL DEFAULT NULL,
  `has_signature` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `memos_zoom_booking_id_foreign`(`zoom_booking_id` ASC) USING BTREE,
  INDEX `memos_user_id_foreign`(`user_id` ASC) USING BTREE,
  CONSTRAINT `memos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `memos_zoom_booking_id_foreign` FOREIGN KEY (`zoom_booking_id`) REFERENCES `zoom_bookings` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of memos
-- ----------------------------

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` VALUES (4, '2026_10_26_090000_create_zoom_system_tables', 1);
INSERT INTO `migrations` VALUES (5, '2026_10_26_150000_create_system_settings_table', 2);
INSERT INTO `migrations` VALUES (6, '2026_10_26_153900_add_slug_to_zoom_bookings_table', 3);
INSERT INTO `migrations` VALUES (7, '2026_10_27_113548_add_project_name_to_zoom_bookings_table', 4);
INSERT INTO `migrations` VALUES (8, '2026_10_27_131202_create_projects_table', 5);
INSERT INTO `migrations` VALUES (9, '2026_10_27_131808_add_dates_to_projects_table', 6);
INSERT INTO `migrations` VALUES (10, '2026_10_27_153944_add_is_active_to_users_table', 7);
INSERT INTO `migrations` VALUES (11, '2026_10_31_154844_create_departments_table', 8);
INSERT INTO `migrations` VALUES (12, '2026_10_31_154854_create_positions_table', 8);
INSERT INTO `migrations` VALUES (13, '2026_11_02_112143_add_fonnte_settings_to_system_settings', 9);
INSERT INTO `migrations` VALUES (14, '2026_11_02_134145_create_whatsapp_gateway_settings', 10);
INSERT INTO `migrations` VALUES (15, '2026_11_03_153040_add_department_id_to_projects_table', 11);
INSERT INTO `migrations` VALUES (16, '2026_11_12_144354_create_whatsapp_bulk_tables', 12);
INSERT INTO `migrations` VALUES (17, '2026_11_12_150925_add_account_options_to_whatsapp_bulk_tables', 13);
INSERT INTO `migrations` VALUES (18, '2026_11_12_164012_add_project_id_to_zoom_bookings_table', 14);

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens`  (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of password_reset_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for positions
-- ----------------------------
DROP TABLE IF EXISTS `positions`;
CREATE TABLE `positions`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_jabatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_jabatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `positions_kode_jabatan_unique`(`kode_jabatan` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of positions
-- ----------------------------
INSERT INTO `positions` VALUES (1, 'JBT-01', 'DIREKTUR UTAMA', '2026-10-31 15:58:53', '2026-10-31 15:58:53');
INSERT INTO `positions` VALUES (2, 'JBT-02', 'DIREKTUR EKSEKUTIF', '2026-10-31 15:58:54', '2026-10-31 15:58:54');
INSERT INTO `positions` VALUES (3, 'JBT-03', 'DIREKTUR KEUANGAN', '2026-10-31 15:58:54', '2026-10-31 15:58:54');
INSERT INTO `positions` VALUES (4, 'JBT-04', 'MANAJER', '2026-10-31 15:58:54', '2026-10-31 15:58:54');
INSERT INTO `positions` VALUES (5, 'JBT-05', 'STAFF', '2026-10-31 15:58:54', '2026-10-31 15:58:54');
INSERT INTO `positions` VALUES (6, 'JBT-06', 'SECURITY', '2026-10-31 15:58:54', '2026-10-31 15:58:54');
INSERT INTO `positions` VALUES (7, 'JBT-07', 'DRIVER', '2026-10-31 15:58:54', '2026-10-31 15:58:54');
INSERT INTO `positions` VALUES (8, 'JBT-08', 'CLEANING SERVICE', '2026-10-31 15:58:54', '2026-10-31 15:58:54');
INSERT INTO `positions` VALUES (9, 'JBT-09', 'SUPPORT', '2026-10-31 15:58:54', '2026-10-31 15:58:54');
INSERT INTO `positions` VALUES (10, 'JBT-10', 'SUPPORT HARIAN', '2026-10-31 15:58:54', '2026-10-31 15:58:54');

-- ----------------------------
-- Table structure for projects
-- ----------------------------
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` bigint UNSIGNED NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `start_date` date NULL DEFAULT NULL,
  `end_date` date NULL DEFAULT NULL,
  `status` enum('active','completed','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `projects_department_id_foreign`(`department_id` ASC) USING BTREE,
  CONSTRAINT `projects_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of projects
-- ----------------------------
INSERT INTO `projects` VALUES (6, 22, 'Lisa Mitra Mandiri', NULL, NULL, NULL, NULL, 'active', '2026-10-30 07:51:17', '2026-11-03 15:43:13');
INSERT INTO `projects` VALUES (7, NULL, 'Meeting Internal', NULL, NULL, NULL, NULL, 'active', '2026-11-02 09:36:58', '2026-11-02 09:36:58');
INSERT INTO `projects` VALUES (8, NULL, 'Meeting Eksternal', NULL, NULL, NULL, NULL, 'active', '2026-11-02 09:37:06', '2026-11-02 09:37:06');
INSERT INTO `projects` VALUES (9, 8, 'Assessment Center Quantum HRM Internasional', NULL, NULL, NULL, NULL, 'active', '2026-11-02 13:48:18', '2026-11-10 10:30:09');
INSERT INTO `projects` VALUES (10, 11, 'Sertifikasi Person Quantum HRM Internasional', NULL, 'Tanda Pelaksanaan Proyek Sertifikasi', NULL, NULL, 'active', '2026-11-03 15:00:55', '2026-11-12 16:48:01');
INSERT INTO `projects` VALUES (11, 12, 'Webinar Pelatihan', NULL, 'Untuk Proyek Webinar dari Departemen Mutu dan Pelatihan', NULL, NULL, 'active', '2026-11-04 09:19:17', '2026-11-04 09:19:17');

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sessions_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `sessions_last_activity_index`(`last_activity` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sessions
-- ----------------------------
INSERT INTO `sessions` VALUES ('2JiXRfYCedIJXuxC1QbX3TSFXxDF9M5A0AWp5GEf', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJldjRPem1LRVdVWVBHSkdtSmV5S3I3WjNkZmpXWVdpMEFSVDNzYlRmIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDMwXC9ib29raW5nc1wvY3JlYXRlIiwicm91dGUiOiJib29raW5ncy5jcmVhdGUifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwicGFzc3dvcmRfaGFzaF93ZWIiOiIzYTc4YmVlZTAzYjBlODE0OGYxYmEwMjljYzJkMWJiYWJhM2FhMzhjZjM1YjMxZDliZmRmOTBmOGI2NjFlM2QzIn0=', 1795164191);
INSERT INTO `sessions` VALUES ('IHlU8WVN9aOAdDfvqxY3nkqe2grJMSTbYzQiyZ4t', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJrYm9VNFVwaFZrbkowWXdrZGhWUEhaem02Umdack9BdHUxZWY4eTdNIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDMwXC9ib29raW5nc1wvY3JlYXRlIiwicm91dGUiOiJib29raW5ncy5jcmVhdGUifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwicGFzc3dvcmRfaGFzaF93ZWIiOiIzYTc4YmVlZTAzYjBlODE0OGYxYmEwMjljYzJkMWJiYWJhM2FhMzhjZjM1YjMxZDliZmRmOTBmOGI2NjFlM2QzIn0=', 1795332843);
INSERT INTO `sessions` VALUES ('NQdjXK0J9YPDV4ip52Mgof5cfH7BazWlOXjeMIDZ', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiI0Ujg5SzZEQ0ZsZUtnWWIwWFI3dHJHRnpZUm1xRW41RWNrNElsZDJiIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDMwXC96b29tLWFjY291bnRzXC9leHBvcnQtcGRmIiwicm91dGUiOiJ6b29tLWFjY291bnRzLmV4cG9ydC1wZGYifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwicGFzc3dvcmRfaGFzaF93ZWIiOiIzYTc4YmVlZTAzYjBlODE0OGYxYmEwMjljYzJkMWJiYWJhM2FhMzhjZjM1YjMxZDliZmRmOTBmOGI2NjFlM2QzIn0=', 1795402830);

-- ----------------------------
-- Table structure for system_settings
-- ----------------------------
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'zoom',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `system_settings_key_unique`(`key` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of system_settings
-- ----------------------------
INSERT INTO `system_settings` VALUES (1, 'zoom_waiting_room', '1', 'zoom_meeting', 'boolean', 'Ruang Tunggu (Waiting Room)', 'Tahan peserta di ruang tunggu sebelum diizinkan masuk oleh Host.', '2026-10-26 15:03:05', '2026-10-26 15:03:05');
INSERT INTO `system_settings` VALUES (2, 'zoom_mute_upon_entry', '1', 'zoom_meeting', 'boolean', 'Mute Peserta Saat Masuk (Mute Upon Entry)', 'Otomatis matikan mikrofon peserta saat baru bergabung.', '2026-10-26 15:03:05', '2026-10-26 15:03:05');
INSERT INTO `system_settings` VALUES (3, 'zoom_join_before_host', '0', 'zoom_meeting', 'boolean', 'Izinkan Masuk Sebelum Host (Join Before Host)', 'Peserta diperbolehkan masuk ke room sebelum Host memulai rapat.', '2026-10-26 15:03:05', '2026-10-26 15:03:05');
INSERT INTO `system_settings` VALUES (4, 'zoom_jbh_time', '0', 'zoom_meeting', 'select', 'Waktu Sebelum Host (Menit)', 'Batas menit peserta boleh masuk sebelum jam mulai (0 = kapan saja, 5, 10, 15 menit).', '2026-10-26 15:03:05', '2026-10-26 16:47:27');
INSERT INTO `system_settings` VALUES (5, 'zoom_host_video', '1', 'zoom_meeting', 'boolean', 'Video Host Aktif Saat Masuk', 'Kamera Host otomatis menyala saat bergabung ke pertemuan.', '2026-10-26 15:03:05', '2026-10-26 15:03:05');
INSERT INTO `system_settings` VALUES (6, 'zoom_participant_video', '1', 'zoom_meeting', 'boolean', 'Video Peserta Aktif Saat Masuk', 'Kamera peserta otomatis menyala saat bergabung ke pertemuan.', '2026-10-26 15:03:05', '2026-10-26 15:03:05');
INSERT INTO `system_settings` VALUES (7, 'zoom_auto_recording', 'local', 'zoom_meeting', 'select', 'Perekaman Otomatis (Auto Recording)', 'Pilihan: none (tidak merekam), cloud (rekam ke Cloud Zoom), local (rekam di komputer host).', '2026-10-26 15:03:05', '2026-10-26 16:47:22');
INSERT INTO `system_settings` VALUES (8, 'zoom_meeting_authentication', '0', 'zoom_meeting', 'boolean', 'Wajib Login Akun Zoom (Authentication Required)', 'Hanya pengguna yang login ke aplikasi Zoom yang dapat masuk room.', '2026-10-26 15:03:05', '2026-10-26 15:03:05');
INSERT INTO `system_settings` VALUES (9, 'zoom_watermark', '0', 'zoom_meeting', 'boolean', 'Tampilkan Watermark Layar', 'Menempelkan watermark nama/email peserta pada layar share-screen (keamanan dokumen).', '2026-10-26 15:03:05', '2026-10-26 15:03:05');
INSERT INTO `system_settings` VALUES (10, 'fonnte_token', NULL, 'whatsapp_gateway', 'password', 'Fonnte Device Token', 'Token API dari akun Fonnte Anda (menu Device di fonnte.com).', '2026-11-02 13:42:18', '2026-11-02 13:42:18');
INSERT INTO `system_settings` VALUES (11, 'fonnte_enabled', '0', 'whatsapp_gateway', 'boolean', 'Aktifkan Pengiriman WhatsApp via Fonnte', 'Jika diaktifkan, tombol kirim WhatsApp di halaman detail peminjaman akan otomatis mengirim pesan langsung via API Fonnte ke nomor tujuan.', '2026-11-02 11:23:57', '2026-11-02 15:15:45');
INSERT INTO `system_settings` VALUES (12, 'fonnte_country_code', '62', 'whatsapp_gateway', 'string', 'Kode Negara Default', 'Kode negara default jika nomor tujuan diawali angka 0 (misal: 62 untuk Indonesia).', '2026-11-02 11:23:57', '2026-11-02 11:23:57');
INSERT INTO `system_settings` VALUES (13, 'wa_default_provider', 'flowkirim', 'whatsapp_gateway', 'select', 'Default WhatsApp Gateway Provider', 'Pilih gateway WhatsApp utama yang digunakan sistem untuk mengirimkan pesan/notifikasi secara otomatis.', '2026-11-02 13:42:18', '2026-11-02 13:42:18');
INSERT INTO `system_settings` VALUES (14, 'wa_gateway_enabled', '1', 'whatsapp_gateway', 'boolean', 'Aktifkan Gateway WhatsApp Otomatis', 'Jika dinonaktifkan, seluruh aksi kirim pesan akan dialihkan manual ke WhatsApp Web.', '2026-11-02 13:42:18', '2026-11-02 13:42:18');
INSERT INTO `system_settings` VALUES (15, 'wa_country_code', '62', 'whatsapp_gateway', 'string', 'Kode Negara Default', 'Kode negara standar untuk nomor telepon lokal berawalan 0 (contoh: 62).', '2026-11-02 13:42:18', '2026-11-02 13:42:18');
INSERT INTO `system_settings` VALUES (16, 'flowkirim_token', '6ef2d43d621882acc24419ea4bf0449f993dd701f234999c5084106206de8a11', 'whatsapp_gateway', 'password', 'FlowKirim API / Bearer Token', 'API Token dari dashboard FlowKirim (Authorization: Bearer token).', '2026-11-02 13:42:18', '2026-11-02 15:15:45');
INSERT INTO `system_settings` VALUES (17, 'flowkirim_endpoint', 'https://scan.flowkirim.com/api/whatsapp/messages/text', 'whatsapp_gateway', 'string', 'FlowKirim Send Endpoint', 'URL endpoint pengiriman pesan FlowKirim API (default: https://api.flowkirim.com/v1/messages).', '2026-11-02 13:42:18', '2026-11-02 16:04:38');
INSERT INTO `system_settings` VALUES (18, 'fonnte_endpoint', 'https://api.fonnte.com/send', 'whatsapp_gateway', 'string', 'Fonnte Send Endpoint', 'URL endpoint pengiriman pesan Fonnte API (default: https://api.fonnte.com/send).', '2026-11-02 13:42:18', '2026-11-02 13:42:18');
INSERT INTO `system_settings` VALUES (19, 'flowkirim_session_id', 'shandy123-22246765-e4f4-4c60-8991-ab433c51bb1b_1794205676', 'whatsapp_gateway', 'string', 'FlowKirim Device / Session ID', 'ID Sesi / Perangkat WhatsApp yang aktif di dashboard FlowKirim (contoh: flowkirim-xxx).', '2026-11-02 16:04:43', '2026-11-09 13:33:18');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` tinyint NOT NULL DEFAULT 2 COMMENT '1: Admin, 2: Pegawai/Unit, 3: Staff, 4: Lainnya',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status akun aktif / nonaktif',
  `jabatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `departemen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `departemen2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `no_hp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email` ASC) USING BTREE,
  UNIQUE INDEX `users_username_unique`(`username` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 320 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Administrator Zoom', 'admin', 'admin@zoom.test', NULL, '$2y$12$K.tbeK5TU0z6ZtInOs6lse9R5hB7h8Yr9MUJge.x0Y/o1eXDkzRl.', 1, 1, 'Super Administrator', 'TI / Pusat Komputer', NULL, '081234567890', NULL, NULL, '2026-10-26 02:16:30', '2026-10-26 02:16:30');
INSERT INTO `users` VALUES (2, 'Staf Humas & Kerjasama', 'stafhumas', 'user@zoom.test', NULL, '$2y$12$wiWUwaUZAgKzYdiMJmDDSOjcF.3TUV0cPEPHY1wkuT7jsUd/nW1DO', 2, 1, 'Pranata Humas', 'Biro Humas & Protokol', NULL, '089876543210', NULL, NULL, '2026-10-26 02:16:30', '2026-10-26 02:16:30');
INSERT INTO `users` VALUES (3, 'PRIBADIYONO', '17075104', 'pribadiyono@yahoo.com', NULL, '$2y$12$9bsn8F.DuixU.sU/km0UH.ydZIP26c0rfc0eiqasM4gBTIilQ9fZ6', 2, 1, 'JBT-02', 'DP-002', NULL, '082140445011', NULL, NULL, '2026-10-26 13:26:22', '2026-10-26 13:26:22');
INSERT INTO `users` VALUES (4, 'ARY YUNANTO', '20066906', 'aryqhrm@gmail.com', NULL, '$2y$12$fhuzous53bwHc17EgnmmF.Vmt.jIZEg2O2fYddNS6uMcIczasD0qy', 2, 1, 'JBT-04', 'DP-021', NULL, '082230081930', NULL, NULL, '2026-10-26 13:26:22', '2026-10-26 13:26:22');
INSERT INTO `users` VALUES (5, 'NURI DJATI PERWITASARI', '19116708', 'WITASARI11@G.MAIL.COM', NULL, '$2y$12$LZG0MBXX5rhGetRChzayROzThTVhzbQf901gNkrZn5ok2OI7KOOji', 2, 1, 'JBT-03', 'DP-003', NULL, '081252983049', NULL, NULL, '2026-10-26 13:26:22', '2026-10-26 13:26:22');
INSERT INTO `users` VALUES (9, 'MICHAEL ADITYA HENDRAWAN', '20068111', 'michaelarchangel26@gmail.com', NULL, '$2y$12$r0AB6CsgZ7q693WNR2239uZMipGa5xewkTruQq3Xz1j3/6UeMCZNy', 2, 1, 'JBT-04', 'DP-004', NULL, '081332015184', NULL, NULL, '2026-10-26 13:26:23', '2026-10-26 13:26:23');
INSERT INTO `users` VALUES (10, 'SLAMET HARYONO', '12125811', 'slmtt531@gmail.com', NULL, '$2y$12$vuLnRe9OxWFG1aJtMAm02OqnxyYd7w4ud8swXD1GHcPtEnUiX9jGe', 2, 1, 'JBT-05', 'DP-021', NULL, '085203023851', NULL, NULL, '2026-10-26 13:26:23', '2026-10-26 13:26:23');
INSERT INTO `users` VALUES (11, 'NOVA MAYA WULANDARI', '10039413', 'novamaya1003@gmail.com', NULL, '$2y$12$FQzheKneqlzUALRFKdSPFOQHSd4OIDS66Uajd14m7Drg1GSJN7Cjq', 2, 1, 'JBT-04', 'DP-007', NULL, '081554499348', NULL, NULL, '2026-10-26 13:26:23', '2026-10-26 13:26:23');
INSERT INTO `users` VALUES (12, 'RATNA DEWI PUSPITAWATI', '26057614', 'razna.kecil@gmail.com', NULL, '$2y$12$xnW/hoXCd2jToYq1K9jb7evX6tOrqRTMWx/riizIpUle6PK7VBCkm', 2, 1, 'JBT-04', 'DP-009', NULL, '08113519176', NULL, NULL, '2026-10-26 13:26:24', '2026-10-26 13:26:24');
INSERT INTO `users` VALUES (13, 'MOH. HAFILUDDIN', '12048714', 'hafiluddin3@gmail.com', NULL, '$2y$12$MXS4CTk4VZvBktaGG4nwXuoFJXB1ecvXUs3ETghdbdKYoP4be3PeW', 2, 1, 'JBT-04', 'DP-011', NULL, '081333319734', NULL, NULL, '2026-10-26 13:26:24', '2026-10-26 13:26:24');
INSERT INTO `users` VALUES (14, 'KAIDEN ADJI BASUKI', '31077214', 'kaidenadji07@gmail.com', NULL, '$2y$12$NkJXmWHVj1QxHsdUxIw4OeqQFGR.uqR0fY3llK80frwFtncfEIe5e', 2, 1, 'JBT-04', 'DP-014', NULL, '082244929227', NULL, NULL, '2026-10-26 13:26:24', '2026-10-26 13:26:24');
INSERT INTO `users` VALUES (15, 'HERI SUSANTO', '07039516', 'antoputra00@gmail.com', NULL, '$2y$12$qb599UfhLWLRgrO/23XjLOtw8p6ol3QAQFahuD1rHYiGmBiSLGbA6', 2, 1, 'JBT-04', 'DP-006', NULL, '081336107544', NULL, NULL, '2026-10-26 13:26:24', '2026-10-26 13:26:24');
INSERT INTO `users` VALUES (16, 'SUDARKO', '04077016', 'bamsukoko@gmail.com', NULL, '$2y$12$Lldu4oTeN2TLN0wMl3X/8ea8qVoiCPntQ2pXTaqNGyB4HfnBGVwpa', 2, 1, 'JBT-06', 'DP-021', NULL, '081336720713', NULL, NULL, '2026-10-26 13:26:24', '2026-10-26 13:26:24');
INSERT INTO `users` VALUES (18, 'ERITA MARISDIANTI', '23039617', 'eritamarisdianti96@gmail.com', NULL, '$2y$12$qApfBWY5ePGuZYZ6OaxAqupX73HJam.kLNhSpi4ohSOQkEz5XqB86', 2, 1, 'JBT-04', 'DP-008', NULL, '082333557173', NULL, NULL, '2026-10-26 13:26:25', '2026-10-26 13:26:25');
INSERT INTO `users` VALUES (20, 'ADAM SOPIAN', '13099817', 'adamsopian98@gmail.com', NULL, '$2y$12$ziqNR49CNDZj/kmiZ1TjGeJgygPeh6m/xin2GGaB2KPYeq1mGPpb6', 2, 0, 'JBT-05', 'DP-015', NULL, '083841277187', NULL, NULL, '2026-10-26 13:26:25', '2026-10-27 15:49:51');
INSERT INTO `users` VALUES (21, 'DWI CAHYO PURNOMO', '09109619', 'dwi@quantum-hrmi.com', NULL, '$2y$12$tVG6RLSauU44DuBSizaeDO1Yfv0L4.42bEH3dSw3GHGjq33edTyW6', 2, 1, 'JBT-04', 'DP-012', NULL, '087856008311', NULL, NULL, '2026-10-26 13:26:25', '2026-10-26 13:26:25');
INSERT INTO `users` VALUES (24, 'ANIS FITRIAH', '01119919', 'anisfitriahxak1@gmail.com', NULL, '$2y$12$ch6TIszGWTZoS3cCV4V2GeShzDOqRzjX4BwPoZP3xu532tlFOJ1H6', 2, 1, 'JBT-05', 'DP-007', NULL, '085851317281', NULL, NULL, '2026-10-26 13:26:26', '2026-10-26 13:26:26');
INSERT INTO `users` VALUES (28, 'HARDIKA PRAYUDI STYAWAN', '16098920', 'hardikaprayudi_styawan@yahoo.co.id', NULL, '$2y$12$zsTosmNr7HoOLD/Vy3JUReyWsyXxOhjQ0oEUAqxT7ThzmSD1F8aMq', 2, 1, 'JBT-04', 'DP-005', NULL, '08563154010', NULL, NULL, '2026-10-26 13:26:27', '2026-10-26 13:26:27');
INSERT INTO `users` VALUES (29, 'DWI ERNAWATI', '30097013', 'dwiquantum30@gmail.com', NULL, '$2y$12$Y4.N7FpaXhiEF6S4srHvV.Ob2QkbKLNvvKm860JNO4fi3ocs7/Zja', 2, 1, 'JBT-05', 'DP-007', NULL, '085853413177', NULL, NULL, '2026-10-26 13:26:27', '2026-10-26 13:26:27');
INSERT INTO `users` VALUES (34, 'INDRA NUR ROKHMAT', '26099918', 'indrasanu72@gmail.com', NULL, '$2y$12$o95QxzaQQMoUWlS14TCX2.L7OyAgUERU6TINKJvMyRAZUsqQFmu96', 2, 1, 'JBT-05', 'DP-005', NULL, '6281216929401', NULL, NULL, '2026-10-26 13:26:28', '2026-10-26 13:26:28');
INSERT INTO `users` VALUES (36, 'DYAH LARASATI S', '02029820', 'sanlarasati02@gmail.com', NULL, '$2y$12$miKOxrY3Vestsb7uUgiX4ug1AJOZsIKK7dHzITXmZRV95/.mmbv9W', 2, 1, 'JBT-05', 'DP-009', NULL, '+628815511545', NULL, NULL, '2026-10-26 13:26:28', '2026-10-26 13:26:28');
INSERT INTO `users` VALUES (40, 'EMMA DYAH SAFIRA', '27019921', 'emma.dyah27@gmail.com', NULL, '$2y$12$LA/p1MJm.Ux8kjoMq.VDr.CGV1KgXuYUkURDJtyL4ukf7Dv4osO8K', 2, 1, 'JBT-05', 'DP-008', NULL, '089644951575', NULL, NULL, '2026-10-26 13:26:29', '2026-10-26 13:26:29');
INSERT INTO `users` VALUES (41, 'REZA SAPUTRA', '25099721', 'rezaaaspt@gmail.com', NULL, '$2y$12$Jkw1RLRSnRSGrbSD3bieLObSp1JXe2g0NBYyi1DLoAfVB3xj6Q.Q2', 2, 1, 'JBT-05', 'DP-006', NULL, '082155006022', NULL, NULL, '2026-10-26 13:26:29', '2026-10-26 13:26:29');
INSERT INTO `users` VALUES (42, 'AHMAD SURYADI', '12067122', 'suryadinomi@gmail.com', NULL, '$2y$12$DHanmABfpixrORGQ5/eurummt3wZZybHsPEsn.H6nZrDxJJu8dhui', 2, 1, 'JBT-01', 'DP-001', NULL, '08129240993', NULL, NULL, '2026-10-26 13:26:29', '2026-10-26 13:26:29');
INSERT INTO `users` VALUES (120, 'Moh Rizal Kurniawan', '18099717', 'rizal.kuncunk@gmail.com', NULL, '$2y$12$nKABGnqx8tWPCpqiDGE2Cuh0InzlWKZ0BqWwQ037vzo7r.64rzkwi', 2, 1, 'JBT-05', 'DP-022', NULL, '0895927382', NULL, NULL, '2026-10-26 13:26:44', '2026-10-26 13:26:44');
INSERT INTO `users` VALUES (122, 'BUDI SETIYAWAN', '3119322', 'greenhouzy000@gmail.com', NULL, '$2y$12$DqdjBUjtfatzF99ROilel.bD8KnwO4OiK8jiLsqXWKYQ4ppEqt6Zi', 2, 1, 'JBT-07', 'DP-021', NULL, '+6282331709270', NULL, NULL, '2026-10-26 13:26:44', '2026-10-26 13:26:44');
INSERT INTO `users` VALUES (123, 'Bima Bakti Mandala', '15059822', 'bmbakti@gmail.com', NULL, '$2y$12$C1PagptgB8Sh4Q8yP2Jm/e9Dp/j/LnVeHfrU0MkTQvlOurw7kLOOG', 2, 1, 'JBT-05', 'DP-020', NULL, '085607185320', NULL, NULL, '2026-10-26 13:26:45', '2026-10-26 13:26:45');
INSERT INTO `users` VALUES (124, 'Noven Anggraini Permatasari', '19110021', 'novenanggrainiip@gmail.com', NULL, '$2y$12$TdzwiHl4zHq7gqgRQR//VO7LemoPH1.mfO8Smn5ktoEeSjCS.jch2', 2, 1, 'JBT-05', 'DP-022', NULL, '082298617397', NULL, NULL, '2026-10-26 13:26:45', '2026-10-26 13:26:45');
INSERT INTO `users` VALUES (125, 'Arum Laili Fitroh', '15129923', 'arum151299@gmail.com', NULL, '$2y$12$683lkcAWt/lzuExI5YPR2ODkSCIxcnuMY8srwQfYDR/hSFYcZfdoy', 2, 1, 'JBT-01', 'DP-001', NULL, '081358396114', NULL, NULL, '2026-10-26 13:26:45', '2026-10-26 13:26:45');
INSERT INTO `users` VALUES (126, 'Rus Sulistianingrum', '29096722', 'rurum2014@gmail.com', NULL, '$2y$12$KofZOuJpoku.md1rOJMQwu8cQGOKx9JZSUahtR4nNPVo0b8tYfEnC', 2, 1, 'JBT-05', 'DP-015', NULL, '081288139386', NULL, NULL, '2026-10-26 13:26:45', '2026-10-26 13:26:45');
INSERT INTO `users` VALUES (134, 'Fadilah Sarasuni', '18069923', 'fadilah.sarasuni18@gmail.com', NULL, '$2y$12$tS7DDq0ZO5YSBcFE1oJxy.aCc1KF371AvziX.HHUXjZU79OV.JjiW', 2, 1, 'JBT-05', 'DP-014', NULL, '082332521860', NULL, NULL, '2026-10-26 13:26:47', '2026-10-26 13:26:47');
INSERT INTO `users` VALUES (135, 'PURADI', '04097223', 'poraditukang@gmail.com', NULL, '$2y$12$q5w5ZLrPKc6PJCs2BLJlhOcFzIhAIBaihRm0QyJxPmoOu4sXuJV96', 2, 1, 'JBT-09', 'DP-021', NULL, '085655124393', NULL, NULL, '2026-10-26 13:26:47', '2026-10-26 13:26:47');
INSERT INTO `users` VALUES (138, 'ISMAIL', '20128023', '20128023@qhrm.institusi.ac.id', NULL, '$2y$12$mZ8DYPdjMVfmx/MwF3PDa.X3wFzsO6gGJDuxDkhvQkuyuMCP5V47W', 2, 1, 'JBT-09', 'DP-021', NULL, '(628) 2233-031660', NULL, NULL, '2026-10-26 13:26:47', '2026-10-26 13:26:47');
INSERT INTO `users` VALUES (140, 'SUPARDI', '13097522', 'Gakpunya.com', NULL, '$2y$12$0rctNWBGHLNg54zl6k/Z6eyC9Gibu.zThMmb1KPlHEDYii8O/2ssi', 2, 1, 'JBT-05', 'DP-017', NULL, '+6282132860309', NULL, NULL, '2026-10-26 13:26:48', '2026-10-26 13:26:48');
INSERT INTO `users` VALUES (144, 'Muhammad Cahyo Hermansyah', '04030123', 'cahyohermansyah1234@gmail.com', NULL, '$2y$12$sNsNabvaBzx1cmDxRYecHuZFay7e6OHakZSxydLXrb8SHLL7yyk5W', 2, 1, 'JBT-05', 'DP-008', NULL, '081337449334', NULL, NULL, '2026-10-26 13:26:49', '2026-10-26 13:26:49');
INSERT INTO `users` VALUES (147, 'Alfin Krisdianto', '20079823', 'alfinkrisdianto.co.id@gmail.com', NULL, '$2y$12$.E/QUzOaSMiS1WzhshKAROfaudh./udzp6fI5bTqdqJo0uYNTq8kG', 2, 1, 'JBT-05', 'DP-020', NULL, '0895364883189', NULL, NULL, '2026-10-26 13:26:49', '2026-10-26 13:26:49');
INSERT INTO `users` VALUES (167, 'ALMENDARIS SHANDY PRIYATAMA', '06100023', 'almenshandy@gmail.com', NULL, '$2y$12$IF7U9EQmG7ll2EwNvjrcs.JNLNjWIHMzReAvKR54YIpvf5IYsATW2', 1, 1, 'JBT-05', 'DP-020', NULL, '081240167405', NULL, NULL, '2026-10-26 13:26:53', '2026-10-26 13:27:53');
INSERT INTO `users` VALUES (171, 'BAGHAS WAHYU PANGESTU', '07070023', 'baghaswahyu1@gmail.com', NULL, '$2y$12$eIwtLE6OewTJtqPw/DBPauuzhyFubKvh4oLxteQG3aw66Dw4Gdhdi', 2, 1, 'JBT-05', 'DP-020', NULL, '082139312061', NULL, NULL, '2026-10-26 13:26:54', '2026-10-26 13:26:54');
INSERT INTO `users` VALUES (174, 'SONIA', '13129823', 'inajuarsonia@gmail.com', NULL, '$2y$12$5aSFgh5NvXEjkHYDyWQNhuC7zQYCuA8YAU5actWT0g4mnMwtJV6qe', 2, 1, 'JBT-05', 'DP-011', NULL, '085157998983', NULL, NULL, '2026-10-26 13:26:54', '2026-10-26 13:26:54');
INSERT INTO `users` VALUES (187, 'M. BADRUL QOMAR', '30089823', 'Badrulqmr4@gmail.com', NULL, '$2y$12$Ut7WQLOrwe4AKFz88yNW0uWT4dYOA59F8JsxSG65zxk.xpbVqbTMC', 2, 1, 'JBT-05', 'DP-014', NULL, '085707644415', NULL, NULL, '2026-10-26 13:26:57', '2026-10-26 13:26:57');
INSERT INTO `users` VALUES (207, 'ACHMAD ALDI FAHREZA ROHIMIN MUHTAR', '01029824', 'achmadaldifahreza@gmail.com', NULL, '$2y$12$FuDpHPoqABEc51ey9pjvx.Plr0zbKY/2TVhmJ3/b0HgMhzxq7qnaa', 2, 1, 'JBT-05', 'DP-004', NULL, '089677873666', NULL, NULL, '2026-10-26 13:27:01', '2026-10-26 13:27:01');
INSERT INTO `users` VALUES (217, 'Cellin Diva Handra Putri', '31050224', 'cellindivahandraputri@gmail.com', NULL, '$2y$12$UVT2WV4rxT3vrhNZg1Y85uxRPj09rRjzrdFfnKydowxRpzq3cw9Qu', 2, 1, 'JBT-05', 'DP-007', NULL, '089679849489', NULL, NULL, '2026-10-26 13:27:03', '2026-10-26 13:27:03');
INSERT INTO `users` VALUES (219, 'Yohanes Mario Wikenton', '15090124', 'yohanesmariowikenton@gmail.com', NULL, '$2y$12$h9V/ZfukewNj4zpZm9Pn4uXpc9QjC0DNrmlIy0V0hiuFPiKV6caU2', 2, 1, 'JBT-05', 'DP-006', NULL, '081236250689', NULL, NULL, '2026-10-26 13:27:03', '2026-10-26 13:27:03');
INSERT INTO `users` VALUES (222, 'USMAN', '15049024', 'usmangaya15@gmail.com', NULL, '$2y$12$HOWvUChxAbYLvTyad91y6Oyz3zN1k2lkczsOI69qS2NrLLsbtO24O', 2, 0, 'JBT-05', 'DP-017', NULL, '085257722509', NULL, NULL, '2026-10-26 13:27:03', '2026-10-27 15:59:04');
INSERT INTO `users` VALUES (225, 'Muhammad Faris Al-Adni', '04040024', 'faris@quantum-hrmi.com', NULL, '$2y$12$jNc4G3NQE.8sWlDxaN5abOo6NuflbzCEQMuC9.8xxf77x5tIDbhxO', 2, 1, 'JBT-05', 'DP-020', NULL, '085236736821', NULL, NULL, '2026-10-26 13:27:04', '2026-10-26 13:27:04');
INSERT INTO `users` VALUES (226, 'Muhammad Faisal Fanani', '25079624', 'muhammadff27@gmail.com', NULL, '$2y$12$2u6anSZEpmXAFy9yUrGQd.LIx95YOnFYrVc3O6zNWebXossE/9.hi', 2, 1, 'JBT-05', 'DP-020', NULL, '081294926990', NULL, NULL, '2026-10-26 13:27:04', '2026-10-26 13:27:04');
INSERT INTO `users` VALUES (228, 'Odetta Aulia Bulantrisna', '31109724', 'auliabulantrisna@gmail.com', NULL, '$2y$12$xCJkTzpiEDw7HHyR/1gGq.zwgI67t7B6y8SlBPKQ.Oar8QGDjxsoa', 2, 1, 'JBT-05', 'DP-011', NULL, '0895342000586', NULL, NULL, '2026-10-26 13:27:05', '2026-10-26 13:27:05');
INSERT INTO `users` VALUES (231, 'Bayu Pratama', '20040024', 'yutama277@gmail.com', NULL, '$2y$12$5XHAadOasWg7quDdPeeNCOqePM0GaV9RHJaTiXvpGP9K9DRPDRQNu', 2, 1, 'JBT-05', 'DP-006', NULL, '08819607203', NULL, NULL, '2026-10-26 13:27:05', '2026-10-26 13:27:05');
INSERT INTO `users` VALUES (232, 'Rachmad Andre Ramadhani', '12120024', 'ramadhani272829@gmail.com', NULL, '$2y$12$Mg6.lVVpdkdhERcgss/BzugBYOn3Ow0TSY41muBqJriQxtLF2xds.', 2, 1, 'JBT-05', 'DP-011', NULL, '083832717081', NULL, NULL, '2026-10-26 13:27:05', '2026-10-26 13:27:05');
INSERT INTO `users` VALUES (255, 'Dzaky Abdillah', '25070224', 'dzakyabdillah648@gmail.com', NULL, '$2y$12$AUWfFgWYEd9gk.X0IHmIK.gbChX.RCIvNJ.GCyGELsBiTuzaVM6RC', 2, 1, 'JBT-05', 'DP-008', NULL, '081939980421', NULL, NULL, '2026-10-26 13:27:10', '2026-10-26 13:27:10');
INSERT INTO `users` VALUES (258, 'Muhammad Pradana Yulianto', '24070224', 'mpradana200@gmail.com', NULL, '$2y$12$B8RcrM6J2cUFnnw630SLyu4eNJr/4Ea5nOhU0PY/RRUMxawEisjiu', 2, 1, 'JBT-05', 'DP-004', NULL, '083837766886', NULL, NULL, '2026-10-26 13:27:10', '2026-10-26 13:27:10');
INSERT INTO `users` VALUES (261, 'MUHAMMAD ATTAR RAMADHANI', '25110224', 'attarramadhani@gmail.com', NULL, '$2y$12$UXUJySLPjfqKAYCG2/Wtc.DRFmFvq.0LfUhN.B0rYNlGUe1zYpJvy', 2, 1, 'JBT-05', 'DP-020', NULL, '087855546300', NULL, NULL, '2026-10-26 13:27:11', '2026-10-26 13:27:11');
INSERT INTO `users` VALUES (276, 'ADELLIA FIRARINDRA CHRISTINE', '04050224', 'adelliafirarindra@gmail.com', NULL, '$2y$12$ix.dllrHNxu9SrNkj821keBDGiBdozRzf1YnIqQyr5r4cjjEMECvW', 2, 1, 'JBT-05', 'DP-007', NULL, '0881026587037', NULL, NULL, '2026-10-26 13:27:14', '2026-10-26 13:27:14');
INSERT INTO `users` VALUES (292, 'Vonny Rahmasari', '01010224', 'vonnyrahmasari112@gmail.com', NULL, '$2y$12$c/aanQrXxjIIMOF6UkPfM.rwz9YOPLULvStzodY6J487nWqFnxn4C', 2, 1, 'JBT-05', 'DP-007', NULL, '082264395826', NULL, NULL, '2026-10-26 13:27:17', '2026-10-26 13:27:17');
INSERT INTO `users` VALUES (294, 'Debby Ganesh Fazri Rosyady', '06080124', 'Debbyganesh46@gmail.com', NULL, '$2y$12$tu3XaCFYydvFkIvr5NPJ8e7LptZj2zIL.5u3KWzUw4oCiYeisvpHS', 2, 0, 'JBT-09', 'DP-007', NULL, '085646453561', NULL, NULL, '2026-10-26 13:27:17', '2026-10-27 15:58:03');
INSERT INTO `users` VALUES (299, 'Sofi Irmayanti', '11048725', 'sofiirmayanti812@gmail.com', NULL, '$2y$12$eBrOp5R.ZCXNhBAlbTXKEOFhcaRY4/8wtaZUJPtpssGd6la3hRtMm', 2, 1, 'JBT-05', 'DP-015', NULL, '082324645177', NULL, NULL, '2026-10-26 13:27:18', '2026-10-26 13:27:18');
INSERT INTO `users` VALUES (301, 'IMAM MUSLIH', '11128224', 'quantumfarmps@gmail.com', NULL, '$2y$12$3U07U0QxW34WJGgqinR4ke7rGIv7C3CvyHqAVHoDm/k2O2xMKBKty', 2, 1, 'JBT-04', 'DP-017', NULL, '082264098637', NULL, NULL, '2026-10-26 13:27:18', '2026-10-26 13:27:18');
INSERT INTO `users` VALUES (306, 'DWI YULIARDI', '01078825', 'Dwiyuliardi01@gmail.com', NULL, '$2y$12$BKj83aiVat3zNy14pJlP1OkOOh18GZKbHpCCWbgXV6aimYhangNIu', 2, 0, 'JBT-07', 'DP-021', NULL, '085225396150', NULL, NULL, '2026-10-26 13:27:19', '2026-10-27 15:58:12');
INSERT INTO `users` VALUES (307, 'HANIP DWI UNTORO', '15108925', 'hduntoro15@gmail.com', NULL, '$2y$12$7a4cefdo7MFZ6c8mMOcbnuJQsUV7a0ldJSzsVRGVA/B6b82lWcc4W', 2, 1, 'JBT-05', 'DP-017', NULL, '082115151537', NULL, NULL, '2026-10-26 13:27:20', '2026-10-26 13:27:20');
INSERT INTO `users` VALUES (308, 'PAYAKUN', '19107325', '19107325@qhrm.institusi.ac.id', NULL, '$2y$12$Yy22c.wa6EIwUbnDUWF16uUXRVtpJZwQLxNw9ANf9R5RsX0Q4TQ/q', 2, 1, 'JBT-08', 'DP-021', NULL, '', NULL, NULL, '2026-10-26 13:27:20', '2026-10-26 13:27:20');
INSERT INTO `users` VALUES (309, 'SITI HOTIJA', '02038026', '02038026@qhrm.institusi.ac.id', NULL, '$2y$12$2n1gghnz1vh4JX/srNMSEOiQ.IbLP2QuhhpGWNlSpzgFI/KMo91IK', 2, 0, 'JBT-08', 'DP-017', NULL, '', NULL, NULL, '2026-10-26 13:27:20', '2026-10-27 15:58:45');
INSERT INTO `users` VALUES (310, 'DIMAS KURNIA ROMO DHANI', '14120226', 'Dimaskurnia494@gmail.com', NULL, '$2y$12$96ajlX2dWChUeneJHKov5eF7s7.IUHLqZKXW/rPcYOk8v2wu4gHKG', 2, 1, 'JBT-07', 'DP-021', NULL, '082141344220', NULL, NULL, '2026-10-26 13:27:20', '2026-10-26 13:27:20');
INSERT INTO `users` VALUES (311, 'ZAHIDAH HANUM AL ZAHRA', '28020526', 'pinkuvoisu@gmail.com', NULL, '$2y$12$sZNBcP8rAxnoVV7JVazhLe/ftkUhA5B0s8WyNlQXwf489JwG.toDK', 2, 0, 'JBT-09', 'DP-020', NULL, '089520856521', NULL, NULL, '2026-10-26 13:27:20', '2026-10-27 15:59:18');
INSERT INTO `users` VALUES (312, 'IZZAN ITSNA RAFIF', '08020626', 'itsnarafif@gmail.com', NULL, '$2y$12$rGTgaPXABel6tgOL3wOHA.iIW4RdyWEM7LCy44dKh6fP7i6W8c47S', 2, 0, 'JBT-09', 'DP-020', NULL, '081332661099', NULL, NULL, '2026-10-26 13:27:21', '2026-10-27 15:58:24');
INSERT INTO `users` VALUES (313, 'M.Mu’Affa Aditya', '01120426', 'muaffaaditya88@gmail.com', NULL, '$2y$12$Gme23nePejXACFJ2kXepkeIzWWfZLglDmtA7CKMpOSe3JncuFyKEG', 2, 0, 'JBT-09', 'DP-020', NULL, '081249771960', NULL, NULL, '2026-10-26 13:27:21', '2026-10-27 15:58:27');
INSERT INTO `users` VALUES (314, 'Siti Khotimah', '14070326', 'sitikhotimah5486@gmail.com', NULL, '$2y$12$LYbZ8qyBSSlogixnn7dIbOJDLdRJjs63iHf4Dz9zLe.72aM0DuVkK', 2, 0, 'JBT-09', 'DP-020', NULL, '081357059461', NULL, NULL, '2026-10-26 13:27:21', '2026-10-27 15:58:50');
INSERT INTO `users` VALUES (315, 'PANDU ABIDIN', '01070626', 'lazyzen126@gmail.com', NULL, '$2y$12$8MWGoE5IlgjW355WzqiDUeASW8yoxnoYrUwTegVN2t4VzhPb8Cq6y', 2, 0, 'JBT-08', 'DP-017', NULL, '085806339546', NULL, NULL, '2026-10-26 13:27:21', '2026-10-27 15:58:37');
INSERT INTO `users` VALUES (316, 'TEBING RIZKY TSANIANSYAH', '27020626', 'tebingtsaniansyah56@gmail.com', NULL, '$2y$12$IwlSEBDQM/20LRhMZyY2ou.71dxjD1KkzrB8Ytl0TcWHapduIlRFi', 2, 0, 'JBT-09', 'DP-020', NULL, '081210926089', NULL, NULL, '2026-10-26 13:27:21', '2026-10-27 15:59:10');
INSERT INTO `users` VALUES (317, 'ARIS MUNANDAR', '07069226', 'arismunandar8280@gmail.com', NULL, '$2y$12$rsstWRsX6shuhf/CtEhLt.bsnKkf9e/nkp60DHFRTM3emg6i3KsSO', 2, 0, 'JBT-07', 'DP-021', NULL, '081324722346', NULL, NULL, '2026-10-26 13:27:22', '2026-10-27 15:59:40');
INSERT INTO `users` VALUES (318, 'Faniel Johana Leda', '29010326', 'annaworks2913@gmail.com', NULL, '$2y$12$IBR/ENDBUKoRDxdtzXoBpOsj.lAdkyjPT18IchME7PH.C4DrKcHGO', 2, 1, 'JBT-09', 'DP-022', NULL, '081237681644', NULL, NULL, '2026-10-26 13:27:22', '2026-10-26 13:27:22');
INSERT INTO `users` VALUES (319, 'SUDOTO', '05088726', 'elqordhowiedot@gmail.com', NULL, '$2y$12$qUsCIqA2JXktm5suEELy6eJ6xJvFAGPcHDsJW4Zq8cELco.DovhOC', 2, 1, 'JBT-05', 'DP-015', NULL, '085715234596', NULL, NULL, '2026-10-26 13:27:22', '2026-10-26 13:27:22');

-- ----------------------------
-- Table structure for whatsapp_bulk_batches
-- ----------------------------
DROP TABLE IF EXISTS `whatsapp_bulk_batches`;
CREATE TABLE `whatsapp_bulk_batches`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `request_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `provider` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `interval_seconds` tinyint UNSIGNED NOT NULL,
  `total_messages` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `include_credentials` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `whatsapp_bulk_batches_request_id_unique`(`request_id` ASC) USING BTREE,
  INDEX `whatsapp_bulk_batches_user_id_created_at_index`(`user_id` ASC, `created_at` ASC) USING BTREE,
  CONSTRAINT `whatsapp_bulk_batches_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of whatsapp_bulk_batches
-- ----------------------------
INSERT INTO `whatsapp_bulk_batches` VALUES (2, '2e844847-280a-4656-9ae6-745fdf5847a6', 1, 'flowkirim', 10, 2, '2026-11-12 15:33:50', '2026-11-12 15:33:50', 1);

-- ----------------------------
-- Table structure for whatsapp_bulk_dispatch_state
-- ----------------------------
DROP TABLE IF EXISTS `whatsapp_bulk_dispatch_state`;
CREATE TABLE `whatsapp_bulk_dispatch_state`  (
  `id` tinyint UNSIGNED NOT NULL,
  `next_allowed_at_ms` bigint UNSIGNED NOT NULL DEFAULT 0,
  `reservation_token` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of whatsapp_bulk_dispatch_state
-- ----------------------------
INSERT INTO `whatsapp_bulk_dispatch_state` VALUES (1, 1794472466308, NULL);

-- ----------------------------
-- Table structure for whatsapp_bulk_messages
-- ----------------------------
DROP TABLE IF EXISTS `whatsapp_bulk_messages`;
CREATE TABLE `whatsapp_bulk_messages`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `batch_id` bigint UNSIGNED NOT NULL,
  `booking_id` bigint UNSIGNED NULL DEFAULT NULL,
  `topic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipient` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'queued',
  `error` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `attempted_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `account_credentials` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `whatsapp_bulk_messages_batch_id_booking_id_recipient_unique`(`batch_id` ASC, `booking_id` ASC, `recipient` ASC) USING BTREE,
  INDEX `whatsapp_bulk_messages_booking_id_foreign`(`booking_id` ASC) USING BTREE,
  INDEX `whatsapp_bulk_messages_batch_id_status_index`(`batch_id` ASC, `status` ASC) USING BTREE,
  CONSTRAINT `whatsapp_bulk_messages_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `whatsapp_bulk_batches` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `whatsapp_bulk_messages_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `zoom_bookings` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of whatsapp_bulk_messages
-- ----------------------------
INSERT INTO `whatsapp_bulk_messages` VALUES (3, 2, 30, 'Sertifikasi Estate Management Batch 24', '6287855546300', 'Undangan Zoom Meeting\nAgenda: Sertifikasi Estate Management Batch 24\nBooking atas nama: Rachmad Andre Ramadhani\nProyek: Tes Daring Sertifikasi Person Quantum HRM Internasional\nHari/Tanggal: Sabtu, 21 November 2026\nWaktu: 08:00 - 18:00 WIB\n\nJoin Zoom Meeting:\nhttps://us06web.zoom.us/j/84245419344?pwd=IZukwof1Ccb2Lfod9SYbvD0hCBMbMe.1\nMeeting ID: 84245419344\nPasscode: hNJTZp', 'sent', NULL, '2026-11-12 15:33:51', '2026-11-12 15:33:58', '2026-11-12 15:33:50', '2026-11-12 15:33:58', 'eyJpdiI6Im9GM3NIU052R3dURkowMjMvZWMyRWc9PSIsInZhbHVlIjoibjNmYU5pWmt1UXlOUGc5TStVWmtFOUpXTmMxTHNwblRlVDBXTmpFczBQenJMcElVcW13NDV0cldwaEoyUXpXM0NKanFGaU1NTS9VK0lnSWpUdHB5Y2FEa2hvZUZnT1pXOU5sbVBucWxvN0JhakdMU2w1b0Vxcy85OFRYcm0xTEkzSXJCRFQ3bmd0R3Q4NXd1STVzQ1NqRnRUeWhibDd4b1U0WGhzQlA1aE1NPSIsIm1hYyI6ImQxMGI3ZWNlNzViMTkyZjZjNDc5MmY3NGJjYzExOGYwYzQzNjY2MDkxZmRjYzQ1NzJiZDM0Y2JjMjI2YTVkYWMiLCJ0YWciOiIifQ==');
INSERT INTO `whatsapp_bulk_messages` VALUES (4, 2, 31, 'Sertifikasi Public Speaker Batch 5', '6287855546300', 'Undangan Zoom Meeting\nAgenda: Sertifikasi Public Speaker Batch 5\nBooking atas nama: Rachmad Andre Ramadhani\nProyek: Tes Daring Sertifikasi Person Quantum HRM Internasional\nHari/Tanggal: Sabtu, 21 November 2026\nWaktu: 08:00 - 18:00 WIB\n\nJoin Zoom Meeting:\nhttps://us06web.zoom.us/j/84118469395?pwd=rrUqvNXh24uOZ86zTNjREtdr6yaOnb.1\nMeeting ID: 84118469395\nPasscode: 2HnC3r', 'sent', NULL, '2026-11-12 15:34:08', '2026-11-12 15:34:16', '2026-11-12 15:33:50', '2026-11-12 15:34:16', 'eyJpdiI6InltRU9JTktLMDhVSUI0VjFoYXcwaFE9PSIsInZhbHVlIjoiUGtYZ2Z2YWQ1Uk00bUFVSUtiUG41UWJrZjNqUU4vSC81dlNaOWMxazJ4aWhmdGVGMnM1TGZMZjY4dTlpOFpHOWZzNzVSNkZzZE1NRGZwczU2d0VqUVlnZitVbitYV1JFSzJpSkswQzVlYVV2em9aOEdYNk5kZlladnRrSmluYnkzK2Z6RGNUSmcrVmlub0FkcFN0N09TRnRPRnhPV2FYamhVdS9vYkEwMVdVPSIsIm1hYyI6IjU5NWVjYTEzMjk3YjlhOGE4ZDQ5NTI5YjdjZGM0ZTI2ZWIxNTFjY2JjMzE2N2JmZDExNWFiMmU2ZDBlMGM0YzAiLCJ0YWciOiIifQ==');

-- ----------------------------
-- Table structure for zoom_accounts
-- ----------------------------
DROP TABLE IF EXISTS `zoom_accounts`;
CREATE TABLE `zoom_accounts`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Label/Nama Akun Zoom, misal: Akun 01 - 500 Peserta',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `capacity` int NOT NULL DEFAULT 100 COMMENT 'Kapasitas peserta',
  `expired_date` date NULL DEFAULT NULL,
  `password_hotmail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `account_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `client_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `client_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `secret_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `verification_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `status` enum('active','inactive','maintenance') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `zoom_accounts_email_unique`(`email` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 50 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of zoom_accounts
-- ----------------------------
INSERT INTO `zoom_accounts` VALUES (3, 'Akun Zoom (quantum.hrmi1)', 'quantum.hrmi1@hotmail.com', '1Sampai9', 100, '2025-08-29', 'Zoom12345', 'yQcpBomrTTWqkMOUm484Dg', 'fPW52DgUTxeoHBoeJ9ezg', 'dV239CGzp13hegrbcCZqjjpAtTlxqft3', NULL, NULL, 'inactive', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (4, 'Akun Zoom (quantum.hrmi2)', 'quantum.hrmi2@hotmail.com', '1Sampai9', 100, '2024-03-02', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'active', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (5, 'Akun Zoom (quantum.hrmi3)', 'quantum.hrmi3@hotmail.com', '1Sampai9', 100, '2024-08-29', 'Zoom12345', 'qQuCPaAXRFK5Ut4yzHTFcg', 'V_J9T44XQCGqWRmoqiM4xw', 'N9MY1p3AyBhX7DPfUSReGcR1oQ4q1zGA', NULL, NULL, 'inactive', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (6, 'Akun Zoom (quantum.hrmi4)', 'quantum.hrmi4@hotmail.com', 'Jpt15032024', 100, '2024-03-20', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'active', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (7, 'Akun Zoom (quantum.hrmi5)', 'quantum.hrmi5@hotmail.com', 'Mascahyo185', 100, '2024-03-20', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'active', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (8, 'Akun Zoom (quantum.hrmi6)', 'quantum.hrmi6@hotmail.com', '21ZoomQuntum', 100, '2024-08-29', 'Zoom12345', '7nDsNgLzRZe5O_chBl0qTQ', 'G9LPehgzSXKFyvqYddJ_Fg', '72CO4Xzmd52vG2bNeBDr6vkhlBakHuYg', NULL, NULL, 'inactive', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (9, 'Akun Zoom (quantum.hrmi7)', 'quantum.hrmi7@hotmail.com', 'Produksi25', 100, '2024-09-15', 'Zoom12345', '0T05jbdRROSutx_8Ztjbow', 'Dj3MveWoQea7YN3xVN7Stw', 'tQwjXQTc9SEAGVTZ9aYJZSGJC4LRmvoa', NULL, NULL, 'inactive', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (10, 'Akun Zoom (quantum.hrmi8)', 'quantum.hrmi8@hotmail.com', '1Sampai9', 100, '2026-09-30', 'Zoom12345', 'aECO6hqmSkW_cVX8p8vnog', 'vUY25ZdTgGrMwCw9_rIg', 'au4WFmLM92hgUbTjETDrOMUq5o2i8c24', NULL, NULL, 'inactive', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (11, 'Akun Zoom (quantum.hrmi9)', 'quantum.hrmi9@hotmail.com', '1Sampai9', 100, '2026-11-28', 'Zoom12345', 'YJx4q75ESPyT1e5vaq7ezQ', 'qz1SFpCMSc6Y3a9_HQDocw', 'zoizkrkN2aWoU1iMVhDEaYAb7ZS8xyx4', NULL, NULL, 'active', '2026-10-26 10:31:23', '2026-10-28 18:32:02');
INSERT INTO `zoom_accounts` VALUES (12, 'Akun Zoom (quantum.hrmi10)', 'quantum.hrmi10@hotmail.com', '9Sampai1', 100, '2026-11-21', 'Zoom12345', 'CJONc3IcQ_u9BC78F0PxVw', 'RQ2OUfZRzK95no_bwp6Fw', 'wUWcSCImcWKsucWR1x44WE4X8ArYcEc1', NULL, NULL, 'active', '2026-10-26 10:31:23', '2026-11-12 15:32:51');
INSERT INTO `zoom_accounts` VALUES (13, 'Akun Zoom (quantum.hrmi11)', 'quantum.hrmi11@hotmail.com', '1Sampai9', 100, '2026-12-21', 'Zoom12345', 'CATdMYr4QqKe65DuVZzqvA', '1lPk9qweSyqtD93zausckA', 'GTZMGyNTmAmvnD24EQxpBumB3gEiazxl', NULL, NULL, 'active', '2026-10-26 10:31:23', '2026-11-22 14:33:51');
INSERT INTO `zoom_accounts` VALUES (14, 'Akun Zoom (quantum.hrmi12)', 'quantum.hrmi12@hotmail.com', '1Sampai9', 100, '2024-01-02', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'active', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (15, 'Akun Zoom (quantum.hrmi13)', 'quantum.hrmi13@hotmail.com', '1Sampai9', 100, '2024-01-02', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'active', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (16, 'Akun Zoom (quantum.hrmi14)', 'quantum.hrmi14@hotmail.com', '1Sampai9', 100, '2024-01-02', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'active', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (17, 'Akun Zoom (quantum.hrmi15)', 'quantum.hrmi15@hotmail.com', '1Sampai9', 100, '2023-10-04', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (18, 'Akun Zoom (quantum.hrmi16)', 'quantum.hrmi16@hotmail.com', '1Sampai9', 100, '2023-10-04', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (19, 'Akun Zoom (quantum.hrmi17)', 'quantum.hrmi17@hotmail.com', '1Sampai9', 100, '2023-10-04', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (20, 'Akun Zoom (quantum.hrmi18)', 'quantum.hrmi18@hotmail.com', '1Sampai9', 100, '2023-10-04', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (21, 'Akun Zoom (quantum.hrmi19)', 'quantum.hrmi19@hotmail.com', '1Sampai9', 100, '2023-10-04', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (22, 'Akun Zoom (mitra.zoom1)', 'mitra.zoom1@hotmail.com', 'Mascahyo18', 500, '2026-12-08', 'Zoom12345', 'Qp_mGiQqTUie-gW1dcUrWA', '1zryKdM6QgimmCNb0zS1xA', '7Piylcfgv5dS31vjXjkIXBhIY4tWTCqm', NULL, NULL, 'active', '2026-10-26 10:31:23', '2026-11-09 13:22:59');
INSERT INTO `zoom_accounts` VALUES (23, 'Akun Zoom (eritamarisdianti96)', 'eritamarisdianti96@hotmail.com', 'Mbak_Sonya_04', 500, '2024-07-25', 'Zoom12345', 'R2Y6WElMSzC28HHpQXLoZQ', 'lG1t3MauQCGhewZjU8yPtQ', 'dTI1LJFKisDYiNiJBxrjKTBxecV5aFeB', NULL, NULL, 'inactive', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (24, 'Akun Zoom (hrm.quantum1)', 'hrm.quantum1@hotmail.com', '9Sampai1', 500, '2025-09-10', 'Zoom12345', 'WQd1pgLESpOaE-m1sMK3ww', 'aOu4odWzS7WaqQMhgkruCw', 'q0YfP7NA6QBuKW6zh75AbpOx6a60rAt3', NULL, NULL, 'inactive', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (25, 'Akun Zoom (hrm.quantum2)', 'hrm.quantum2@hotmail.com', '1Sampai9', 500, '2025-09-28', 'Zoom12345', 'LMDHXGd4Qo6YbNKwaFuXTw', '0Hj66FwTROsgJ6qdL3Gyg', 'G020Atjq1IKMzxkfJE06V9ZQurYm2Wn1', NULL, NULL, 'inactive', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (26, 'Akun Zoom (hrm.quantum3)', 'hrm.quantum3@hotmail.com', '1Sampai9', 500, '2025-07-21', 'Zoom12345', 'QMNR7pzCSYatUIBP4ZCdKw', 'NCHVDhtsT0CfsgsWjnJ5mw', 'f5Um1ac3Ban7RM3Wb8lMNa97i7IwnyM9', NULL, NULL, 'inactive', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (27, 'Akun Zoom (hrm.quantum4)', 'hrm.quantum4@hotmail.com', '1Sampai9', 500, '2024-07-25', 'Zoom12345', 'zMEFrmPuQemNcDaFI46jCg', 'BAJCVrNxRvyeIYf3KqxXdw', '5eeYRG0qIkbgGo94CdQHX2NRmxRw6ZJx', NULL, NULL, 'inactive', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (28, 'Akun Zoom (hrm.quantum5)', 'hrm.quantum5@hotmail.com', '1Sampai9', 500, '2024-07-02', 'Zoom12345', '7MdvhrMtQn-oCJWRG_2xVQ', 'ZfUe8beOS6S2OUMPVgc5Bg', 'tYhSZ9ghRZ58tQc2uLKEUo5p6I9sm0zq', NULL, NULL, 'inactive', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (29, 'Akun Zoom (hrm.quantum6)', 'hrm.quantum6@hotmail.com', '1Sampai9', 100, '2024-05-27', 'Zoom12345', 'c0ckyg-8TmOp2LabKTTRXg', '3lHmkuJeRCGWjB0Nbd5Lzw', 'Y4p0QJeyqPDgvW55agAQHJQ8zEuR8J57', NULL, NULL, 'inactive', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (30, 'Akun Zoom (hrm.quantum7)', 'hrm.quantum7@hotmail.com', '1Sampai9', 100, '2023-10-06', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (31, 'Akun Zoom (hrm.quantum8)', 'hrm.quantum8@hotmail.com', '1Sampai9', 100, '2023-10-06', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (32, 'Akun Zoom (hrm.quantum9)', 'hrm.quantum9@hotmail.com', '1Sampai9', 100, '2023-10-06', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (33, 'Akun Zoom (hrm.quantum10)', 'hrm.quantum10@hotmail.com', '1Sampai9', 100, '2023-10-06', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (34, 'Akun Zoom (hrm.quantum11)', 'hrm.quantum11@hotmail.com', '1Sampai9', 100, '2023-10-06', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (35, 'Akun Zoom (hrm.quantum12)', 'hrm.quantum12@hotmail.com', '1Sampai9', 100, '2023-10-06', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (36, 'Akun Zoom (hrm.quantum13)', 'hrm.quantum13@hotmail.com', '1Sampai9', 100, '2023-10-06', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (37, 'Akun Zoom (hrm.quantum14)', 'hrm.quantum14@hotmail.com', '1Sampai9', 100, '2023-10-06', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (38, 'Akun Zoom (hrm.quantum15)', 'hrm.quantum15@hotmail.com', '1Sampai9', 100, '2023-10-06', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (39, 'Akun Zoom (hrm.quantum17)', 'hrm.quantum17@hotmail.com', '1Sampai9', 100, '2023-10-06', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (40, 'Akun Zoom (hrm.quantum18)', 'hrm.quantum18@hotmail.com', '1Sampai9', 100, '2023-10-06', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (41, 'Akun Zoom (hrm.quantum19)', 'hrm.quantum19@hotmail.com', '1Sampai9', 100, '2023-10-06', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (42, 'Akun Zoom (hrm.quantum20)', 'hrm.quantum20@hotmail.com', '1Sampai9', 100, '2023-10-06', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (43, 'Akun Zoom (hrm.quantum21)', 'hrm.quantum21@hotmail.com', '1Sampai9', 100, '2023-10-06', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (44, 'Akun Zoom (hrm.quantum22)', 'hrm.quantum22@hotmail.com', '1Sampai9', 100, '2023-10-06', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (45, 'Akun Zoom (hrm.quantum23)', 'hrm.quantum23@hotmail.com', '1Sampai9', 100, '2023-01-15', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (46, 'Akun Zoom (hrm.quantum24)', 'hrm.quantum24@hotmail.com', '1Sampai9', 100, '2023-01-15', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (47, 'Akun Zoom (hrm.quantum25)', 'hrm.quantum25@hotmail.com', '1Sampai9', 100, '2023-01-15', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (48, 'Akun Zoom (hrm.quantum26)', 'hrm.quantum26@hotmail.com', '1Sampai9', 100, '2023-01-16', 'Zoom12345', NULL, NULL, NULL, NULL, NULL, 'maintenance', '2026-10-26 10:31:23', '2026-10-26 10:31:23');
INSERT INTO `zoom_accounts` VALUES (49, 'Akun Zoom (quantum.utama1)', 'quantum.utama1@hotmail.com', '1Sampai9', 1000, '2025-09-28', '1Sampai9', 'p6j9EELcTkarvoSgDIzEWg', 'hle0hQTTTMI9Hkin9RZCA', 'pGcU77IprxdmirQ0PtSMfFxlwLBugf4o', NULL, NULL, 'inactive', '2026-10-26 10:31:23', '2026-10-26 10:31:23');

-- ----------------------------
-- Table structure for zoom_bookings
-- ----------------------------
DROP TABLE IF EXISTS `zoom_bookings`;
CREATE TABLE `zoom_bookings`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `zoom_account_id` bigint UNSIGNED NULL DEFAULT NULL,
  `topic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Nama / Kode Proyek khusus multi-hari (Opsional)',
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `start_time` time NULL DEFAULT NULL,
  `end_time` time NULL DEFAULT NULL,
  `duration_minutes` int NOT NULL DEFAULT 60,
  `day_count` int NOT NULL DEFAULT 1,
  `days_detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'JSON / list hari kegiatan',
  `status` tinyint NOT NULL DEFAULT 0 COMMENT '0: Pending, 1: Approved/ACC, 2: Rejected, 3: Executed, 4: Completed, 5: Cancelled',
  `admin_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `user_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `zoom_meeting_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `join_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `start_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `passcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `project_id` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `zoom_bookings_slug_unique`(`slug` ASC) USING BTREE,
  INDEX `zoom_bookings_user_id_foreign`(`user_id` ASC) USING BTREE,
  INDEX `zoom_bookings_zoom_account_id_foreign`(`zoom_account_id` ASC) USING BTREE,
  INDEX `zoom_bookings_project_id_foreign`(`project_id` ASC) USING BTREE,
  CONSTRAINT `zoom_bookings_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `zoom_bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `zoom_bookings_zoom_account_id_foreign` FOREIGN KEY (`zoom_account_id`) REFERENCES `zoom_accounts` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 45 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of zoom_bookings
-- ----------------------------
INSERT INTO `zoom_bookings` VALUES (7, 15, 12, 'Kick Of Meeting Pelaksanaan Jasa Konsultan HR Profesional Penilaian dan Rekrutmen Staf Inti PR untuk Transisi GC7 Menuju GC8 di PR Konsorsium Komunitas Penabulu-STPI', 'Meeting Eksternal', 'kick-of-meeting-pelaksanaan-jasa-konsultan-hr-profesional-penilaian-dan-rekrutmen-staf-inti-pr-untuk-transisi-gc7-menuju-gc8-di-pr-konsorsium-komunitas-penabulu-stpi-80pfla', 'Pelaksanaan Jasa Konsultan HR Profesional Penilaian dan Rekrutmen Staf Inti PR untuk Transisi GC7 Menuju GC8 di PR Konsorsium Komunitas Penabulu-STPI', '2026-10-28', '2026-10-28', '16:00:00', '21:00:00', 300, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '89574924310', 'https://us06web.zoom.us/j/89574924310?pwd=ZypojwWelGMNPaSN0dpdbvyDERBPFR.1', 'https://us06web.zoom.us/s/89574924310?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4OTU3NDkyNDMxMCIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiMGJqYW1INm1UM1dwVjFWWU9MXzBZUSIsInppZCI6IjhiNmJjZDFlZWU4ZTRkNjI4MTZmMDU3MzAxNGU0NmYzIiwic2siOiI1ODM0MTUwNzU2MTEyNDI3ODA5Iiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTMxNjM4NjgsImlhdCI6MTc5MzE1NjY2OCwiYWlkIjoiQ0pPTmMzSWNRX3U5QkM3OEYwUHhWdyIsImNpZCI6IiJ9.1_LOVeBbPISCLPpn_lr-ni_7aPsb2a-wnQ0KuRFuJlc', 'FXn9MC', '2026-10-28 10:03:30', '2026-11-02 09:38:43', 8);
INSERT INTO `zoom_bookings` VALUES (8, 21, 12, 'Surveilen 1 - 29 Oktober 2026', 'Meeting Internal', 'surveilen-1-29-oktober-2026-4gyg5z', NULL, '2026-10-29', '2026-10-29', '09:00:00', '17:00:00', 480, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '87339545132', 'https://us06web.zoom.us/j/87339545132?pwd=f6GoqSJHd7bSOoGLYJ86J6RfiE3uZr.1', 'https://us06web.zoom.us/s/87339545132?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4NzMzOTU0NTEzMiIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiMGJqYW1INm1UM1dwVjFWWU9MXzBZUSIsInppZCI6IjkwZDYxOTEwODc4MTQ4MGE5YjFiMDllZWFkN2IzMjhjIiwic2siOiI1ODM0MTUwNzU2MTEyNDI3ODA5Iiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTMyNDI4NDQsImlhdCI6MTc5MzIzNTY0NCwiYWlkIjoiQ0pPTmMzSWNRX3U5QkM3OEYwUHhWdyIsImNpZCI6IiJ9.iz5qMIQ9IY799hvdcofrRqLk4p8lrABn_ctwN-fGPKk', '89WpZ7', '2026-10-29 08:00:33', '2026-11-02 09:38:29', 7);
INSERT INTO `zoom_bookings` VALUES (9, 15, 11, 'Kick Of Meeting Pelaksanaan Jasa Konsultan HR Profesional Penilaian dan Rekrutmen Staf Inti PR untuk Transisi GC7 Menuju GC8 di PR Konsorsium Komunitas Penabulu-STPI', 'Meeting Eksternal', 'kick-of-meeting-pelaksanaan-jasa-konsultan-hr-profesional-penilaian-dan-rekrutmen-staf-inti-pr-untuk-transisi-gc7-menuju-gc8-di-pr-konsorsium-komunitas-penabulu-stpi-1xol3j', NULL, '2026-10-29', '2026-10-29', '16:00:00', '21:00:00', 300, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '84337538472', 'https://us02web.zoom.us/j/84337538472?pwd=9MzESgfNfA1xhcraSvortkvAeDMc5q.1', 'https://us02web.zoom.us/s/84337538472?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4NDMzNzUzODQ3MiIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiTWJiMklabmdRejJlWVJJYlJVVDE3USIsInppZCI6ImU3YWE4MmE5NGU0NjRjNjRiMjRlYTFjMTAwODYyNzZmIiwic2siOiI5MDIwNzM0Nzc3NTQ1NjQxNzU4Iiwic3R5IjoxMDAsIndjZCI6InVzMDIiLCJleHAiOjE3OTMyNDMxMjcsImlhdCI6MTc5MzIzNTkyNywiYWlkIjoiWUp4NHE3NUVTUHlUMWU1dmFxN2V6USIsImNpZCI6IiJ9.VhE3HjYKuQuer5Ewp-NmT6SzJ9kdb8y055MtIbJX5Hc', '96Sbbi', '2026-10-29 08:05:07', '2026-11-02 09:38:37', 8);
INSERT INTO `zoom_bookings` VALUES (10, 120, 11, 'Psikotes dan Wawancara Kandidat Karyawan PT. Kimpai Dyna Tube', 'Lisa Mitra Mandiri', 'psikotes-dan-wawancara-kandidat-karyawan-pt-kimpai-dyna-tube-bijtan', NULL, '2026-10-30', '2026-10-30', '08:00:00', '17:00:00', 540, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '83311922296', 'https://us02web.zoom.us/j/83311922296?pwd=Wv7ywbzPgGoNZFNplE4bGruhsIfpV3.1', 'https://us02web.zoom.us/s/83311922296?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4MzMxMTkyMjI5NiIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiTWJiMklabmdRejJlWVJJYlJVVDE3USIsInppZCI6IjAyNmM5ZjE4YjU4NzRkNGNiNDY5NGQ4ZjU2N2NjOThjIiwic2siOiI5MDIwNzM0Nzc3NTQ1NjQxNzU4Iiwic3R5IjoxMDAsIndjZCI6InVzMDIiLCJleHAiOjE3OTMzMjg4NDUsImlhdCI6MTc5MzMyMTY0NSwiYWlkIjoiWUp4NHE3NUVTUHlUMWU1dmFxN2V6USIsImNpZCI6IiJ9.BxTUg02XIu60OWBP_KoiJ20a5Wnyh6KwKSvbDVuBG3A', 'WAdPUD', '2026-10-30 07:53:57', '2026-10-30 07:54:05', 6);
INSERT INTO `zoom_bookings` VALUES (11, 228, 12, 'Meeting Tim Sertifikasi', 'Meeting Internal', 'meeting-tim-sertifikasi-jbxqvi', NULL, '2026-11-02', '2026-11-02', '10:30:00', '21:00:00', 630, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '81776882295', 'https://us06web.zoom.us/j/81776882295?pwd=Tge8lirKTYurCBZavQMX6YqeZScr9l.1', 'https://us06web.zoom.us/s/81776882295?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4MTc3Njg4MjI5NSIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiMGJqYW1INm1UM1dwVjFWWU9MXzBZUSIsInppZCI6ImY4YzgxOWVhZTkxMzQxYmViOWVhNjUyODRhMjg1YjMzIiwic2siOiI1ODM0MTUwNzU2MTEyNDI3ODA5Iiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTM1OTQzNzgsImlhdCI6MTc5MzU4NzE3OCwiYWlkIjoiQ0pPTmMzSWNRX3U5QkM3OEYwUHhWdyIsImNpZCI6IiJ9.M0byBQHGWJbA20s0Qkyk_thr6ze1Hgc209iTskgq9Jo', '56zGHd', '2026-11-02 09:39:23', '2026-11-02 09:39:37', 7);
INSERT INTO `zoom_bookings` VALUES (12, 144, 13, 'Asesmen Potensi Level Jabatan Staf/BOD-4 di Lingkungan PG Rajawali II Tahun 2026', 'Assessment Center Quantum HRM Internasional', 'asesmen-potensi-level-jabatan-stafbod-4-di-lingkungan-pg-rajawali-ii-tahun-2026-vsnpai', 'Kode Proyek: PR-A-422', '2026-11-03', '2026-11-03', '08:00:00', '19:00:00', 660, 1, NULL, 1, 'Disetujui otomatis oleh Administrator saat pengajuan.', 'Akun dipinjam jadi tidak ada pembuatan link di sistem', NULL, NULL, NULL, NULL, '2026-11-02 13:49:54', '2026-11-04 09:37:05', 9);
INSERT INTO `zoom_bookings` VALUES (13, 232, 12, 'Sertifikasi Auditor Mutu Internal Batch 92', 'Tes Daring Sertifikasi Person Quantum HRM Internasional', 'sertifikasi-auditor-mutu-internal-batch-92-jzn4bo', NULL, '2026-11-07', '2026-11-07', '08:00:00', '21:00:00', 780, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '84310637429', 'https://us06web.zoom.us/j/84310637429?pwd=OChiPAodj8ixoBCmaFOKmaX50AFasS.1', 'https://us06web.zoom.us/s/84310637429?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4NDMxMDYzNzQyOSIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiMGJqYW1INm1UM1dwVjFWWU9MXzBZUSIsInppZCI6ImQ0NTY5MzA1N2EyYjQ1ZmJhZGQxMjdjOWJlNGVjY2I4Iiwic2siOiI1ODM0MTUwNzU2MTEyNDI3ODA5Iiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTM3MDAxMjMsImlhdCI6MTc5MzY5MjkyMywiYWlkIjoiQ0pPTmMzSWNRX3U5QkM3OEYwUHhWdyIsImNpZCI6IiJ9.flEMsU1xSR1STKK43HYRGwbT5dvEcNii1_Z9mcH7fLA', 'paSZXb', '2026-11-03 15:01:49', '2026-11-04 09:37:26', 10);
INSERT INTO `zoom_bookings` VALUES (14, 15, 12, 'Diskusi Quantum dan Kemenhub', 'Meeting Eksternal', 'diskusi-quantum-dan-kemenhub-p3tc1g', NULL, '2026-11-04', '2026-11-04', '11:00:00', '21:00:00', 600, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '83812359714', 'https://us06web.zoom.us/j/83812359714?pwd=diyFhoIfGPlY9YvmWjsljOrEIE1oaF.1', 'https://us06web.zoom.us/s/83812359714?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4MzgxMjM1OTcxNCIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiMGJqYW1INm1UM1dwVjFWWU9MXzBZUSIsInppZCI6IjlkMjgxMzc4OTNmMzRhZTRhNTdiNjM1ZTljMzBlYTM3Iiwic2siOiI1ODM0MTUwNzU2MTEyNDI3ODA5Iiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTM3MDY0NDgsImlhdCI6MTc5MzY5OTI0OCwiYWlkIjoiQ0pPTmMzSWNRX3U5QkM3OEYwUHhWdyIsImNpZCI6IiJ9.GAxhRNhzNM2H1_l7U6Nr7xprpLWDkrOTQ3z6I_JSAcA', 'PZ6Lph', '2026-11-03 16:31:25', '2026-11-03 16:47:23', 8);
INSERT INTO `zoom_bookings` VALUES (15, 21, 22, 'Webinar Hari Pahlawan: \"Wawasan Kebangsaan dan Bela Negara Momentum Memperkuat Nasionalisme dan Mengisi Mindset Karakter Bangsa\"', 'Webinar Pelatihan', 'webinar-hari-pahlawan-wawasan-kebangsaan-dan-bela-negara-momentum-memperkuat-nasionalisme-dan-mengisi-mindset-karakter-bangsa-tqfjev', NULL, '2026-11-10', '2026-11-10', '08:30:00', '18:00:00', 570, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '86307557736', 'https://us06web.zoom.us/j/86307557736?pwd=lSXI1h6crOfQWu97qPZ0EOJ3O5ZOpz.1', 'https://us06web.zoom.us/s/86307557736?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4NjMwNzU1NzczNiIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoibFpMVWFvQ0dRR1dta29McGhaX3QydyIsInppZCI6IjliNDg5MTVhMGQ5MDRiZmE4ZTgyNzFmZDc1OGIyYjkxIiwic2siOiI0MTUyNTc3OTg2ODIyNTk2NTkxIiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTM3NjgwNTEsImlhdCI6MTc5Mzc2MDg1MSwiYWlkIjoiUXBfbUdpUXFUVWllLWdXMWRjVXJXQSIsImNpZCI6IiJ9.LLRg5hzI4MsybI-tQvcLfOBoKmy80L7c1ooOa0akHD0', '3vHdfa', '2026-11-04 09:20:01', '2026-11-04 09:54:06', 11);
INSERT INTO `zoom_bookings` VALUES (16, 144, 13, 'Meeting Akhir Asesor_Asesmen PT PG Rajawali I_BOD-4_4.11.2026', 'Assessment Center Quantum HRM Internasional', 'meeting-akhir-asesor-asesmen-pt-pg-rajawali-i-bod-4-4112026-ox15lo', 'Kode Proyek: PR-A-422', '2026-11-04', '2026-11-04', '08:00:00', '17:00:00', 540, 1, NULL, 1, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, NULL, NULL, NULL, NULL, '2026-11-04 09:23:22', '2026-11-04 09:36:07', 9);
INSERT INTO `zoom_bookings` VALUES (17, 232, 12, 'Ujian susulan dan Remedial Operator X-Ray Fluorescence Spectrometry Batch 11', 'Tes Daring Sertifikasi Person Quantum HRM Internasional', 'ujian-susulan-dan-remedial-operator-x-ray-fluorescence-spectrometry-batch-11-f3bpz0', 'Pelaksanaan Sertifikasi Person', '2026-11-06', '2026-11-06', '08:00:00', '17:00:00', 540, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '89553714951', 'https://us06web.zoom.us/j/89553714951?pwd=ty1457beiDPew7uTvikiorkmax0OgB.1', 'https://us06web.zoom.us/s/89553714951?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4OTU1MzcxNDk1MSIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiMGJqYW1INm1UM1dwVjFWWU9MXzBZUSIsInppZCI6ImJmMzYxMDAyYjFlMjQ4ODNhZjc4ZjVjMzAxOGY0MWQ2Iiwic2siOiI1ODM0MTUwNzU2MTEyNDI3ODA5Iiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTM3NjY0MDIsImlhdCI6MTc5Mzc1OTIwMiwiYWlkIjoiQ0pPTmMzSWNRX3U5QkM3OEYwUHhWdyIsImNpZCI6IiJ9.KIrA_BJPCLtwwiynVjy3snj9M9Prf-krQk9grGkY3gQ', '2L3WkH', '2026-11-04 09:26:28', '2026-11-04 09:36:45', 10);
INSERT INTO `zoom_bookings` VALUES (18, 232, 12, 'Paparan Sertifikasi LMS Politeknik Keselamatan Transportasi Jalan', 'Tes Daring Sertifikasi Person Quantum HRM Internasional', 'paparan-sertifikasi-lms-politeknik-keselamatan-transportasi-jalan-k5qfmz', NULL, '2026-11-11', '2026-11-11', '13:00:00', '17:00:00', 240, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '86297935594', 'https://us06web.zoom.us/j/86297935594?pwd=q5AW4J17IgFfYO88EyzLaYbUS8Ipae.1', 'https://us06web.zoom.us/s/86297935594?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4NjI5NzkzNTU5NCIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiMGJqYW1INm1UM1dwVjFWWU9MXzBZUSIsInppZCI6IjVmNWVjMWVmNTBlNzQ3MjFhYTAwOTg0MjA1ZDM4ZjZhIiwic2siOiI1ODM0MTUwNzU2MTEyNDI3ODA5Iiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTM3Njc0NTksImlhdCI6MTc5Mzc2MDI1OSwiYWlkIjoiQ0pPTmMzSWNRX3U5QkM3OEYwUHhWdyIsImNpZCI6IiJ9.Zr8C-WlExCHjsd8LMWhMl2PIoijAwDGo5teT3DBbZQI', 'HrcWA2', '2026-11-04 09:44:02', '2026-11-04 09:44:14', 10);
INSERT INTO `zoom_bookings` VALUES (19, 232, 12, 'Sertifikasi LMS Politeknik Keselamatan Transportasi Jalan', 'Tes Daring Sertifikasi Person Quantum HRM Internasional', 'sertifikasi-lms-politeknik-keselamatan-transportasi-jalan-hkemif', NULL, '2026-11-13', '2026-11-13', '08:00:00', '17:00:00', 540, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '81479664320', 'https://us06web.zoom.us/j/81479664320?pwd=xb0Lmxa3ujLsIDBW6rdsn6ZP3ogWFo.1', 'https://us06web.zoom.us/s/81479664320?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4MTQ3OTY2NDMyMCIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiMGJqYW1INm1UM1dwVjFWWU9MXzBZUSIsInppZCI6ImFhOTk0MzVhNGY4YjRmMGU5NWMzYzBkOTZmZGVmZjVjIiwic2siOiI1ODM0MTUwNzU2MTEyNDI3ODA5Iiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTM3Njc1NTQsImlhdCI6MTc5Mzc2MDM1NCwiYWlkIjoiQ0pPTmMzSWNRX3U5QkM3OEYwUHhWdyIsImNpZCI6IiJ9.b76Bt5TfvnN39P0pI6SIs0z4g_55HnJTsvIkmjA5pl0', 'sXFZ6z', '2026-11-04 09:45:38', '2026-11-04 09:45:49', 10);
INSERT INTO `zoom_bookings` VALUES (20, 232, 13, 'Sertifikasi Manajemen Risiko Batch 7', 'Tes Daring Sertifikasi Person Quantum HRM Internasional', 'sertifikasi-manajemen-risiko-batch-7-ghvow7', NULL, '2026-11-10', '2026-11-10', '08:00:00', '18:00:00', 600, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '88312428765', 'https://us06web.zoom.us/j/88312428765?pwd=lidxV7TkkeKtJxgwQxAIuEC6ZleZ2m.1', 'https://us06web.zoom.us/s/88312428765?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4ODMxMjQyODc2NSIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiOFpPcUozWW1RdEtWS1JaNURJUnRTQSIsInppZCI6IjQxOWQxYTBlYzhjZTQ0MDk4MzMyZDRiNjkxMGUzN2Y1Iiwic2siOiIxNzQ1MTEzNzk3NjM2Njg1MjkyIiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTQyMTI3NTgsImlhdCI6MTc5NDIwNTU1OCwiYWlkIjoiQ0FUZE1ZcjRRcUtlNjVEdVZaenF2QSIsImNpZCI6IiJ9.RlsTp2WnjGRfskkdoI0SO9fk-Kxa67nhPZrf1qSubSw', 'BzdsZf', '2026-11-09 13:24:49', '2026-11-09 13:25:58', 10);
INSERT INTO `zoom_bookings` VALUES (21, 232, 13, 'Sertifikasi Manajemen Risiko Batch 8', 'Tes Daring Sertifikasi Person Quantum HRM Internasional', 'sertifikasi-manajemen-risiko-batch-8-e4tojj', NULL, '2026-11-11', '2026-11-11', '08:00:00', '18:00:00', 600, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '81237614572', 'https://us06web.zoom.us/j/81237614572?pwd=bxfdyXWHH4WOG95mAn9DVYDTGaaqAC.1', 'https://us06web.zoom.us/s/81237614572?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4MTIzNzYxNDU3MiIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiOFpPcUozWW1RdEtWS1JaNURJUnRTQSIsInppZCI6IjY2NjZkNmMwNDViZjQ1MzdiYzA4MDFjODc2NTMwMjFkIiwic2siOiIxNzQ1MTEzNzk3NjM2Njg1MjkyIiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTQyMTI3ODEsImlhdCI6MTc5NDIwNTU4MSwiYWlkIjoiQ0FUZE1ZcjRRcUtlNjVEdVZaenF2QSIsImNpZCI6IiJ9.9NphoqrSzJ6gTZz3Np3hQcgBwhEFh7HOOW9M6cnB0hI', '59yVmB', '2026-11-09 13:25:20', '2026-11-09 13:26:21', 10);
INSERT INTO `zoom_bookings` VALUES (22, 232, 13, 'Sertifikasi Manajemen Risiko Batch 9', 'Tes Daring Sertifikasi Person Quantum HRM Internasional', 'sertifikasi-manajemen-risiko-batch-9-rg9qvs', NULL, '2026-11-12', '2026-11-12', '08:00:00', '18:00:00', 600, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '87403509526', 'https://us06web.zoom.us/j/87403509526?pwd=34oYuoRbUWaX5uzHIvf7bKCAdfKnHG.1', 'https://us06web.zoom.us/s/87403509526?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4NzQwMzUwOTUyNiIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiOFpPcUozWW1RdEtWS1JaNURJUnRTQSIsInppZCI6ImY0OTE4Mzk4Mzg1NTQyMmE5NjczZTFlMGExMmQwYTMwIiwic2siOiIxNzQ1MTEzNzk3NjM2Njg1MjkyIiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTQyMTI3OTEsImlhdCI6MTc5NDIwNTU5MSwiYWlkIjoiQ0FUZE1ZcjRRcUtlNjVEdVZaenF2QSIsImNpZCI6IiJ9.0Fk-vwCt1KyehWTEIWlyxHkfbCIsGMZNYEdwVMixb-c', 'he97qu', '2026-11-09 13:25:40', '2026-11-09 13:26:30', 10);
INSERT INTO `zoom_bookings` VALUES (23, 144, 12, 'Meeting Awal Asesor', 'Assessment Center Quantum HRM Internasional', 'meeting-awal-asesor-eabyxj', 'PR-A-425', '2026-11-10', '2026-11-10', '08:00:00', '17:00:00', 540, 1, NULL, 1, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, NULL, NULL, NULL, NULL, '2026-11-10 10:15:39', '2026-11-10 10:15:39', 9);
INSERT INTO `zoom_bookings` VALUES (24, 144, 11, 'LGD, Wawancara dan Presentasi', 'Assessment Center Quantum HRM Internasional', 'lgd-wawancara-dan-presentasi-nvttcr', 'PR-A-425', '2026-11-11', '2026-11-11', '08:00:00', '17:00:00', 540, 1, NULL, 1, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, NULL, NULL, NULL, NULL, '2026-11-10 10:16:44', '2026-11-10 10:16:44', 9);
INSERT INTO `zoom_bookings` VALUES (25, 144, 11, 'Wawancara, Presentasi dan In Basket', 'Assessment Center Quantum HRM Internasional', 'wawancara-presentasi-dan-in-basket-1optso', 'PR-A-426', '2026-11-13', '2026-11-13', '07:00:00', '17:00:00', 600, 1, NULL, 1, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, NULL, NULL, NULL, NULL, '2026-11-10 10:40:33', '2026-11-10 10:40:33', 9);
INSERT INTO `zoom_bookings` VALUES (26, 124, 11, 'Psikotes dan Wawancara Kandidat Karyawan Asuransi Astra Cabang Palu 12.11.2026', 'Lisa Mitra Mandiri', 'psikotes-dan-wawancara-kandidat-karyawan-asuransi-astra-cabang-palu-12112026-bhcuys', NULL, '2026-11-12', '2026-11-12', '08:00:00', '17:00:00', 540, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '82015830659', 'https://us02web.zoom.us/j/82015830659?pwd=aZHBydMIoWm2suTaNaOTYCVhX1uSuZ.1', 'https://us02web.zoom.us/s/82015830659?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4MjAxNTgzMDY1OSIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiTWJiMklabmdRejJlWVJJYlJVVDE3USIsInppZCI6IjJmNGQ4MDRiMmYxYzQzNjBiMDIyNDAzZmQ0ZjRhZWYzIiwic2siOiI5MDIwNzM0Nzc3NTQ1NjQxNzU4Iiwic3R5IjoxMDAsIndjZCI6InVzMDIiLCJleHAiOjE3OTQ0NTE5OTEsImlhdCI6MTc5NDQ0NDc5MSwiYWlkIjoiWUp4NHE3NUVTUHlUMWU1dmFxN2V6USIsImNpZCI6IiJ9.qZiqwcg13Bd--nbXngj0bok47MTGbxCYWgg4gP79qOM', 'thm3Cr', '2026-11-12 07:53:01', '2026-11-12 07:53:11', 6);
INSERT INTO `zoom_bookings` VALUES (27, 232, 12, 'Koordinasi Sertifikasi Wawasan Kebangsaan dan Bela Negara Quantum dan Komunitas Peduli Obyek Vital Nasional', 'Meeting Eksternal', 'koordinasi-sertifikasi-wawasan-kebangsaan-dan-bela-negara-quantum-dan-komunitas-peduli-obyek-vital-nasional-wcyboy', 'Permintaan Pimpinan Untuk Rapat Koordinasi via Daring', '2026-11-12', '2026-11-12', '16:00:00', '20:00:00', 240, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '82083434680', 'https://us06web.zoom.us/j/82083434680?pwd=ZULoQ0lKRdaiW523u48cH6Imbvkfa4.1', 'https://us06web.zoom.us/s/82083434680?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4MjA4MzQzNDY4MCIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiMGJqYW1INm1UM1dwVjFWWU9MXzBZUSIsInppZCI6ImVkYzA5MzVjMGEyNTQyNDRiODZiMGRkMDMyZmY4YWY3Iiwic2siOiI1ODM0MTUwNzU2MTEyNDI3ODA5Iiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTQ0NTUxMTUsImlhdCI6MTc5NDQ0NzkxNSwiYWlkIjoiQ0pPTmMzSWNRX3U5QkM3OEYwUHhWdyIsImNpZCI6IiJ9.6wSWp1SW0Te0E2iyKMLWw7Dsi5NnusXKTbtizVHe-2k', 'bDDdHx', '2026-11-12 08:45:06', '2026-11-12 08:47:39', 8);
INSERT INTO `zoom_bookings` VALUES (28, 15, 22, 'Meeting Diskusi Rencana Asesmen', 'Meeting Internal', 'meeting-diskusi-rencana-asesmen-pzm2ew', NULL, '2026-11-12', '2026-11-12', '10:00:00', '17:00:00', 420, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '85288487444', 'https://us06web.zoom.us/j/85288487444?pwd=awk890ooz2xDvNrPgUrvzkriQhebW3.1', 'https://us06web.zoom.us/s/85288487444?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4NTI4ODQ4NzQ0NCIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoibFpMVWFvQ0dRR1dta29McGhaX3QydyIsInppZCI6IjA0MjFmNWYzMmEyODRjNDE4MzFjNmMxMTQ4MjAxMjQzIiwic2siOiI0MTUyNTc3OTg2ODIyNTk2NTkxIiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTQ0NTUzOTMsImlhdCI6MTc5NDQ0ODE5MywiYWlkIjoiUXBfbUdpUXFUVWllLWdXMWRjVXJXQSIsImNpZCI6IiJ9.Eql1iH94s9_YvfNQ6VTPppFOmGRuXmiDvUESu_fohqw', 'nkyVaa', '2026-11-12 08:49:46', '2026-11-12 08:49:53', 7);
INSERT INTO `zoom_bookings` VALUES (29, 232, 12, 'Sertifikasi Wawasan Kebangsaan dan Bela Negara Batch 7', 'Tes Daring Sertifikasi Person Quantum HRM Internasional', 'sertifikasi-wawasan-kebangsaan-dan-bela-negara-batch-7-j6gsjr', NULL, '2026-11-20', '2026-11-20', '08:00:00', '18:00:00', 600, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '89760043385', 'https://us06web.zoom.us/j/89760043385?pwd=naVSIaUDrl51ZFZKab9fYa2iRR3rX9.1', 'https://us06web.zoom.us/s/89760043385?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4OTc2MDA0MzM4NSIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiMGJqYW1INm1UM1dwVjFWWU9MXzBZUSIsInppZCI6ImJhNDNlZTRkYjk1MTRkZTA4NzliY2VhODE5N2IyYTQ1Iiwic2siOiI1ODM0MTUwNzU2MTEyNDI3ODA5Iiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTQ0NzU0NjcsImlhdCI6MTc5NDQ2ODI2NywiYWlkIjoiQ0pPTmMzSWNRX3U5QkM3OEYwUHhWdyIsImNpZCI6IiJ9.16ubm7he_gYfhtcNS3iIETs8Dh4VxmTrqDJgCSbUW0Q', 'brg3ax', '2026-11-12 14:24:12', '2026-11-12 14:24:27', 10);
INSERT INTO `zoom_bookings` VALUES (30, 232, 12, 'Sertifikasi Estate Management Batch 24', 'Tes Daring Sertifikasi Person Quantum HRM Internasional', 'sertifikasi-estate-management-batch-24-0zqjbj', NULL, '2026-11-21', '2026-11-21', '08:00:00', '18:00:00', 600, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '84245419344', 'https://us06web.zoom.us/j/84245419344?pwd=IZukwof1Ccb2Lfod9SYbvD0hCBMbMe.1', 'https://us06web.zoom.us/s/84245419344?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4NDI0NTQxOTM0NCIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiMGJqYW1INm1UM1dwVjFWWU9MXzBZUSIsInppZCI6ImFmNjVlNTJkZTZlZTQ0MmY4YzZlMzQzMTFlYjZjNDk3Iiwic2siOiI1ODM0MTUwNzU2MTEyNDI3ODA5Iiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTQ0NzU1NDAsImlhdCI6MTc5NDQ2ODM0MCwiYWlkIjoiQ0pPTmMzSWNRX3U5QkM3OEYwUHhWdyIsImNpZCI6IiJ9.hGP1qdhzZVvREDVuRQDr8Pz56RhnEUjra8Enb6M5XRs', 'hNJTZp', '2026-11-12 14:25:33', '2026-11-12 14:25:40', 10);
INSERT INTO `zoom_bookings` VALUES (31, 232, 13, 'Sertifikasi Public Speaker Batch 5', 'Tes Daring Sertifikasi Person Quantum HRM Internasional', 'sertifikasi-public-speaker-batch-5-kyfcw0', NULL, '2026-11-21', '2026-11-21', '08:00:00', '18:00:00', 600, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '84118469395', 'https://us06web.zoom.us/j/84118469395?pwd=rrUqvNXh24uOZ86zTNjREtdr6yaOnb.1', 'https://us06web.zoom.us/s/84118469395?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4NDExODQ2OTM5NSIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiOFpPcUozWW1RdEtWS1JaNURJUnRTQSIsInppZCI6Ijg3MjJjYTg4YmY0ODQ2ZDhhYjE2MTA2Mzk5NGU1ZWRlIiwic2siOiIxNzQ1MTEzNzk3NjM2Njg1MjkyIiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTQ0NzU1OTMsImlhdCI6MTc5NDQ2ODM5MywiYWlkIjoiQ0FUZE1ZcjRRcUtlNjVEdVZaenF2QSIsImNpZCI6IiJ9.0PnqCvMEgBOXGGvnM7LqDFGznOyS_w182L6EbD78Xxs', '2HnC3r', '2026-11-12 14:26:26', '2026-11-12 14:26:32', 10);
INSERT INTO `zoom_bookings` VALUES (32, 124, 13, 'Wawancara Rekrutmen', 'Lisa Mitra Mandiri', 'wawancara-rekrutmen-scqmgd', NULL, '2026-11-13', '2026-11-13', '08:00:00', '17:00:00', 540, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '83999062762', 'https://us06web.zoom.us/j/83999062762?pwd=QtAO5sR28n8JgSwYZheVLFLc3Lje99.1', 'https://us06web.zoom.us/s/83999062762?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4Mzk5OTA2Mjc2MiIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiOFpPcUozWW1RdEtWS1JaNURJUnRTQSIsInppZCI6IjI0ZjRlOThhNjI2OTQxYTE5Y2VhZjg5ODU5YmY1YWVjIiwic2siOiIxNzQ1MTEzNzk3NjM2Njg1MjkyIiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTQ0ODAyNzcsImlhdCI6MTc5NDQ3MzA3NywiYWlkIjoiQ0FUZE1ZcjRRcUtlNjVEdVZaenF2QSIsImNpZCI6IiJ9.Tw0INVZEg86iFO7sgfwqeMbJK-HJeU0xHz0nTrq3RFI', 'CkzPm6', '2026-11-12 15:44:28', '2026-11-12 15:44:36', 6);
INSERT INTO `zoom_bookings` VALUES (33, 144, 11, 'Asesmen SKK Migas', 'Assessment Center Quantum HRM Internasional', 'asesmen-skk-migas-bailzh', 'Akun Zoom Dipakai - Hanya Pencatatan Booking Tidak Membuatkan Link', '2026-11-16', '2026-11-16', '09:00:00', '17:00:00', 480, 1, NULL, 1, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, NULL, NULL, NULL, NULL, '2026-11-16 10:03:19', '2026-11-16 10:03:19', 9);
INSERT INTO `zoom_bookings` VALUES (34, 232, 12, 'Remedial Wawasan Kebangsaan dan Bela Negara Universitas Merdeka Malang', 'Sertifikasi Person Quantum HRM Internasional', 'remedial-wawasan-kebangsaan-dan-bela-negara-universitas-merdeka-malang-kv0xnk', NULL, '2026-11-19', '2026-11-19', '09:00:00', '17:00:00', 480, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '83597915309', 'https://us06web.zoom.us/j/83597915309?pwd=ucp99k0pm0X67bz8xAYxE5vwFMPPkJ.1', 'https://us06web.zoom.us/s/83597915309?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4MzU5NzkxNTMwOSIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiMGJqYW1INm1UM1dwVjFWWU9MXzBZUSIsInppZCI6ImU0ZGY4NGQwOTk4OTQ3YThiMGQ5MjljYjgwMjVhMzVlIiwic2siOiI1ODM0MTUwNzU2MTEyNDI3ODA5Iiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTQ4MjM0MTQsImlhdCI6MTc5NDgxNjIxNCwiYWlkIjoiQ0pPTmMzSWNRX3U5QkM3OEYwUHhWdyIsImNpZCI6IiJ9.jwKMs1lUnziTGcPh9tRChYSukOzPTf6jMBHNJAu0Nkk', 'xEu4Bg', '2026-11-16 15:03:24', '2026-11-16 15:03:34', 10);
INSERT INTO `zoom_bookings` VALUES (35, 232, 11, 'Ujian Susulan dan Remedial Manajemen Risiko IPB', 'Sertifikasi Person Quantum HRM Internasional', 'ujian-susulan-dan-remedial-manajemen-risiko-ipb-uqjxbr', NULL, '2026-11-25', '2026-11-25', '10:00:00', '18:00:00', 480, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '87520031910', 'https://us02web.zoom.us/j/87520031910?pwd=jUfig3lQLYN3fUaU2gqdOnCls1ln7F.1', 'https://us02web.zoom.us/s/87520031910?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4NzUyMDAzMTkxMCIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiTWJiMklabmdRejJlWVJJYlJVVDE3USIsInppZCI6IjcxNzM4NDUwMmEwNjQwMmNiOTgxODg2NGYxYzExNDFiIiwic2siOiI5MDIwNzM0Nzc3NTQ1NjQxNzU4Iiwic3R5IjoxMDAsIndjZCI6InVzMDIiLCJleHAiOjE3OTQ5NzYzMzYsImlhdCI6MTc5NDk2OTEzNiwiYWlkIjoiWUp4NHE3NUVTUHlUMWU1dmFxN2V6USIsImNpZCI6IiJ9.Go6wuh5VK-7VxuFkjugK_RMcaH24SJjHSkT3PLrvoSI', 'ivrxTS', '2026-11-18 09:31:32', '2026-11-18 09:32:16', 10);
INSERT INTO `zoom_bookings` VALUES (36, 232, 11, 'Ujian Susulan dan Remedial Manajemen Risiko IPB', 'Sertifikasi Person Quantum HRM Internasional', 'ujian-susulan-dan-remedial-manajemen-risiko-ipb-xlqaqv', NULL, '2026-11-26', '2026-11-26', '10:00:00', '18:00:00', 480, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '82241782249', 'https://us02web.zoom.us/j/82241782249?pwd=aHGvNpBDfVbBmzloTJlJRCaQUY2LSH.1', 'https://us02web.zoom.us/s/82241782249?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4MjI0MTc4MjI0OSIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiTWJiMklabmdRejJlWVJJYlJVVDE3USIsInppZCI6IjkyZjVlYWQzMzYyMzRkNDk5MjBjZmJjYjE3ZmYzYTM3Iiwic2siOiI5MDIwNzM0Nzc3NTQ1NjQxNzU4Iiwic3R5IjoxMDAsIndjZCI6InVzMDIiLCJleHAiOjE3OTQ5NzYzNDksImlhdCI6MTc5NDk2OTE0OSwiYWlkIjoiWUp4NHE3NUVTUHlUMWU1dmFxN2V6USIsImNpZCI6IiJ9.MeURw3xxrOVxRV8GClmzagC2gk3335IOTdYA5QZKq_Y', 'ZmybHb', '2026-11-18 09:31:57', '2026-11-18 09:32:29', 10);
INSERT INTO `zoom_bookings` VALUES (37, 144, 11, 'Assessment PT Pop Properti', 'Assessment Center Quantum HRM Internasional', 'assessment-pt-pop-properti-qac9ka', 'PR-A-430 - Pinjam Akun Zoom', '2026-11-18', '2026-11-18', '07:30:00', '17:00:00', 570, 1, NULL, 1, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, NULL, NULL, NULL, NULL, '2026-11-18 10:18:17', '2026-11-18 10:18:28', 9);
INSERT INTO `zoom_bookings` VALUES (38, 144, 11, 'Wawancara PT Pop', 'Assessment Center Quantum HRM Internasional', 'wawancara-pt-pop-toldrk', 'PR-A-430 - Pinjam Akun Zoom', '2026-11-19', '2026-11-19', '07:30:00', '17:00:00', 570, 1, NULL, 1, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, NULL, NULL, NULL, NULL, '2026-11-18 10:19:24', '2026-11-18 10:19:24', 9);
INSERT INTO `zoom_bookings` VALUES (39, 144, 22, 'Wawancara Tugsus Padinakes', 'Assessment Center Quantum HRM Internasional', 'wawancara-tugsus-padinakes-iqfsjm', 'PR-A-431 - Pinjam Akun Zoom', '2026-11-19', '2026-11-19', '07:30:00', '17:00:00', 570, 1, NULL, 1, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, NULL, NULL, NULL, NULL, '2026-11-18 10:20:33', '2026-11-18 10:20:33', 9);
INSERT INTO `zoom_bookings` VALUES (40, 21, 22, 'Pelatihan AMI ITERA Hari Pertama', 'Webinar Pelatihan', 'pelatihan-ami-itera-hari-pertama-o0fgnb', NULL, '2026-11-24', '2026-11-24', '08:00:00', '17:00:00', 540, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '82434280088', 'https://us06web.zoom.us/j/82434280088?pwd=bdntoi3HesqYbrBfegCLRrRYIaEssS.1', 'https://us06web.zoom.us/s/82434280088?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4MjQzNDI4MDA4OCIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoibFpMVWFvQ0dRR1dta29McGhaX3QydyIsInppZCI6ImNjNGU3NGE2NDE0ODRiNTliNDcxYmE2ZjU2YWQ3NjBlIiwic2siOiI0MTUyNTc3OTg2ODIyNTk2NTkxIiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTUwNzc5NjUsImlhdCI6MTc5NTA3MDc2NSwiYWlkIjoiUXBfbUdpUXFUVWllLWdXMWRjVXJXQSIsImNpZCI6IiJ9.78N2ms2fHcTN8cLJEcJMSNsdTmbYzHNB2sEWHRuSYwM', 'AMIITERA1', '2026-11-19 13:44:13', '2026-11-19 13:46:04', 11);
INSERT INTO `zoom_bookings` VALUES (41, 21, 22, 'Pelatihan AMI ITERA Hari Kedua', 'Webinar Pelatihan', 'pelatihan-ami-itera-hari-kedua-65gtkt', NULL, '2026-11-25', '2026-11-25', '08:00:00', '17:00:00', 540, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '85414187376', 'https://us06web.zoom.us/j/85414187376?pwd=69clBHiCXUDPLtvIigvW6arJ0TCvRn.1', 'https://us06web.zoom.us/s/85414187376?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4NTQxNDE4NzM3NiIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoibFpMVWFvQ0dRR1dta29McGhaX3QydyIsInppZCI6IjlmYjFjMDllNGQwNzQyODRhYjVlZTA4MGRhOWIyM2U3Iiwic2siOiI0MTUyNTc3OTg2ODIyNTk2NTkxIiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTUwNzc5NzgsImlhdCI6MTc5NTA3MDc3OCwiYWlkIjoiUXBfbUdpUXFUVWllLWdXMWRjVXJXQSIsImNpZCI6IiJ9.ySg1bT3tnWJ_QAgsohutLrRxKRy-Cx5n_x4qexljp2U', 'AMIITERA2', '2026-11-19 13:44:48', '2026-11-19 13:46:17', 11);
INSERT INTO `zoom_bookings` VALUES (42, 21, 22, 'Pelatihan AMI ITERA Hari Ketiga', 'Webinar Pelatihan', 'pelatihan-ami-itera-hari-ketiga-5kbuda', NULL, '2026-11-26', '2026-11-26', '08:00:00', '17:00:00', 540, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '82811344088', 'https://us06web.zoom.us/j/82811344088?pwd=QmWSxWYDvOCFWBR9BJ80ci2ToJhNFB.1', 'https://us06web.zoom.us/s/82811344088?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4MjgxMTM0NDA4OCIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoibFpMVWFvQ0dRR1dta29McGhaX3QydyIsInppZCI6IjhiMmU1M2VkNzkzNDQ5OGU5ZGRmM2MzNjVjMmI0ZDliIiwic2siOiI0MTUyNTc3OTg2ODIyNTk2NTkxIiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTUwNzc5ODksImlhdCI6MTc5NTA3MDc4OSwiYWlkIjoiUXBfbUdpUXFUVWllLWdXMWRjVXJXQSIsImNpZCI6IiJ9.aIK3hBZTQ7AGoqsnURv3d2gS0q-ml2t5sD3_TTCabbs', 'AMIITERA3', '2026-11-19 13:45:22', '2026-11-19 13:46:28', 11);
INSERT INTO `zoom_bookings` VALUES (43, 144, 11, 'CornerStone', 'Assessment Center Quantum HRM Internasional', 'cornerstone-bwk5ve', 'Pinjam Akun Zoom', '2026-11-20', '2026-11-21', '08:00:00', '18:00:00', 600, 2, NULL, 1, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, NULL, NULL, NULL, NULL, '2026-11-20 10:58:27', '2026-11-20 10:58:27', 9);
INSERT INTO `zoom_bookings` VALUES (44, 120, 13, 'Briefing Tim Panitia Psikotes Poltekpel Surabaya', 'Lisa Mitra Mandiri', 'briefing-tim-panitia-psikotes-poltekpel-surabaya-pwnhin', 'LMM', '2026-11-20', '2026-11-20', '14:00:00', '17:00:00', 180, 1, NULL, 3, 'Disetujui otomatis oleh Administrator saat pengajuan.', NULL, '81020868679', 'https://us06web.zoom.us/j/81020868679?pwd=KuxHK7t6TOiuwnB5fHpILIbhoz1usG.1', 'https://us06web.zoom.us/s/81020868679?zak=eyJ0eXAiOiJKV1QiLCJzdiI6IjAwMDAwMiIsInptX3NrbSI6InptX28ybSIsImFsZyI6IkhTMjU2In0.eyJpc3MiOiJ3ZWIiLCJjbHQiOjAsIm1udW0iOiI4MTAyMDg2ODY3OSIsImF1ZCI6ImNsaWVudHNtIiwidWlkIjoiOFpPcUozWW1RdEtWS1JaNURJUnRTQSIsInppZCI6IjhkNDEzNjBmNzMzZDQ2YjY4NDMxMjk5ZmM1YTc3MWQ0Iiwic2siOiIxNzQ1MTEzNzk3NjM2Njg1MjkyIiwic3R5IjoxMDAsIndjZCI6InVzMDYiLCJleHAiOjE3OTUxNTQ0MjMsImlhdCI6MTc5NTE0NzIyMywiYWlkIjoiQ0FUZE1ZcjRRcUtlNjVEdVZaenF2QSIsImNpZCI6IiJ9.Jf0YRblXRNhqYaa67Tfm3RJkOLsDecviic5aVQw6Xs0', 'Vxzauu', '2026-11-20 10:59:53', '2026-11-20 11:00:23', 6);

-- ----------------------------
-- Table structure for zoom_meetings
-- ----------------------------
DROP TABLE IF EXISTS `zoom_meetings`;
CREATE TABLE `zoom_meetings`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `zoom_booking_id` bigint UNSIGNED NOT NULL,
  `zoom_account_id` bigint UNSIGNED NULL DEFAULT NULL,
  `meeting_date` date NOT NULL,
  `meeting_time` time NULL DEFAULT NULL,
  `activity_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `zoom_meeting_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `join_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `passcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `zoom_meetings_zoom_booking_id_foreign`(`zoom_booking_id` ASC) USING BTREE,
  INDEX `zoom_meetings_zoom_account_id_foreign`(`zoom_account_id` ASC) USING BTREE,
  CONSTRAINT `zoom_meetings_zoom_account_id_foreign` FOREIGN KEY (`zoom_account_id`) REFERENCES `zoom_accounts` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `zoom_meetings_zoom_booking_id_foreign` FOREIGN KEY (`zoom_booking_id`) REFERENCES `zoom_bookings` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of zoom_meetings
-- ----------------------------

-- ----------------------------
-- Table structure for zoom_renewals
-- ----------------------------
DROP TABLE IF EXISTS `zoom_renewals`;
CREATE TABLE `zoom_renewals`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `zoom_account_id` bigint UNSIGNED NOT NULL,
  `invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `renewal_date` date NULL DEFAULT NULL,
  `entry_date` datetime NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `zoom_renewals_zoom_account_id_foreign`(`zoom_account_id` ASC) USING BTREE,
  CONSTRAINT `zoom_renewals_zoom_account_id_foreign` FOREIGN KEY (`zoom_account_id`) REFERENCES `zoom_accounts` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of zoom_renewals
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
