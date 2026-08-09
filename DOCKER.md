# 🐳 Docker – SISFOR Croma Music

Panduan lengkap untuk menjalankan **SISFOR Croma Music** menggunakan Docker.

---

## Struktur File Docker

```
.
├── Dockerfile                  # Multi-stage build (Node 20 → PHP 8.4 FPM)
├── docker-compose.yml          # Orchestration services
├── .dockerignore               # Files excluded from build context
├── .env.docker                 # Docker environment variables
└── docker/
    ├── nginx/
    │   └── default.conf        # Nginx server block
    ├── php/
    │   └── opcache.ini         # PHP 8.4 OPcache settings
    └── entrypoint.sh           # Container startup script
```

---

## Prasyarat

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) terinstall dan berjalan
- Port `8080` dan `3307` tersedia di host

---

## Quickstart

### 1. Salin environment Docker
```bash
cp .env.docker .env.docker.local
```
> **Penting**: Sebelum deploy ke production, ganti `APP_KEY` dengan key baru:
> ```bash
> docker compose exec app php artisan key:generate
> ```

### 2. Build container images
```bash
docker compose build
```
> Build pertama memerlukan waktu beberapa menit karena proses instalasi PHP extensions, Composer dependencies, dan kompilasi Vite assets.

### 3. Jalankan semua services
```bash
docker compose up -d
```

### 4. Cek status containers
```bash
docker compose ps
```
Output yang diharapkan:
| Container      | Service | Status  | Ports               |
|----------------|---------|---------|---------------------|
| croma_app      | app     | running | 9000/tcp            |
| croma_web      | web     | running | 0.0.0.0:8080→80/tcp |
| croma_db       | db      | healthy | 0.0.0.0:3307→3306/tcp |

### 5. Akses aplikasi
Buka browser dan akses: **http://localhost:8080**

---

## Perintah Artisan via Docker

```bash
# Menjalankan perintah artisan
docker compose exec app php artisan <command>

# Contoh: Fresh migration + seeding
docker compose exec app php artisan migrate:fresh --seed

# Contoh: Clear all caches
docker compose exec app php artisan optimize:clear

# Contoh: Membuat controller baru
docker compose exec app php artisan make:controller NamaController
```

## Perintah Composer via Docker

```bash
docker compose exec app composer require <package>
docker compose exec app composer dump-autoload
```

---

## Melihat Logs

```bash
# Semua service
docker compose logs -f

# Hanya service tertentu
docker compose logs -f app
docker compose logs -f web
docker compose logs -f db
```

---

## Menghentikan Services

```bash
# Stop containers (data tetap tersimpan)
docker compose stop

# Stop dan hapus containers (data volume tetap tersimpan)
docker compose down

# Stop, hapus containers, DAN hapus volumes (HATI-HATI: data DB akan hilang)
docker compose down -v
```

---

## Rebuild setelah perubahan kode

```bash
# Rebuild image dan restart
docker compose up -d --build
```

---

## Koneksi Database

Database MySQL dapat diakses dari host machine di:
- **Host**: `localhost`
- **Port**: `3307` (bukan 3306 untuk menghindari konflik dengan MySQL lokal)
- **Database**: `sisfor_croma_music`
- **Username**: `croma_user`
- **Password**: `croma_secret`

---

## Troubleshooting

### Container `app` tidak bisa connect ke database
Cek apakah `db` service sudah `healthy`:
```bash
docker compose ps db
```
Jika belum, tunggu beberapa detik lalu coba lagi. Entrypoint script secara otomatis menunggu MySQL siap.

### Permission error pada storage
```bash
docker compose exec app chmod -R 775 storage bootstrap/cache
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Clear semua cache Laravel
```bash
docker compose exec app php artisan optimize:clear
```
