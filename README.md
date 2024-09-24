# Snapgram Simplified

Snapgram Simplified adalah aplikasi berbagi foto yang memungkinkan pengguna untuk mengunggah, mengedit, dan memberikan komentar pada foto. 

## Prerequisites

Sebelum memulai, pastikan Anda telah menginstal:

- [PHP](https://www.php.net/) (minimal versi 7.3)
- [Composer](https://getcomposer.org/)
- [MySQL](https://www.mysql.com/) atau database lainnya yang didukung
- [Laravel](https://laravel.com/docs/8.x/installation) (minimal versi 8.x)

## Instalasi

Ikuti langkah-langkah berikut untuk menginstal dan menjalankan aplikasi ini:

1. **Clone repositori**
   ```bash
   git clone https://github.com/harry-awanda/snapgram-simplified.git
   cd snapgram-simplified
   ```

2. **Instal dependensi menggunakan Composer**
   ```bash
   composer install
   ```

3. **Buat salinan file `.env`**
   ```bash
   cp .env.example .env
   ```

4. **Konfigurasi file `.env`**
   Edit file `.env` dengan informasi database Anda:
   ```plaintext
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database
   DB_USERNAME=username
   DB_PASSWORD=password
   ```

5. **Jalankan migrasi**
   Untuk membuat tabel yang diperlukan dalam database, jalankan perintah berikut:
   ```bash
   php artisan migrate
   ```

6. **Generate key aplikasi**
   Jalankan perintah berikut untuk menghasilkan kunci aplikasi:
   ```bash
   php artisan key:generate
   ```

7. **Jalankan server**
   Setelah semua langkah selesai, Anda dapat menjalankan aplikasi dengan perintah:
   ```bash
   php artisan serve
   ```

   Aplikasi akan berjalan di `http://localhost:8000`.

## Kontribusi

Jika Anda ingin berkontribusi pada proyek ini, silakan buat branch baru dan ajukan pull request.

## Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).
