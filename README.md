# Almas Clothing Brand E-Commerce

A complete e-commerce website for **Almas Clothing Brand**. Customers can browse products, manage their shopping cart, and place orders online. The project is built using PHP, MySQL, HTML5, CSS3, and XAMPP for local development.

---

## Quick Start

### Requirements
- XAMPP ([Download](https://www.apachefriends.org/))
- Modern web browser
- Git (optional)

### Installation (5 minutes)

1. **Place project in XAMPP**
   ```
   C:\xampp\htdocs\almas_clothing\
   ```

2. **Start XAMPP** - Open XAMPP Control Panel and click Start for Apache and MySQL

3. **Initialize Database** - Visit in your browser:
   ```
   http://localhost/almas_clothing/setup_db.php
   ```
   Then delete `setup_db.php` from the folder

4. **Access the Application**
   - Customer: `http://localhost/almas_clothing/public/index.php`
   - Admin: `http://localhost/almas_clothing/admin/admin_login.php`

5. **Create Admin Account** - Use phpMyAdmin to add an admin user to the `admins` table

---

## Project Overview

An online store for selling Eastern formal, party, and wedding wear. Features:

### Customer Features
- ✓ Browse products by category
- ✓ View detailed product information
- ✓ Add/remove items from shopping cart
- ✓ Checkout with shipping address
- ✓ Multiple payment methods (Cash on Delivery, Card Payment)
- ✓ Order receipts and confirmation
- ✓ Newsletter subscription
- ✓ Contact form
- ✓ User registration and login
- ✓ Responsive mobile design

### Admin Features
- ✓ Admin login and dashboard
- ✓ Add/edit/delete products
- ✓ Manage product categories
- ✓ View products overview

---

## Technology Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3, JavaScript |
| Backend | PHP 7.4+ |
| Database | MySQL 5.7+ |
| Server | Apache (XAMPP) |

---

## Installation Guide

### Step 1: Download & Install XAMPP

1. Download from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install on your system:
   - **Windows**: Default to `C:\xampp`
   - **macOS**: Default to `/Applications/XAMPP`
   - **Linux**: Default to `/opt/lampp`

### Step 2: Download Project

**Option A: Git**
```bash
cd C:\xampp\htdocs
git clone https://github.com/your-repo/almas-clothing.git almas_clothing
```

**Option B: Manual**
- Download project ZIP
- Extract to `C:\xampp\htdocs\almas_clothing`

### Step 3: Start Services

1. Open XAMPP Control Panel
2. Click **Start** for Apache
3. Click **Start** for MySQL
4. Wait for green indicators

### Step 4: Setup Database

**Option A: Using setup_db.php (Recommended for beginners)**

1. Open browser and go to:
   ```
   http://localhost/almas_clothing/setup_db.php
   ```

2. You should see: **"✓ Database Setup Successful!"**
   - This creates the database and all tables automatically

3. **Important**: Delete `setup_db.php` from your folder after setup

**Option B: Using SQL Import in phpMyAdmin (Advanced)**

1. Visit: `http://localhost/phpmyadmin`

2. **Create Database First:**
   - Click "Databases" tab
   - Type `almas_clothing` in the text field
   - Click **Create**

3. **Import SQL File:**
   - Select the `almas_clothing` database
   - Click **Import** tab
   - Click **Choose File** button
   - Select `almas_clothing.sql` from your project folder
   - Click **Go** to import all tables

4. Done! All 9 tables will be created with relationships and indexes

### Step 5: Create Admin Account

Use phpMyAdmin to add your admin account:

1. Visit: `http://localhost/phpmyadmin`
2. Select database: `almas_clothing`
3. Click table: `admins`
4. Click **Insert** and add:
   - **email**: admin@example.com
   - **password**: Use PHP to hash it

To hash a password, run this PHP code:
```php
<?php
$hash = password_hash('your_password_123', PASSWORD_DEFAULT);
echo $hash;
?>
```

Then copy the hash into the password field.

### Step 6: Start Using

**Customer Website:**
```
http://localhost/almas_clothing/public/index.php
```

**Admin Login:**
```
http://localhost/almas_clothing/admin/admin_login.php
```

Use email and password you created in Step 5.

---

## Project Structure

```
almas_clothing/
│
├── admin/                          # Admin Panel
│   ├── admin_login.php            # Login page
│   ├── dashboard.php              # Dashboard
│   ├── add_product.php            # Add products
│   ├── edit_product.php           # Edit products
│   ├── delete_product.php         # Delete products
│   ├── manage_categories.php      # Manage categories
│   └── logout.php                 # Logout handler
│
├── public/                         # Customer Website
│   ├── index.php                  # Homepage
│   ├── shop.php                   # Products list
│   ├── product_page.php           # Product details
│   ├── cart.php                   # Shopping cart
│   ├── checkout.php               # Checkout & payment
│   ├── login.php                  # Customer login
│   ├── register.php               # Customer registration
│   ├── contact.php                # Contact form
│   ├── about.php                  # About page
│   ├── subscribe.php              # Newsletter handler
│   ├── send_message.php           # Contact handler
│   ├── logout.php                 # Logout
│   │
│   ├── css/
│   │   ├── style.css              # Main stylesheet
│   │   └── admin.css              # Admin styles
│   │
│   └── scripts/
│       └── ui.js                  # UI interactions
│
├── templates/
│   ├── header.php                 # Site header
│   └── footer.php                 # Site footer
│
├── includes/
│   ├── db.php                     # Database connection
│   └── functions.php              # Helper functions
│
├── images/                         # Product & static images
│   ├── hero.jpg
│   ├── logo.svg
│   ├── (product images)
│   └── ...
│
├── scripts/
│   └── seed_products.php          # Sample data generator
│
├── uploads/                        # User-uploaded files
│
├── setup_db.php                   # Database setup (delete after use)
│
└── README.md                      # This file
```

---

## Database Schema

**9 tables are automatically created** (via `setup_db.php` or `almas_clothing.sql` import)

### Database Files

- **setup_db.php** - Automatic database setup via browser (easiest method)
- **almas_clothing.sql** - SQL file for direct import in phpMyAdmin

### Table Structures
```
user_id (PK), name, email (UNIQUE), password, created_at
```
Customer accounts

### 2. categories
```
category_id (PK), name (UNIQUE), description, image_url, created_at
```
Product categories

### 3. products
```
product_id (PK), name, description, price, category_id (FK), 
image_url, created_at
```
Product inventory

### 4. admins
```
admin_id (PK), email (UNIQUE), password, created_at
```
Admin accounts

### 5. orders
```
order_id (PK), user_id (FK), total_amount, shipping_address, 
payment_method, status, created_at
```
Customer orders

### 6. order_items
```
order_item_id (PK), order_id (FK), product_id (FK), 
quantity, price
```
Items in orders

### 7. cart_items
```
cart_item_id (PK), user_id (FK), product_id (FK), 
quantity, added_at
```
Shopping cart

### 8. contacts
```
contact_id (PK), name, email, message, created_at
```
Contact form messages

### 9. subscribers
```
subscriber_id (PK), email (UNIQUE), subscribed_at
```
Newsletter subscribers

---

## Configuration

### Database Settings
File: `includes/db.php`

```php
$host = 'localhost';
$db = 'almas_clothing';
$user = 'root';
$password = '';  // Empty by default in XAMPP
```

If you set a MySQL password, update the `$password` value.

---

## Database Initialization Methods

### Method 1: Using setup_db.php (Simplest)
```
1. Visit: http://localhost/almas_clothing/setup_db.php
2. Wait for success message
3. Delete setup_db.php
```
✓ Creates database automatically
✓ Creates all tables with relationships
✓ No manual steps

### Method 2: Using almas_clothing.sql (Direct Import)
```
1. Visit phpMyAdmin: http://localhost/phpmyadmin
2. Create database "almas_clothing"
3. Go to Import tab
4. Choose almas_clothing.sql file
5. Click Go
```
✓ Direct SQL import
✓ Full control over structure
✓ Good for backups/migrations

---

## How to Use

### For Customers

1. **Browse Products**
   - Visit homepage
   - Click "Shop" to see all products
   - Use categories to filter

2. **Add to Cart**
   - Click on product
   - Click "Add to Cart"
   - View cart icon in header

3. **Checkout**
   - Go to cart page
   - Review items
   - Click "Proceed to Checkout"
   - Enter shipping address
   - Choose payment method:
     - Cash on Delivery
     - Credit/Debit Card
   - Complete order

4. **Order Confirmation**
   - View order receipt with order ID
   - Continue shopping or close

### For Admin

1. **Login**
   - Visit admin login page
   - Enter admin email and password

2. **Manage Products**
   - Go to Dashboard
   - Click "Add Product"
   - Fill in details and upload image
   - Save product

3. **Edit Products**
   - Click product in list
   - Update details
   - Save changes

4. **Delete Products**
   - Select product
   - Click delete
   - Confirm deletion

5. **Manage Categories**
   - Go to "Categories"
   - Add new categories
   - Edit existing ones

---

## Design Details

### Color Scheme
- **Primary Black**: #1f2937
- **Secondary Grey**: #374151
- **Light Grey**: #f8fafc
- **Borders**: #e2e8f0
- **Text Dark**: #0f1724

### Responsive Design
- Mobile: 320px+
- Tablet: 768px+
- Desktop: 1024px+
- Large: 1200px+

---

## Troubleshooting

### Problem: "Unknown database 'almas_clothing'"
**Solution:**
- Run `setup_db.php` in your browser
- Check MySQL is running in XAMPP Control Panel
- Verify database name in `includes/db.php`

### Problem: "Access denied" when connecting
**Solution:**
- Check MySQL is running
- Verify credentials in `includes/db.php`
- Default: user='root', password='' (empty)
- If you set a MySQL password, update it in db.php

### Problem: Apache won't start
**Solution:**
- Port 80 might be in use
- Close other web servers (IIS, Nginx, etc.)
- Change Apache port in XAMPP settings
- Restart XAMPP

### Problem: Blank pages or PHP errors
**Solution:**
- Check Apache and MySQL have green status
- Check browser console (F12) for errors
- Check XAMPP error logs

### Problem: CSS/Images not loading
**Solution:**
- Hard refresh browser (Ctrl+F5 or Cmd+Shift+R)
- Clear browser cache
- Check images exist in `images/` folder
- Verify file paths are correct

### Problem: 404 errors
**Solution:**
- Verify folder name is exactly `almas_clothing`
- Check URL path matches folder structure
- Ensure files exist in correct locations

### Problem: Can't access admin panel
**Solution:**
- Verify admin account exists in `admins` table
- Check password is hashed with `password_hash()`
- Try creating new admin account in phpMyAdmin

---

## File Deletion Guide

After setup, these files can be deleted:
- **setup_db.php** - Delete after first run (IMPORTANT)

Keep all other files for application to work.

---

## Browser Support

| Browser | Version |
|---------|---------|
| Chrome | 90+ |
| Firefox | 88+ |
| Safari | 14+ |
| Edge | 90+ |

---

## Performance Tips

1. **Cache CSS** - Browser caches CSS automatically
2. **Optimize Images** - Use compressed images in `images/` folder
3. **Database Indexing** - Indexes created automatically on primary keys
4. **PHP Opcache** - Enabled by default in XAMPP 7.4+

---

## Security Notes

1. Delete `setup_db.php` after setup
2. Change default MySQL password
3. Use HTTPS in production
4. Hash passwords with `password_hash()`
5. Validate all user inputs
6. Use prepared statements for queries (already implemented)

---

## Features Implemented

### E-Commerce Core
- ✓ Product catalog with images
- ✓ Shopping cart (session-based)
- ✓ Order management
- ✓ Order history

### Payment
- ✓ Cash on Delivery option
- ✓ Card Payment form (client-side validation)
- ✓ Order receipts/invoices
- ✓ Professional receipt modal

### User Management
- ✓ Customer registration
- ✓ Customer login
- ✓ Admin authentication
- ✓ Password hashing

### Admin Features
- ✓ Product management (CRUD)
- ✓ Category management
- ✓ Dashboard overview
- ✓ Admin logout

### Customer Features
- ✓ Product browsing
- ✓ Search by category
- ✓ Cart management
- ✓ Checkout process
- ✓ Newsletter signup
- ✓ Contact form
- ✓ Account management

### Design
- ✓ Responsive layout
- ✓ Black/Grey/White theme
- ✓ Smooth animations
- ✓ Mobile-optimized

---

## Next Steps

1. ✓ Install XAMPP
2. ✓ Download project
3. ✓ Run setup_db.php
4. ✓ Create admin account
5. ✓ Add products via admin
6. ✓ Browse and shop
7. ✓ Test checkout process

---

## Support & Contact

For issues or questions:
1. Check Troubleshooting section above
2. Review file paths and configurations
3. Check XAMPP error logs
4. Verify database is created and tables exist

---

## License

This project is proprietary to Almas Clothing Brand.

---

**Project Status**: ✓ Complete and Ready for Use

**Last Updated**: November 2025

**Version**: 1.0
