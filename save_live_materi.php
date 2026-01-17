<?php
session_start();

if (isset($_POST['update_materi_live'])) {
    $nama_mk = $_POST['nama_mk'];
    // Ambil materi baru dan bersihkan spasi kosong
    $materi_baru = trim($_POST['materi_baru']);

    if (isset($_SESSION['data_mk'][$nama_mk])) {
        if ($materi_baru === "") {
            // JIKA TEKS KOSONG: Hapus mata kuliah dari array session secara permanen
            unset($_SESSION['data_mk'][$nama_mk]);
            echo "deleted";
        } else {
            // JIKA ADA ISI: Update materi di session
            $_SESSION['data_mk'][$nama_mk]['materi'] = $_POST['materi_baru'];
            echo "updated";
        }
    } else {
        echo "error_not_found";
    }
}
?>