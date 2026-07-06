<!-- SweetAlert2 UI/UX System -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Intercept native browser confirm() dialogues
        const confirmElements = document.querySelectorAll('[onclick^="return confirm"]');
        confirmElements.forEach(el => {
            const originalOnclick = el.getAttribute('onclick');
            // Extract message inside the confirm('message')
            const msgMatch = originalOnclick.match(/return\s+confirm\s*\(\s*['"](.*?)['"]\s*\)/);
            const message = msgMatch ? msgMatch[1] : 'Anda yakin ingin melanjutkan?';
            
            // Remove native onclick
            el.removeAttribute('onclick');
            
            el.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    customClass: {
                        confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-300 mr-2 transition-all',
                        cancelButton: 'bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-4 focus:ring-red-300 transition-all'
                    },
                    buttonsStyling: false,
                    confirmButtonText: 'Ya, Lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (el.tagName === 'A') {
                            window.location.href = el.href;
                        } else if (el.closest('form')) {
                            // If it's a submit button, append it as a hidden input before submitting
                            if(el.name) {
                                const hidden = document.createElement('input');
                                hidden.type = 'hidden';
                                hidden.name = el.name;
                                hidden.value = el.value || '1';
                                el.closest('form').appendChild(hidden);
                            }
                            el.closest('form').submit();
                        }
                    }
                });
            });
        });
    });
</script>

<?php
// Global Flash Message Handler
if (isset($_GET['pesan'])) {
    $pesan = $_GET['pesan'];
    $title = '';
    $text = '';
    $icon = '';
    $toast = false;

    // Mapping 'pesan' actions
    if (strpos($pesan, 'sukses') !== false) {
        $icon = 'success';
        if ($pesan == 'sukses_login') {
            $title = 'Berhasil Login';
            $text = 'Selamat datang di Sistem Swift SC.';
        } elseif ($pesan == 'sukses_tambah') {
            $title = 'Berhasil';
            $text = 'Data baru telah berhasil ditambahkan.';
        } elseif ($pesan == 'sukses_edit') {
            $title = 'Berhasil';
            $text = 'Perubahan data telah berhasil disimpan.';
            $toast = true;
        } elseif ($pesan == 'sukses_hapus' || $pesan == 'hapus') {
            $title = 'Terhapus';
            $text = 'Data telah berhasil dihapus dari sistem.';
            $toast = true;
        } elseif ($pesan == 'sukses_bayar') {
            $title = 'Pembayaran Diterima';
            $text = 'Tagihan telah berhasil ditandai Lunas.';
            $toast = true;
        } else {
            $title = 'Sukses';
            $text = 'Operasi berhasil dilakukan.';
            $toast = true;
        }
    } elseif ($pesan == 'gagal' || $pesan == 'error') {
        $icon = 'error';
        $title = 'Gagal';
        $msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'Terjadi kesalahan pada sistem.';
        if ($pesan == 'gagal_login') {
            $title = 'Login Gagal';
            $msg = 'Email atau Password salah.';
        }
        $text = $msg;
    } elseif ($pesan == 'belum_login') {
        $icon = 'warning';
        $title = 'Akses Ditolak';
        $text = 'Anda harus login terlebih dahulu.';
    }

    if (!empty($icon)) {
        if ($toast) {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({
                    icon: '{$icon}',
                    title: '{$title}',
                    text: '{$text}'
                });
                
                // Clean URL parameters
                const url = new URL(window.location);
                url.searchParams.delete('pesan');
                url.searchParams.delete('msg');
                window.history.replaceState({}, document.title, url);
            });
            </script>";
        } else {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '{$icon}',
                    title: '{$title}',
                    text: '{$text}',
                    customClass: {
                        confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all'
                    },
                    buttonsStyling: false
                }).then(() => {
                    // Clean URL parameters
                    const url = new URL(window.location);
                    url.searchParams.delete('pesan');
                    url.searchParams.delete('msg');
                    window.history.replaceState({}, document.title, url);
                });
            });
            </script>";
        }
    }
}
?>
