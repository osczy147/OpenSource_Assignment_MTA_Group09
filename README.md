# ArtVault — Digital Portfolio Management System

**Course:** CP 222 — Open Source Technologies  
**Degree Program:** Multimedia Technology and Animation  
**Group Number:** [Group09]  
**Deadline:** 18th June 2026

---

## Project Overview

**ArtVault** is a web-based Digital Portfolio Management System built with PHP and MySQL, designed for Multimedia Technology and Animation students and professionals. The platform allows artists to create accounts, upload and manage their creative works, and showcase projects in a public gallery with powerful search capabilities.

### Key Features
- **User Registration & Authentication** — Secure login/logout with password hashing
- **User Management Module** — Admin panel to add, delete, and assign user roles (admin/artist)
- **Portfolio Item Management** — Add, edit, and delete artwork/project entries with image uploads
- **Gallery Display** — Browse all portfolio items with category filtering
- **Search System** — Search by keyword, category, and year
- **Featured Items** — Admins can highlight outstanding work
- **Responsive Design** — Works on desktop and mobile browsers

---

## Technologies Used

| Layer       | Technology                          |
|-------------|-------------------------------------|
| Backend     | PHP 8.x (procedural + OOP/mysqli)  |
| Database    | MySQL 8.x                           |
| Frontend    | HTML5, CSS3 (custom, no framework)  |
| Fonts       | Google Fonts (Syne, Inter)          |
| Auth        | PHP Sessions + `password_hash()`    |
| File Upload | PHP `$_FILES` + `move_uploaded_file()` |
| Version Control | Git + GitHub                   |

---

## Project Structure

```
portfolio_system/
├── index.php              # Homepage / gallery view
├── login.php              # User login
├── register.php           # User registration
├── add_portfolio.php      # Add new portfolio item
├── edit_portfolio.php     # Edit existing item
├── delete_portfolio.php   # Delete item handler
├── search.php             # Search portfolio items
├── logout.php             # Session logout
├── install.php            # One-time database setup
├── admin/
│   └── users.php          # User management (admin only)
├── includes/
│   ├── db.php             # Database connection
│   ├── auth.php           # Authentication helpers
│   ├── header.php         # Shared nav/header
│   └── footer.php         # Shared footer
├── css/
│   └── style.css          # Full stylesheet
└── uploads/               # Uploaded artwork images
```

---

## Installation Steps

### Requirements
- PHP >= 7.4
- MySQL >= 5.7 or MariaDB >= 10.3
- Apache / Nginx (XAMPP, WAMP, or LAMP stack recommended)

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/[YourUsername]/OpenSource_Assignment_MTA_Group[N].git
   cd OpenSource_Assignment_MTA_Group[N]
   ```

2. **Move to your web server root**
   ```bash
   # For XAMPP on Windows
   move portfolio_system C:\xampp\htdocs\

   # For Linux/macOS LAMP
   cp -r portfolio_system /var/www/html/
   ```

3. **Set upload folder permissions** (Linux/macOS)
   ```bash
   chmod 775 portfolio_system/uploads
   ```

4. **Run the installer**
   Open your browser and navigate to:
   ```
   http://localhost/portfolio_system/install.php
   ```
   This creates the `portfolio_db` database and all tables automatically.

5. **Delete the installer** (security best practice)
   ```bash
   rm portfolio_system/install.php
   ```

6. **Login with default admin credentials**
   - Username: `admin`
   - Password: `admin123`

---

## Git Commands Used

```bash
# Initialize repository
git init

# Stage all files
git add .

# Initial commit
git commit -m "Initial project setup and folder structure"

# Add database and auth files
git add includes/
git commit -m "Added database connection and authentication module"

# Add portfolio CRUD
git add index.php add_portfolio.php edit_portfolio.php delete_portfolio.php
git commit -m "Implemented portfolio add, edit, delete functionality"

# Add search feature
git add search.php
git commit -m "Implemented search feature with keyword, category, and year filters"

# Add user management (admin)
git add admin/
git commit -m "Added admin user management module"

# Create development branch
git checkout -b development

# Add a new feature in development branch
git add .
git commit -m "Added featured items badge and category filter tabs"

# Merge development branch into main
git checkout main
git merge development

# Push to GitHub
git remote add origin https://github.com/[YourUsername]/OpenSource_Assignment_MTA_Group[N].git
git branch -M main
git push -u origin main
```

---

## GitHub Repository

🔗 [https://github.com/[YourUsername]/OpenSource_Assignment_MTA_Group[N]](https://github.com/)

---

## Screenshots

> *(Include screenshots of: Homepage Gallery, Search Page, Add Portfolio Form, User Management Panel, Login Page, Git commit history, Branch creation and merge — in your submitted report.)*

---

## Group Members

| # |       Full Name      | Registration Number |    Role   |
|---|----------------------|---------------------|-----------|
| 1 | [BEATRICE REVOCATUS] |  [T24-03-22215]     | Developer |
| 2 | [DEVOTHA JUSTIN]     |  [T24-03-25262]     | Developer |
| 3 | [MOSES DANIEL]       |  [T24-03-12891]     | Developer |
| 4 | [EDWIN NOLBERT]      |  [T24-03-25367]     | Developer |
| 5 | [OSCAR MERTUS]       |  [T24-03-17531]     | Designer  |
| 6 | [LUCAS GIDEON]       |  [T24-03-22921]     | Designer  |

---

## License

This project was developed as an academic assignment for CP 222 — Open Source Technologies.
