# Harmonya TLT CMS Backend

Backend API untuk sistem Harmonya TLT CMS yang dibangun menggunakan **Laravel 12** dan autentikasi **JWT (JSON Web Token)**.

## Persyaratan Sistem

Pastikan lingkungan pengembangan Anda memenuhi persyaratan berikut:

* PHP >= 8.2
* Composer
* MySQL / PostgreSQL
* Git

---

## 1. Clone Repository

```bash
git clone https://github.com/harmonyaID/harmonya_tlt_cms_backend.git
cd harmonya_tlt_cms_backend
```

---

## 2. Install Dependency

Jalankan perintah berikut untuk menginstal seluruh dependency project:

```bash
composer install
```

---

## 3. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Kemudian sesuaikan konfigurasi database pada file `.env`:

```env
APP_NAME=Harmonya
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=harmonya_db
DB_USERNAME=root
DB_PASSWORD=
```

---

## 4. Generate Application Key

Jalankan perintah berikut untuk menghasilkan `APP_KEY`:

```bash
php artisan key:generate
```

---

## 5. Generate JWT Secret

Project ini menggunakan package:

* `php-open-source-saver/jwt-auth`

Jalankan perintah berikut untuk menghasilkan secret JWT:

```bash
php artisan jwt:secret
```

Perintah tersebut akan menambahkan variabel berikut ke file `.env`:

```env
JWT_SECRET=xxxxxxxxxxxxxxxxxxxxxxxx
```

---

## 6. Jalankan Migration Database

Untuk membuat seluruh tabel yang dibutuhkan:

```bash
php artisan migrate
```

Jika tersedia seeder, jalankan:

```bash
php artisan db:seed
```

Atau jalankan migration beserta seeder sekaligus:

```bash
php artisan migrate --seed
```

---

## 7. Generate Autoload

```bash
composer dump-autoload
```

---

# Perintah yang Sering Digunakan

### Menjalankan Server

```bash
php artisan serve
```

### Menjalankan Migration

```bash
php artisan migrate
```

### Menjalankan Seeder

```bash
php artisan db:seed
```

### Refresh Database

```bash
php artisan migrate:fresh --seed
```

### Generate JWT Secret

```bash
php artisan jwt:secret
```

### Membersihkan Cache

```bash
php artisan optimize:clear
```

### Generate Ulang Autoload Composer

```bash
composer dump-autoload
```

# Catatan

* Pastikan konfigurasi database pada file `.env` sudah sesuai sebelum menjalankan migration.
* Jalankan `php artisan optimize:clear` apabila terdapat perubahan konfigurasi yang belum diterapkan.
* Generate JWT Secret hanya perlu dilakukan satu kali saat pertama kali instalasi project.
* Gunakan `php artisan migrate:fresh --seed` apabila ingin membuat ulang seluruh struktur database dari awal.
