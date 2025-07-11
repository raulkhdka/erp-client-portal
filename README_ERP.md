# ERP System - Client Management Platform

A comprehensive ERP system built with Laravel for managing clients, employees, and dynamic forms. This system provides role-based access control and supports multiple contact methods, document management, and dynamic data collection forms.

## Features

### 🔐 Authentication & Role Management
- **Three User Roles:**
  - **Admin**: Full system access, can manage clients, employees, and forms
  - **Employee**: Access to assigned client data with restricted permissions
  - **Client**: Access to their own dashboard, documents, and forms

### 👥 Client Management
- Complete client profiles with company information
- **Multiple Contact Methods:**
  - Multiple phone numbers (mobile, office, home, fax)
  - Multiple email addresses (primary, billing, support, personal)
- Document and image storage
- Service tracking (accounting, payroll, tax preparation, etc.)
- Status management (active, inactive, suspended)

### 👨‍💼 Employee Management
- Employee profiles linked to user accounts
- Department and position tracking
- Salary and hire date management
- **Client Access Control:**
  - Assign specific employees to specific clients
  - Granular permission system
  - Access expiration dates

### 📋 Dynamic Forms System
- Create custom forms with various field types:
  - Text, Email, Number, Date
  - Select, Checkbox, Radio buttons
  - Textarea, File uploads
- Form validation and required field settings
- Client form submissions and response tracking
- Public form links for client access

### 📊 Dashboard Features
- **Admin Dashboard:** System overview, client statistics, recent activities
- **Employee Dashboard:** Assigned clients, permissions overview
- **Client Dashboard:** Documents, form submissions, company information

## Technology Stack

- **Backend:** Laravel 12.x
- **Database:** SQLite (configurable to MySQL/PostgreSQL)
- **Frontend:** Bootstrap 5, Font Awesome icons
- **Authentication:** Laravel's built-in authentication

## Database Schema

### Core Tables
- `users` - Authentication and basic user information
- `clients` - Client company information and settings
- `employees` - Employee profiles and permissions
- `client_employee_accesses` - Access control between employees and clients

### Contact Information
- `client_phones` - Multiple phone numbers per client
- `client_emails` - Multiple email addresses per client

### File Management
- `client_documents` - Document storage and metadata
- `client_images` - Image storage and metadata

### Dynamic Forms
- `dynamic_forms` - Form definitions and settings
- `dynamic_form_fields` - Individual form field configurations
- `dynamic_form_responses` - Client form submissions

## Installation & Setup

1. **Clone and Install Dependencies:**
   ```bash
   composer install
   npm install
   ```

2. **Environment Setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup:**
   ```bash
   php artisan migrate
   php artisan db:seed --class=AdminUserSeeder
   ```

4. **Start Development Server:**
   ```bash
   php artisan serve
   ```

## Default Login Credentials

After running the seeder, you can log in with these accounts:

- **Admin:** admin@erp.com / password
- **Employee:** employee@erp.com / password  
- **Client 1:** client@erp.com / password
- **Client 2:** mike@xyzltd.com / password

## Key Features Implementation

### Multi-Contact Support
Each client can have:
- Multiple phone numbers with types (mobile, office, home, fax)
- Multiple email addresses with types (primary, billing, support, personal)
- Primary contact designation for each type

### Access Control System
- Employees can be assigned to specific clients
- Granular permissions per employee-client relationship
- Access expiration dates for temporary assignments
- Permission types: view_accounting, edit_payroll, view_documents, etc.

### Dynamic Form Builder
- Admin creates forms with custom fields
- Various field types with validation rules
- Public form URLs for client access
- Response tracking and management

### Service Management
Clients can be assigned multiple services:
- Accounting
- Payroll
- Tax Preparation
- Bookkeeping
- HR Consulting
- Financial Planning

## File Structure

```
app/
├── Http/Controllers/
│   ├── Auth/LoginController.php
│   ├── DashboardController.php
│   ├── ClientController.php
│   ├── EmployeeController.php
│   └── DynamicFormController.php
├── Models/
│   ├── User.php
│   ├── Client.php
│   ├── Employee.php
│   ├── ClientPhone.php
│   ├── ClientEmail.php
│   ├── ClientDocument.php
│   ├── ClientImage.php
│   ├── ClientEmployeeAccess.php
│   ├── DynamicForm.php
│   ├── DynamicFormField.php
│   └── DynamicFormResponse.php
└── Http/Middleware/
    └── RoleMiddleware.php

resources/views/
├── layouts/app.blade.php
├── auth/login.blade.php
├── dashboard/
│   ├── admin.blade.php
│   ├── employee.blade.php
│   └── client.blade.php
├── clients/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── show.blade.php
│   └── edit.blade.php
└── dynamic-forms/
    └── index.blade.php
```

## Security Features

- Role-based access control
- CSRF protection on all forms
- Input validation and sanitization
- Password hashing
- Session management
- Database foreign key constraints

## Future Enhancements

- File upload functionality for documents and images
- Email notifications for form submissions
- Advanced reporting and analytics
- API endpoints for mobile app integration
- Multi-language support
- Advanced permission system
- Audit trails and activity logging

## Contributing

This ERP system is designed to be extensible. Key areas for enhancement:
- Additional form field types
- Advanced reporting features
- Integration with external services
- Mobile-responsive improvements
- Performance optimizations

## License

This project is open-source and available under the MIT License.
