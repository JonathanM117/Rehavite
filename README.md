<div align="center">

<img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
<img src="https://img.shields.io/badge/PHP-8.1-777BB4?style=for-the-badge&logo=php&logoColor=white" />
<img src="https://img.shields.io/badge/Livewire-3.4-FB70A9?style=for-the-badge&logo=livewire&logoColor=white" />
<img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
<img src="https://img.shields.io/badge/Status-Production-00C896?style=for-the-badge" />

<br /><br />

# Rehavité — Clinical Management Platform

### A full-featured physiotherapy clinic management system built with Laravel 10, Livewire 3 and a custom dark glassmorphism UI. Deployed in production for a real clinic.

<br />

</div>

---

## 🧠 Overview

**Rehavité** is a production-grade web application designed to digitize and streamline operations for physiotherapy clinics. It replaces paper-based workflows with a secure, role-based digital platform covering everything from patient intake to evolution tracking, payments, and automated email notifications.

The system is actively used by a real clinic with real patients, which pushed the development to handle edge cases you don't encounter in tutorial projects — like encoding issues in shared hosting MySQL, Livewire real-time updates, audit logging, and multi-role access control.

---

## ✨ Key Features

### 👤 Patient Management
- Complete **electronic medical records (expedientes)** per patient
- Customizable fields: personal data, marital status, address, occupation, laterality
- **Emergency contact** and **legal guardian** sections
- **Referral source** tracking (how patients found the clinic)
- Unique auto-generated **expediente ID** (`INITIALS-DDMMYYYY` format)
- Role-based visibility: sensitive data (phone, email) hidden from non-owners

### 📋 Medical History (Historial Clínico)
Structured JSON fields for:
- Family medical history
- Personal non-pathological history
- Personal pathological history
- Systems review (visual, auditory, neurological, musculoskeletal, etc.)

### 📅 Consultations & Evolution
- Per-consultation **evolution notes** with date/time tracking
- Assigned **exercises** per consultation session
- Medical **studies** (labs, imaging) attached to consultations
- Consultation status: Active / Inactive

### 👥 Multi-Role Access Control
| Role | Permissions |
|------|-------------|
| **Administrator** | Full access: all patients, all therapists, system config |
| **Physiotherapist** | Access to own patients only |
| **Collaborator** | Read-only access to specific patients (assigned by owner) |

### 🔔 Automated Notifications
- Email sent automatically to **admin + assigned therapist** on new patient registration
- Email template includes: patient name, expediente ID, diagnosis, assigned therapist
- Built with Laravel Mailable, gracefully fails without crashing the app

### 🕵️ Full Audit Log
- Every significant action (record updates, collaborator changes, file uploads) is logged
- Displayed as "Last Activity" in the patient index table
- Helps admins monitor system usage across all therapists

### 🔐 Authentication & Security
- Standard Laravel Auth with password reset via email
- Role middleware protecting all admin routes
- CSRF protection on all forms

---

## 🏗️ Technical Architecture

```
app/
├── Http/
│   ├── Controllers/Admin/     # PatientController, UserController, ConsultationController
│   └── Livewire/              # PatientIndex, PatientDetail, EvolutionNotes,
│                              # ConsultationStudies, ConsultationExercises,
│                              # UserProfile, ActivityReport, LandingEditor
├── Models/                    # Patient, MedicalRecord, Consultation,
│                              # EvolutionNote, AuditLog, User
├── Mail/                      # PatientCreatedMail
└── Policies/                  # Access control logic

resources/views/
├── layouts/                   # admin.blade.php (custom dark glassmorphism layout)
├── livewire/                  # Real-time reactive components
├── admin/patients/            # Create/Edit forms
└── emails/                    # Transactional email templates
```

### Why Livewire?
Livewire 3 was chosen for the patient detail page and indexes to enable **real-time reactive UIs** without writing a separate JavaScript frontend or API layer. Features like collaborator management, search filtering, and inline field editing update instantly without page reloads, giving the clinic staff a smooth, app-like experience.

### JSON Medical History
Instead of dozens of nullable columns, medical history sections are stored as **structured JSON** in a single column. This allows:
- Flexible schema per record
- Clean migrations
- Easy addition of new fields without altering the database
- Type-safe casting via Eloquent's `$casts`

---

## 🎨 UI Design

The entire frontend was built **from scratch with vanilla CSS** — no Tailwind, no Bootstrap. The design system features:

- **Dark glassmorphism** aesthetic (frosted glass cards, layered depth)
- **Custom CSS variables** for a consistent design token system
- **Responsive layout** with overflow-safe tables for mobile
- **Micro-animations** on hover, focus, and modal transitions
- **FontAwesome 6** icons throughout
- Custom **confirmation modals** (replacing browser `alert()` dialogs)
- Birthday **WhatsApp quick-message** button for patients with upcoming birthdays

---

## 🔧 Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | Laravel 10 (PHP 8.1) |
| **Realtime UI** | Livewire 3.4 |
| **Database** | MySQL 8 |
| **Frontend** | Vanilla HTML/CSS/JS |
| **Auth** | Laravel UI (Blade-based) |
| **Email** | Laravel Mailable + SMTP |
| **Permissions** | Spatie Laravel Permission |
| **Hosting** | HostGator cPanel (shared) |
| **Icons** | FontAwesome 6 |
| **Version Control** | Git / GitHub |

---

## 🚀 Local Setup

```bash
# 1. Clone the repository
git clone https://github.com/JonathanM117/Rehavite.git
cd Rehavite

# 2. Install PHP dependencies
composer install

# 3. Copy and configure environment
cp .env.example .env
php artisan key:generate

# 4. Configure your database in .env
# DB_DATABASE=rehavite
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Run migrations
php artisan migrate

# 6. (Optional) Seed demo data
php artisan db:seed

# 7. Start the development server
php artisan serve
```

Then visit `http://localhost:8000` and log in with your seeded admin credentials.

---

## 📦 Production Deployment Notes

This project is deployed on **HostGator shared hosting** without SSH access, which introduced several real-world engineering challenges:

- **MySQL utf8 collation issues** — Resolved by sanitizing audit log strings and using HTML entities in Blade views instead of raw UTF-8 characters, ensuring compatibility with `utf8mb3` column collation.
- **No artisan CLI access** — Solved by creating a temporary PHP script to run `config:cache` via browser request on deployment.
- **Livewire on subdomain** — Required configuring `SESSION_DOMAIN` and `LIVEWIRE_ASSET_URL` to correctly serve component JS assets from `admin.rehavite.com`.

---

## 📸 Screenshots

> *(Add screenshots here — patient index, medical record form, dashboard, email preview)*

---

## 👨‍💻 Author

**Jonathan Morán**
- GitHub: [@JonathanM117](https://github.com/JonathanM117)
- Project built end-to-end: architecture, database design, backend logic, UI/UX design, deployment and production support.

---

## 📄 License

This project is proprietary software developed for **Rehavité Physiotherapy Clinic**. The source code is shared publicly for **portfolio purposes only**. All rights reserved.
