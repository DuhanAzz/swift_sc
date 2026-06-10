<?php
include '../includes/koneksi.php';

if(isset($_POST['toggle_highlight'])){
    $id = $_POST['id'];
    $current_status = $_POST['current_status'];
    $new_status = ($current_status === '1') ? false : true;

    try {
        $database->setDocument('coaches', $id, [
            'is_highlighted' => $new_status
        ]);
        header("location:ceo_cms_pelatih_highlight.php?pesan=sukses_highlight");
    } catch (\Exception $e) { 
        header("location:ceo_cms_pelatih_highlight.php?pesan=gagal"); 
    }
}
?>
