<?php
$koneksi = mysqli_connect("localhost", "root", "", "swift_sc");
if(mysqli_query($koneksi, "ALTER TABLE pelatih ADD COLUMN is_highlighted TINYINT(1) DEFAULT 0;")) {
    echo "Success!";
} else {
    echo "Error: " . mysqli_error($koneksi);
}
?>
