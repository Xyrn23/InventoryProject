# Inventory Management System

This is a web-based Inventory Management System built with PHP and SQLite. It allows users to manage products, track inventory levels, and process sales through a Point of Sale (POS) interface.

## Features

*   **User Authentication:** Secure user registration and login system.
*   **Dashboard:** An overview of inventory statistics.
*   **Product Management:**
    *   Add new products with details like name, description, price, quantity, and images.
    *   Edit existing product information.
    *   Delete products from the inventory.
*   **Image Uploads:** Supports uploading two images per product.
*   **Point of Sale (POS):** A simple interface for processing customer sales.
*   **Admin Panel:** Manage user roles and permissions.
*   **Search and Sort:** Easily find products by name or code, and sort them.

## Technologies Used

*   **Backend:** PHP
*   **Frontend:** HTML, CSS, JavaScript
*   **Database:** SQLite

## Getting Started

### Prerequisites

*   A web server with PHP support (like Apache or Nginx).
*   PHP with the SQLite3 extension enabled.

### Installation

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/Xyrn23/InventoryProject.git
    cd your-repo-name
    ```

2.  **Database:**
    The project uses a pre-populated SQLite database named `inventory.db`. No special setup is required.

3.  **Running the application:**
    *   Place the project files in the root directory of your web server (e.g., `htdocs` for XAMPP, `www` for WAMP).
    *   Open your web browser and navigate to `http://localhost/` to access the application.

## File Structure

```
.
├── admin_manage.php    # Admin panel for user management
├── dashboard.php       # Dashboard with inventory overview
├── index.php           # Login page
├── inventory.db        # SQLite database file
├── inventory.php       # Main inventory management page
├── login_process.php   # Handles user login
├── logout.php          # Handles user logout
├── pos.php             # Point of Sale interface
├── register.php        # User registration page
├── assets/             # Images, logos, etc.
├── scripts/            # JavaScript files
├── styles/             # CSS stylesheets
└── uploads/            # Directory for uploaded product images
```

## How to Use

1.  **Register a new account** or log in with existing credentials.
2.  Use the **Dashboard** to get an overview of your inventory.
3.  Navigate to the **Inventory** page to add, edit, or delete products.
4.  Use the **POS** system to process sales.
5.  If you are an admin, you can manage users from the **Admin** panel.
