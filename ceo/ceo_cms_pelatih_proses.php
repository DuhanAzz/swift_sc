<?php
include '../includes/koneksi.php';

if(isset($_POST['toggle_highlight'])){
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $current_status = $_POST['current_status'];
    $new_status = ($current_status === '1') ? 0 : 1;

    $q = mysqli_query($koneksi, "UPDATE pelatih SET is_highlighted='$new_status' WHERE id='$id'");
    if($q) {
        header("location:ceo_cms_pelatih_highlight.php?pesan=sukses_highlight");
    } else {
        header("location:ceo_cms_pelatih_highlight.php?pesan=gagal"); 
    }
}
?>
