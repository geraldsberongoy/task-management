# Task Management LMS

A simple PHP + MySQL role-based task/classroom management project for **Admin**, **Teacher**, and **Student** users.

## Current Features

- Secure login flow with session handling and password hashing support
- Role-based redirects and dashboards:
  - Admin (`admin/`)
  - Teacher (`teacher/`)
  - Student (`student/`)
- Admin tools:
  - Add users (`admin/add_users.php`)
  - View users (`admin/show_users.php`)
  - Manage classrooms (`admin/manage_classroom.php`)
- Classroom module (`includes/classroom.php`) for create/list/edit/delete actions

## Project Structure

```text
task-management/
├── admin/                  # Admin pages and management screens
├── config/                 # Database connection files
├── includes/               # Core classes (auth, classroom)
├── student/                # Student dashboard
├── teacher/                # Teacher dashboard
├── index.php               # Login page
├── testing.php             # Manual classroom test script
└── hash_password.php       # Utility for password hash generation
```

## Requirements

- PHP 8.x (7.4+ may also work)
- MySQL / MariaDB
- Local web server (Apache, Nginx, XAMPP, or similar)

## Quick Setup

1. **Clone the repository**
2. **Create a MySQL database** (default used in code: `classroom_db`)
3. **Create required tables** (`users`, `classrooms`) based on app usage
4. **Update DB credentials** in:
   - `config/database.php`
5. **Serve the project** with your local PHP/web server
6. Open `index.php` and log in with an existing seeded user

## Roles

- **Admin (role_id = 1)**: manage users and classrooms
- **Teacher (role_id = 2)**: teacher dashboard access
- **Student (role_id = 3)**: student dashboard access

## Suggestions to Improve the Repo

1. Add SQL schema/migration files (e.g., `database/schema.sql`) for one-command setup.
2. Add a `.env`-based configuration flow so DB credentials are not hardcoded.
3. Add CSRF protection to all admin POST forms, not just login.
4. Add validation/sanitization helpers for all user inputs in admin forms.
5. Add a `logout.php` endpoint (currently referenced by dashboards).
6. Add missing pages referenced in links (`edit_user.php`, `delete_user.php`) or remove dead links.
7. Improve UI consistency by extracting shared styles into a reusable stylesheet.
8. Add automated tests (PHPUnit) for auth and classroom flows.
9. Add a CI workflow to run lint/tests on every push and PR.
10. Add contribution guidelines and issue templates for team collaboration.

## Notes

- This repository currently has no package manager or test framework configured.
- Basic syntax lint can be run with:

```bash
find . -name '*.php' -print0 | xargs -0 -n1 php -l
```
