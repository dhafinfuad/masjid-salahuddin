# Instruksi untuk Claude

## Bahasa
Selalu jawab pengguna dalam bahasa Indonesia.

## Alur kerja: commit, push, dan merge otomatis
Pemilik repo mengizinkan Claude menyelesaikan setiap perubahan sampai masuk ke `main` tanpa bertanya lagi:

1. Kerjakan perubahan di branch kerja sesi (bukan langsung di `main`).
2. Jalankan seluruh tes dari folder `index/`: `php artisan test`.
   - Merge **hanya jika semua tes lulus**. Jika ada yang gagal, perbaiki dulu atau laporkan ke pengguna; jangan merge.
   - Jangan pernah melewati, menonaktifkan, atau menghapus tes agar lulus.
3. Commit dengan pesan yang jelas, lalu push branch kerja.
4. Buka pull request ke `main` (atau perbarui PR yang sudah ada), lalu merge PR itu.
5. Laporkan ke pengguna apa yang diubah dan bahwa perubahan sudah masuk `main`.

Batasan:
- Jangan force-push atau menulis ulang riwayat `main`.
- Deploy ke server tidak dilakukan oleh Claude. Setelah merge, ingatkan pengguna bahwa perubahan baru aktif di website setelah `main` di-deploy.
