# 🏗️ Arsitektur Sistem

Dokumentasi lengkap tentang arsitektur dan struktur teknis Masjid Salahuddin.

---

## 📋 Daftar Isi

- [High-Level Architecture](#high-level-architecture)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Data Flow](#data-flow)
- [Component Diagram](#component-diagram)
- [Design Patterns](#design-patterns)
- [Scalability](#scalability)

---

## 🏛️ High-Level Architecture

```
┌─────────────────────────────────────────────────────────┐
│                      CLIENT LAYER                       │
│  ┌──────────────────┐         ┌──────────────────────┐ │
│  │   Web Browser    │         │   Mobile (PWA)       │ │
│  │  (Blade + Alpine)│         │  (Installable App)   │ │
│  └─────────┬────────┘         └──────────┬───────────┘ │
└────────────┼──────────────────────────────┼──────────────┘
             │ HTTP/HTTPS                   │
┌────────────┼──────────────────────────────┼──────────────┐
│            │        APPLICATION LAYER      │              │
│  ┌─────────▼───────────────────────────────▼──────────┐  │
│  │              Nginx / OpenResty                      │  │
│  │         (Web Server & Reverse Proxy)               │  │
│  └─────────────┬─────────────────────────────────────┘  │
│                │                                        │
│  ┌─────────────▼─────────────────────────────────────┐  │
│  │           Laravel Application                     │  │
│  │  ┌─────────────────────────────────────────────┐  │  │
│  │  │         Routing Layer (routes/web.php)      │  │  │
│  │  │  Controllers → Livewire Components → Views  │  │  │
│  │  └─────────────────────────────────────────────┘  │  │
│  │  ┌─────────────────────────────────────────────┐  │  │
│  │  │      Business Logic & Services              │  │  │
│  │  │  - Prayer Calculation Service               │  │  │
│  │  │  - Notification Service                     │  │  │
│  │  │  - Backup Service                           │  │  │
│  │  └─────────────────────────────────────────────┘  │  │
│  └─────────────┬─────────────────────────────────────┘  │
└────────────────┼──────────────────────────────────────────┘
                 │
┌────────────────┼──────────────────────────────────────────┐
│                │     PERSISTENCE LAYER                     │
│  ┌─────────────▼───────────────┐                          │
│  │    MySQL/MariaDB Database   │                          │
│  │                             │                          │
│  │  Tables:                    │                          │
│  │  - users, roles             │                          │
│  │  - prayers, schedules       │                          │
│  │  - events, news             │                          │
│  │  - donations/infaq          │                          │
│  │  - etc.                     │                          │
│  └─────────────────────────────┘                          │
│                                                            │
│  ┌─────────────────────────────┐                          │
│  │    Cache (Redis/Memcached)  │                          │
│  │  (Optional - Performance)   │                          │
│  └─────────────────────────────┘                          │
└────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────┐
│              EXTERNAL SERVICES (Optional)                │
│  ┌────────────────────────────────────────────────────┐  │
│  │  Mail Service (SMTP)                               │  │
│  │  - Send notifications                             │  │
│  │  - Password reset, etc                            │  │
│  └────────────────────────────────────────────────────┘  │
│  ┌────────────────────────────────────────────────────┐  │
│  │  Web Push Service (Firebase, etc)                 │  │
│  │  - Browser notifications                          │  │
│  └────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────┘
```

---

## 🛠️ Technology Stack

### Backend

| Layer | Technology | Purpose |
|-------|-----------|---------|
| **Framework** | Laravel 11 | Web framework & MVC |
| **Language** | PHP 8.2+ | Server-side logic |
| **ORM** | Eloquent | Database abstraction |
| **Routing** | Laravel Router | URL routing & API |
| **Authentication** | Laravel Auth | User authentication |
| **Authorization** | Spatie Permission | Role & Permission |
| **Validation** | Laravel Validator | Input validation |
| **Queue** | Redis/Database | Background jobs |

### Frontend

| Layer | Technology | Purpose |
|-------|-----------|---------|
| **Templating** | Blade | HTML templating |
| **Reactivity** | Livewire | Real-time components |
| **Scripting** | Alpine.js | Lightweight JavaScript |
| **Styling** | Tailwind CSS | Utility-first CSS |
| **Build Tool** | Vite | Module bundler |
| **PWA** | Service Worker | Offline support |

### Database

| Layer | Technology | Purpose |
|-------|-----------|---------|
| **DBMS** | MySQL 8.0+ / MariaDB 10.5+ | Relational database |
| **Migrations** | Laravel Migrations | Schema version control |
| **Seeding** | Laravel Seeders | Populate test data |

### DevOps & Deployment

| Layer | Technology | Purpose |
|-------|-----------|---------|
| **Web Server** | Nginx | HTTP server |
| **Reverse Proxy** | OpenResty/Nginx | Load balancing |
| **SSL/TLS** | Let's Encrypt | HTTPS certificate |
| **Control Panel** | 1Panel | Server management |
| **Version Control** | Git | Code repository |

---

## 📁 Project Structure

```
masjid-salahuddin/
│
├── index/                          # ⭐ MAIN LARAVEL APPLICATION
│   │
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/        # Request handlers
│   │   │   │   ├── Dashboard/
│   │   │   │   ├── Prayer/
│   │   │   │   ├── Event/
│   │   │   │   └── ...
│   │   │   ├── Requests/           # Form request validation
│   │   │   ├── Middleware/         # HTTP middleware
│   │   │   └── Resources/          # API resources
│   │   │
│   │   ├── Livewire/               # Reactive components
│   │   │   ├── Prayer/
│   │   │   ├── Admin/
│   │   │   └── ...
│   │   │
│   │   ├── Models/                 # Eloquent models
│   │   │   ├── User.php
│   │   │   ├── Prayer.php
│   │   │   ├── Event.php
│   │   │   └── ...
│   │   │
│   │   ├── Services/               # Business logic
│   │   │   ├── PrayerService.php
│   │   │   ├── NotificationService.php
│   │   │   ├── BackupService.php
│   │   │   └── ...
│   │   │
│   │   ├── Actions/                # Single-responsibility actions
│   │   ├── Observers/              # Model observers
│   │   ├── Events/                 # Custom events
│   │   ├── Listeners/              # Event listeners
│   │   ├── Notifications/          # Notification classes
│   │   ├── Jobs/                   # Queued jobs
│   │   └── Providers/              # Service providers
│   │
│   ├── config/                     # Application configuration
│   │   ├── app.php
│   │   ├── database.php
│   │   ├── cache.php
│   │   └── ...
│   │
│   ├── database/
│   │   ├── migrations/             # Database schema
│   │   ├── seeders/                # Seed data
│   │   └── factories/              # Test data factories
│   │
│   ├── public/                     # Web root (served publicly)
│   │   ├── index.php               # Application entry point
│   │   ├── manifest.json           # PWA manifest
│   │   ├── sw.js                   # Service worker
│   │   ├── css/                    # Compiled styles
│   │   ├── js/                     # Compiled scripts
│   │   ├── images/                 # Static images
│   │   └── icons/                  # App icons
│   │
│   ├── resources/
│   │   ├── views/                  # Blade templates
│   │   │   ├── layouts/
│   │   │   ├── components/
│   │   │   ├── pages/
│   │   │   └── livewire/
│   │   ├── css/
│   │   │   └── app.css             # Tailwind + custom CSS
│   │   └── js/
│   │       └── app.js              # Alpine.js + app scripts
│   │
│   ├── routes/
│   │   ├── web.php                 # Web routes
│   │   ├── api.php                 # API routes
│   │   └── console.php             # Console commands
│   │
│   ├── storage/                    # Application storage
│   │   ├── app/                    # File storage
│   │   ├── logs/                   # Application logs
│   │   └── framework/              # Framework cache
│   │
│   ├── tests/
│   │   ├── Feature/                # Feature tests
│   │   ├── Unit/                   # Unit tests
│   │   └── TestCase.php
│   │
│   ├── .env.example                # Environment template
│   ├── composer.json               # PHP dependencies
│   ├── package.json                # Node dependencies
│   ├── vite.config.js              # Vite configuration
│   ├── tailwind.config.js          # Tailwind configuration
│   └── phpunit.xml                 # Test configuration
│
├── rewrite/
│   └── nginx.conf                  # Nginx URL rewrite rules
│
├── ssl/                            # SSL certificates (gitignored)
│   ├── certificate.crt
│   └── private.key
│
├── log/                            # Application logs (gitignored)
│
├── docs/                           # 📚 Documentation
│   ├── SETUP.md
│   ├── ARCHITECTURE.md
│   ├── DATABASE.md
│   ├── API.md
│   └── DEPLOYMENT.md
│
├── README.md                       # Project README
├── CONTRIBUTING.md                 # Contribution guidelines
└── LICENSE                         # MIT License
```

---

## 🔄 Data Flow

### User Registration & Authentication Flow

```
User Input (Registration Form)
        ↓
HTML Form → Livewire Component
        ↓
Validation (Form Request)
        ↓
Store to Database (User Model)
        ↓
Hash Password (bcrypt)
        ↓
Send Verification Email (Notification)
        ↓
User Verified → Account Active
```

### Prayer Time Calculation & Notification Flow

```
Daily Scheduled Job (Cron)
        ↓
PrayerService::calculateDaily()
        ↓
Fetch Prayer Data from Database
        ↓
Calculate Prayer Times (Algorithm)
        ↓
Store to Cache (Redis)
        ↓
Queue Notification Jobs
        ↓
Send Web Push Notifications
        ↓
Send Email Notifications
```

### Admin Dashboard Data Flow

```
Admin Login
        ↓
Authorization Check (Spatie Permission)
        ↓
Load Dashboard Data (Models)
        ↓
Compile Statistics & Charts
        ↓
Render Blade View
        ↓
Livewire Components for Real-time Updates
        ↓
Display in Browser
```

---

## 🧩 Component Diagram

### Model-View-Controller (MVC)

```
┌─────────────────────────────────────────────────────────┐
│                   REQUEST (HTTP)                        │
└────────────────────────┬────────────────────────────────┘
                         │
                         ▼
            ┌────────────────────────┐
            │   Route (web.php)      │
            │ - Matches URL pattern  │
            │ - Calls Controller     │
            └────────────┬───────────┘
                         │
                         ▼
            ┌────────────────────────┐
            │  Controller            │
            │ - Handle request logic │
            │ - Interact with Models │
            │ - Return View          │
            └────────┬───────────┬───┘
                     │           │
          ┌──────────▼──────┐   │
          │  Model (Database)│   │
          │ - Query data     │   │
          │ - Validate data  │   │
          │ - Save/Update    │   │
          └──────────────────┘   │
                                 │
                         ┌───────▼──────────┐
                         │  View (Blade)    │
                         │ - Render HTML    │
                         │ - Display data   │
                         └───────┬──────────┘
                                 │
                         ┌───────▼──────────┐
                         │  Response (HTML) │
                         │ - Browser render │
                         └──────────────────┘
```

---

## 🎯 Design Patterns

### 1. Service Layer Pattern
Memisahkan business logic dari controller:

```php
// Controller
class PrayerController extends Controller
{
    public function __construct(private PrayerService $service)
    {}
    
    public function store(StorePrayerRequest $request)
    {
        $prayer = $this->service->create($request->validated());
        return redirect()->back()->with('success', 'Prayer created');
    }
}

// Service
class PrayerService
{
    public function create(array $data): Prayer
    {
        $prayer = Prayer::create($data);
        $this->calculateTimes($prayer);
        return $prayer;
    }
}
```

### 2. Repository Pattern
Abstraksi data access:

```php
interface PrayerRepository
{
    public function all(): Collection;
    public function find(int $id): Prayer;
    public function create(array $data): Prayer;
}

class EloquentPrayerRepository implements PrayerRepository
{
    public function all(): Collection
    {
        return Prayer::all();
    }
}
```

### 3. Observer Pattern
Menjalankan logic saat event terjadi:

```php
class UserObserver
{
    public function created(User $user)
    {
        // Send welcome email
        Mail::send(new WelcomeEmail($user));
    }
}
```

### 4. Event-Driven Architecture
Loose coupling antar komponen:

```php
// Trigger event
event(new UserRegistered($user));

// Listen to event
class SendWelcomeEmail implements ShouldQueue
{
    public function handle(UserRegistered $event)
    {
        Mail::send(new WelcomeEmail($event->user));
    }
}
```

---

## 📈 Scalability

### Current Architecture
- ✅ Single server deployment
- ✅ Suitable untuk 500-1000 concurrent users
- ✅ Development & small-scale production

### Future Scalability Options

**Horizontal Scaling (Multiple Servers):**
```
Load Balancer
    ├── Web Server 1 (Laravel App)
    ├── Web Server 2 (Laravel App)
    └── Web Server 3 (Laravel App)
         ↓
    Shared Database Server (MySQL)
         ↓
    Shared Cache (Redis)
         ↓
    Shared File Storage (NFS/S3)
```

**Microservices Architecture (Advanced):**
```
API Gateway
    ├── Authentication Service
    ├── Prayer Service
    ├── Notification Service
    ├── File Service
    └── Admin Service
```

---

## 🔐 Security Architecture

```
┌──────────────────────────────────┐
│  HTTPS / SSL/TLS                 │
│  - Encrypted data transmission   │
└──────────────────────────────────┘
         ↓
┌──────────────────────────────────┐
│  CSRF Protection                 │
│  - Laravel CSRF tokens           │
└──────────────────────────────────┘
         ↓
┌──────────────────────────────────┐
│  Authentication                  │
│  - Laravel Auth & Sessions       │
│  - Password hashing (bcrypt)     │
└──────────────────────────────────┘
         ↓
┌──────────────────────────────────┐
│  Authorization                   │
│  - Spatie Permission (Roles)     │
│  - Policy-based (Can/Authorize)  │
└──────────────────────────────────┘
         ↓
┌──────────────────────────────────┐
│  Input Validation                │
│  - Form Request validation       │
│  - Sanitization                  │
└──────────────────────────────────┘
         ↓
┌──────────────────────────────────┐
│  SQL Injection Prevention         │
│  - Eloquent ORM (Parameterized)  │
│  - Query builder                 │
└──────────────────────────────────┘
```

---

## 📞 Questions?

Baca dokumentasi lain atau tanya di GitHub Discussions.

**Next Steps:**
- [SETUP.md](./SETUP.md) - Setup development environment
- [DATABASE.md](./DATABASE.md) - Database schema & relations
- [API.md](./API.md) - API documentation
