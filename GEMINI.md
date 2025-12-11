# Gemini Project Context: Inventory Management System

## Project Overview

This is a web-based Inventory Management System built with PHP and SQLite. It provides functionalities for managing products, tracking inventory, processing sales via a Point of Sale (POS) interface, and user management.

The application supports two user roles:
*   **Admin:** Full access to all features, including product management, user management, and sales reports.
*   **Cashier:** Limited access to the Point of Sale (POS) system for processing customer sales.

## Architecture

The project follows a simple, file-based architecture where each major feature is encapsulated in its own PHP file. It uses a single SQLite database file (`inventory.db`) for all data storage, making it a portable and self-contained application.

The frontend is built with standard HTML, CSS, and JavaScript, with some minor visual enhancements provided by the `vanilla-tilt.js` library.

## Key Files and Directories

*   **Core Application Files:**
    *   `index.php`: The main login page and entry point for users.
    *   `login_process.php`: Handles user authentication and session management.
    *   `dashboard.php`: The admin dashboard, focused on adding new products.
    *   `inventory.php`: The main interface for viewing, editing, and deleting products.
    *   `pos.php`: The Point of Sale (POS) interface for processing sales.
    *   `report.php`: Displays sales analytics, including revenue trends and top-selling products.
    *   `transaction.php`: A detailed view of recent transactions, linked from the main report page.
    *   `admin_manage.php`: A panel for administrators to manage user accounts and roles.

*   **Data:**
    *   `inventory.db`: The SQLite database file containing all application data (users, products, sales).

*   **Assets and Scripts:**
    *   `assets/`: Contains static assets like the application logo and placeholder images.
    *   `styles/`: Contains CSS files for styling each page.
    *   `scripts/`: Contains JavaScript files for frontend interactivity on pages like the POS and inventory.
    *   `uploads/`: The directory where product images are stored after being uploaded.

## Building and Running the Project

### Prerequisites

*   A web server with PHP support (e.g., Apache, Nginx).
*   The PHP `sqlite3` extension must be enabled.

### Running the Application

1.  Place the entire project directory into the root directory of your web server (e.g., `htdocs/` for XAMPP, `www/` for WAMP).
2.  Open a web browser and navigate to `http://localhost/` (or the appropriate subdirectory if you placed it there).
3.  The application uses a pre-existing SQLite database (`inventory.db`), so no initial database setup is required.

## Development Conventions

*   **PHP Coding Style:** The project aims to follow the **PSR-12** coding standard. The configuration file `phpcs.xml` specifies this and includes minor exceptions:
    *   `PSR1.Files.SideEffects.FoundWithSymbols` is excluded, allowing files to both declare symbols and have side-effects (a common pattern in this project).
    *   `Generic.WhiteSpace.ScopeIndent` is excluded, suggesting some flexibility in indentation rules.
*   **Database Interaction:** All database queries are performed directly within the PHP files using the `PDO` extension with `PDO::ERRMODE_EXCEPTION` enabled for error handling.
*   **Security:** The application uses `password_hash()` and `password_verify()` for user password management, which is a secure practice. It also uses prepared statements for database queries to prevent SQL injection.
*   **Frontend:** The frontend uses vanilla JavaScript and CSS. There is no complex build process or package management (like npm).
