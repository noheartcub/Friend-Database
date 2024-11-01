## **Version 2.0.0**

### **Setup Process Enhancements**

- **Setup Script (`setup.php`)**: Enhanced the setup process to initialize the application with database and admin details.
  - **Automatic SQL Import**: The setup script now imports `database.sql` directly, simplifying table creation and initial data setup.
  - **File Auto-Deletion After Setup**: `setup.php` and `database.sql` are automatically deleted upon successful setup for improved security.
  - **Admin Account Setup**: Allows creation of an admin account with username, email, first name, last name, and password fields.
  - **Configuration Check**: Prevents re-running setup if `config.php` is already populated with database configuration.

### **Configuration File Updates**

- **Config File Placeholder Setup**: Added placeholders in `config.php` for database credentials, allowing `setup.php` to run only if needed.
- **Connection Error Feedback**: Improved error handling for database connection issues during setup to guide user action.

### **Database Modifications**

- **`database.sql` Structure Updates**:
  - **New and Corrected Tables**: Includes missing tables (`activity_logs`, `roles`, `permissions`, `settings`, `users`) with corrected field definitions.
  - **Fixed Foreign Key Constraints**: Addressed foreign key issues, enabling `role_permissions` and related tables to be created without errors.
  - **Default Field Values**: Set default values for required fields (e.g., `first_name`, `last_name`) to ensure smooth setup.

### **Admin Panel & User Management**

- **Fixed Admin Role Assignment**: Resolved issues blocking user assignment to the `admin` role.
- **Edit User Page**: Added a page for admins to edit user details, including roles, status, and other key user settings.
- **Password-Protected User Deletion**: User deletion now requires password confirmation from the admin for added security.
- **Settings Management for Admin**: Created `yoursettings.php`, allowing admins to update personal details, including username, email, password, and profile image.

### **Security Enhancements**

- **Critical Action Confirmation**: Password confirmation is now required for sensitive actions, such as deleting a user account.
- **Access Control by Role**: Limited access to setup, user management, and other admin-only features.
- **Secure Error Handling**: Improved error handling and messaging to avoid exposing sensitive information during setup and configuration.

### **General Improvements**

- **UI and Form Standardization**: Improved the structure of forms across setup, user management, and settings for a consistent user experience.
- **Form Validation**: Added required field validation to forms, particularly in setup and admin actions.
- **Changelog Documentation**: Updated changelog to provide a clear record of all modifications, ideal for version control in repositories.
