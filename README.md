📦 CapacityApps — Production Capacity Management System

CapacityApps adalah aplikasi internal yang digunakan untuk mengelola kapasitas produksi, melakukan forecasting, serta membantu proses perencanaan order di pabrik (knitting, linking, dan proses lainnya). Sistem ini terintegrasi dengan beberapa service seperti Material System, HRIS, TLS, dan Warehouse Management (soon).

🚀 Features
Productivity

Kontrol produktivitas harian, mingguan, dan bulanan.

Menganalisis waste dan BS untuk membantu monitoring dan pengendalian proses produksi.

Capacity Forecasting

Menghitung dan menampilkan kapasitas harian, mingguan, atau bulanan.

Perhitungan berbasis status order dan data productivity.

Order Planning

Menentukan area produksi terbaik berdasarkan remaining capacity.

Menyusun planning mesin dan order harian.

Material Integration

Integrasi dengan Material System untuk cek ketersediaan material.

Mendukung proses pemesanan material otomatis.

Multi-Area Support

Mendukung berbagai unit produksi dengan konfigurasi yang fleksibel.

API-First Architecture

Menggunakan CodeIgniter 4 dengan struktur modular.

Menyediakan helper untuk pemanggilan service eksternal.

🏗️ Tech Stack

Backend: CodeIgniter 4

Language: PHP 8+

Database: MySQL/MariaDB

Frontend: Bootstrap / AdminLTE / Soft UI

Integration: Material API, HRIS, TLS

📁 Project Structure
app/
 ├── Config/
 │    ├── App.php
 │    ├── Routes.php
 ├── Controllers/
 ├── Models/
 ├── Views/
 ├── Helpers/
 │    └── api_helper.php
 └── Libraries/
public/
writable/

🔧 Installation
1. Clone Repository
git clone <repo-url>
cd capacityApps

2. Install Dependencies
composer install

3. Setup Environment

Copy file environment:

cp env.example .env


Konfigurasi minimal:

app.baseURL = 'http://localhost:8080/'
CI_ENVIRONMENT = development

materialApiUrl = 'http://172.23.39.117/MaterialSystem/public/api'
hrisApiUrl     = 'http://172.23.39.117/HumanResourceSystem/public/api'
tlsApiUrl      = 'http://172.23.39.117/KHTEXT/public/api'

4. Database Migration & Seeder
(rahasia WLE)

5. Run Development Server
php spark serve

🧩 API Helper Example
/**
 * Resolve API endpoint by key
 */
function api_url(string $key): string
{
    $app = config('App');

    return match ($key) {
        'material' => $app->materialApiUrl,
        'hris'     => $app->hrisApiUrl,
        'tls'      => $app->tlsApiUrl,
        default    => throw new InvalidArgumentException("Unknown API key: {$key}"),
    };
}

📝 Coding Standards

Mengikuti PSR-12

Query di Model dibuat minimalis

Business logic diletakkan di Service / Library

Setiap endpoint divalidasi melalui Request Filters

🛠️ Development Workflow

Buat branch baru dari dev

Lakukan commit kecil dan terstruktur

Buat Pull Request → review → merge ke dev

Branch main hanya digunakan untuk production deploy

📄 License

Internal — PT. Kahatex
Tidak untuk penggunaan publik.
