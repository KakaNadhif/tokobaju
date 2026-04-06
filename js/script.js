function konfirmasiHapus(url, nama) {
    if (confirm('Yakin ingin menghapus "' + nama + '"? Tindakan ini tidak bisa dibatalkan.')) {
        window.location.href = url;
    }
} 

function updateCartTotal() {
    let total = 0;
    document.querySelectorAll('.cart-row').forEach(function(row) {
        const harga = parseFloat(row.dataset.harga) || 0;
        const qty   = parseInt(row.querySelector('.qty-input').value) || 0;
        const subtotal = harga * qty;
        row.querySelector('.subtotal').textContent = 'Rp ' + formatRupiah(subtotal);
        total += subtotal;
    });
    const el = document.getElementById('grand-total');
    if (el) el.textContent = 'Rp ' + formatRupiah(total);
}

function formatRupiah(angka) {
    return angka.toLocaleString('id-ID');
}

function validasiLogin(e) {
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    if (!username || !password) {
        e.preventDefault();
        showAlert('Username dan password tidak boleh kosong!', 'danger');
        return false;
    }
}

function validasiRegister(e) {
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    const konfirm  = document.getElementById('confirm_password').value;
    if (username.length < 3) {
        e.preventDefault();
        showAlert('Username minimal 3 karakter!', 'danger');
        return false;
    }
    if (password.length < 6) {
        e.preventDefault();
        showAlert('Password minimal 6 karakter!', 'danger');
        return false;
    }
    if (password !== konfirm) {
        e.preventDefault();
        showAlert('Konfirmasi password tidak cocok!', 'danger');
        return false;
    }
}

function showAlert(pesan, tipe) {
    const existing = document.querySelector('.alert-js');
    if (existing) existing.remove();
    const div = document.createElement('div');
    div.className = 'alert alert-' + tipe + ' alert-js';
    div.textContent = pesan;
    const form = document.querySelector('form');
    if (form) form.insertAdjacentElement('beforebegin', div);
    setTimeout(function() { div.remove(); }, 4000);
}

document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-auto');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            setTimeout(function() { alert.remove(); }, 500);
        }, 3500);
    });

    document.querySelectorAll('.qty-input').forEach(function(input) {
        input.addEventListener('input', updateCartTotal);
    });

    const formLogin = document.getElementById('form-login');
    if (formLogin) formLogin.addEventListener('submit', validasiLogin);

    const formRegister = document.getElementById('form-register');
    if (formRegister) formRegister.addEventListener('submit', validasiRegister);

    const inputGambar = document.getElementById('gambar');
    if (inputGambar) {
        inputGambar.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('preview-gambar');
                    if (preview) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
