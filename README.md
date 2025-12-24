# Global College Billing & Administration System

A world-class, enterprise-grade school billing system with role-based access control, hidden discount logic, professional receipts, term-based billing, PWA support, and mobile Super Admin dashboard. Built with Laravel, Tailwind CSS, and Alpine.js.

## Features

### 📱 PWA / Installable App
- **Web App Manifest**: Install the system as a desktop app
- **Service Worker**: Basic offline support and faster loading
- **Standalone Mode**: Launch in app-like experience
- **Mobile Optimized**: Responsive design for all devices

### 📊 Mobile Super Admin Dashboard
- **Quick Insights**: Today, week, and month payment summaries
- **Recent Transactions**: Last 10 payments with quick access
- **System Health**: Critical alerts and data consistency checks
- **Term Summaries**: Payment breakdown by academic term
- **Mobile-First Design**: Optimized for phone viewing

### 📅 Term-Based Billing
- **Academic Year Tracking**: All payments linked to academic year (e.g., 2024/2025)
- **Term Management**: Payments organized by Term 1, 2, 3, or 4
- **Clear History**: View student payment history per term
- **Outstanding Balances**: Track unpaid amounts by term
- **Report Filtering**: Filter reports by academic year and term

### 🔐 Role-Based Access Control
- **Super Admin**: Full system access with visibility into all financial data including base prices and discounts
- **Cashier**: Can process payments and generate receipts without seeing base prices or discount information
- **Configurable Roles**: Fully customizable role and permission system

### 💰 Hidden Discount Logic
- Each course has a base price (visible only to Super Admin)
- Cashiers input only the final agreed payment amount
- System automatically computes discount: `discount = base_price - amount_paid`
- Discount data is NEVER visible or editable by Cashiers
- Only Super Admin can view discount information

### 📄 Professional Receipts
- Branded receipts with school name and logo
- Includes student name, course name, amount paid, date, and receipt number
- Printable format optimized for printing
- Admin view shows base price and discount
- Public/Cashier view shows amount paid only

### 📊 Reports
- Super Admin can view and print:
  - Payments today
  - Payments this week
  - Payments this month
  - Custom date range filtering
- Reprint any receipt
- Cashier cannot see discounts or aggregate financial data

### 🎨 Modern UI/UX
- Built with Tailwind CSS and Alpine.js
- Smooth transitions and modern layout
- Permission-aware sidebar and pages
- Responsive design for all devices

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and npm
- SQLite (or MySQL/PostgreSQL)

### Setup Steps

1. **Install PHP Dependencies**
   ```bash
   composer install
   ```

2. **Install Frontend Dependencies**
   ```bash
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Build Frontend Assets**
   ```bash
   npm run build
   ```

6. **Start Development Server**
   ```bash
   php artisan serve
   npm run dev
   ```

## Default Credentials

### Super Admin
- **Email**: admin@globalcollege.edu
- **Password**: password

### Cashier
- **Email**: cashier@globalcollege.edu
- **Password**: password

## Currency
- **Currency**: Kenyan Shillings (KES)
- All amounts displayed in KES format

## System Modules

### Sidebar Navigation
- **Dashboard**: Overview with statistics
- **Students**: Student management
- **Billing**: Process payments (with hidden discount logic and term selection)
- **Courses**: Course management
- **Receipts**: View and print receipts
- **Mobile Dashboard**: Mobile-optimized Super Admin dashboard (Super Admin only)
- **Reports**: Financial reports with term filtering (Super Admin only)
- **Users & Roles**: User and role management (Super Admin only)
- **Settings**: System settings

## Key Features Explained

### Hidden Discount Logic
The system implements a critical business requirement where:
1. Courses have a base price stored in the database
2. Cashiers select a student and course
3. Cashiers input ONLY the final agreed payment amount
4. The system automatically calculates: `discount = base_price - amount_paid`
5. Cashiers NEVER see:
   - Course base prices
   - Discount amounts
   - Whether a discount exists
6. Only Super Admin can view:
   - Base prices
   - Discount amounts
   - Full financial reports

### Receipt Generation
- Automatically generated when payment is processed
- Unique receipt numbers
- Professional formatting
- Print-optimized layout
- Different views for Admin vs Cashier

### Reports
- Filter by date range
- View daily, weekly, monthly, or custom periods
- See total payments, base prices, and discounts
- Export capabilities (can be extended)

## Security

- Role-based access control enforced at both UI and backend levels
- Unauthorized data and actions are completely hidden
- Clean, modular, and scalable architecture
- Password hashing and secure authentication

## Technology Stack

- **Backend**: Laravel 12
- **Frontend**: Tailwind CSS 4, Alpine.js 3
- **Database**: SQLite (configurable to MySQL/PostgreSQL)
- **Build Tool**: Vite

## License

MIT License

## Support

For issues or questions, please contact the development team.
