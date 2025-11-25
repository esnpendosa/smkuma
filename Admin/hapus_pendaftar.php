<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Include koneksi
$koneksi_path = __DIR__ . '/../Database/koneksi.php';
if (!file_exists($koneksi_path)) {
    die("File koneksi tidak ditemukan!");
}
include $koneksi_path;

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    mysqli_begin_transaction($koneksi);
    
    try {
        // 1. Ambil data dokumen terlebih dahulu
        $doc_query = "SELECT sk_lulus, kk, akta_lahir, pas_foto, ktp_ortu_wali, sertifikat_prestasi FROM dokumen WHERE siswa_id = ?";
        $doc_stmt = $koneksi->prepare($doc_query);
        
        if (!$doc_stmt) {
            throw new Exception("Error preparing document query: " . $koneksi->error);
        }
        
        $doc_stmt->bind_param("i", $id);
        
        if (!$doc_stmt->execute()) {
            throw new Exception("Error executing document query: " . $doc_stmt->error);
        }
        
        $doc_result = $doc_stmt->get_result();
        $doc_data = $doc_result->fetch_assoc();
        $doc_stmt->close();

        // 2. Hapus file fisik dengan multiple path detection
        if ($doc_data) {
            $deleted_files = 0;
            $upload_dirs = [
                __DIR__ . '/../Form/uploads/', // Path utama
                __DIR__ . '/../uploads/',      // Path alternatif
                $_SERVER['DOCUMENT_ROOT'] . '/ppdbsmkum/Form/uploads/' // Absolute path
            ];
            
            foreach ($doc_data as $doc_path) {
                if (!empty($doc_path)) {
                    $filename = basename($doc_path);
                    
                    foreach ($upload_dirs as $upload_dir) {
                        $file_path = $upload_dir . $filename;
                        
                        if (file_exists($file_path) && is_file($file_path)) {
                            if (unlink($file_path)) {
                                error_log("✅ Deleted: " . $filename);
                                $deleted_files++;
                                break;
                            }
                        }
                    }
                }
            }
            error_log("🗑️ Total files deleted: $deleted_files for student ID: $id");
        }

        // 3. Hapus dari tabel dokumen pertama (karena ada foreign key)
        $tables_delete_order = [
            'dokumen',
            'orangtua_wali', 
            'alamat_siswa',
            'jurusan_beasiswa', 
            'akademik',
            'pendaftaran_siswa'
        ];

        $total_deleted = 0;
        
        foreach ($tables_delete_order as $table) {
            $column = ($table === 'pendaftaran_siswa') ? 'id' : 'siswa_id';
            $query = "DELETE FROM $table WHERE $column = ?";
            
            $stmt = $koneksi->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed for $table: " . $koneksi->error);
            }
            
            $stmt->bind_param("i", $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed for $table: " . $stmt->error);
            }
            
            $affected = $stmt->affected_rows;
            $total_deleted += $affected;
            $stmt->close();
            
            error_log("📊 Table $table: $affected row(s) deleted");
        }

        // 4. Commit transaction
        mysqli_commit($koneksi);
        
        $_SESSION['success'] = "✅ Data berhasil dihapus! (ID: $id, $total_deleted records)";
        error_log("🎉 Successfully deleted all data for student ID: $id");

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $_SESSION['error'] = "❌ Gagal menghapus: " . $e->getMessage();
        error_log("💥 Delete failed for ID $id: " . $e->getMessage());
    }
} else {
    $_SESSION['error'] = "❌ ID tidak valid!";
}

header("Location: data_pendaftar.php");
exit();
?>