# CamGovCA - Cameroon Government Certificate Authority

A comprehensive Public Key Infrastructure (PKI) management system for Cameroon's government certification authority.

## 🏛️ Overview

CamGovCA is a robust certificate authority management system designed to handle the complete lifecycle of digital certificates for government entities, organizations, and individuals in Cameroon. The system provides secure certificate issuance, management, and validation services.

## ✨ Features

### Core Functionality
- **Certificate Lifecycle Management**: Issue, renew, revoke, suspend, and resume certificates
- **Multi-User Support**: Role-based access control with admin and user roles
- **Organization Management**: Handle multiple organizations and their certificates
- **Audit Logging**: Comprehensive audit trail for all operations
- **Two-Factor Authentication**: Enhanced security with 2FA support

### Security Features
- **PKI Standards Compliance**: Follows X.509 certificate standards
- **Secure Password Management**: Encrypted certificate password handling
- **Session Management**: Secure session handling and timeout
- **Input Validation**: Comprehensive input sanitization and validation

### User Interface
- **Multi-Language Support**: French and English interfaces
- **Responsive Design**: Modern, user-friendly interface
- **Real-time Status**: Live certificate status checking
- **Document Management**: Certificate download and management

## 📁 Project Structure

```
src/
├── public/           # Main application files (PHP, HTML)
├── admin/            # Administrative interface
├── api/              # API endpoints
├── includes/         # Core PHP functions and classes
├── config/           # Configuration files
├── assets/           # Static assets (CSS, JS, images)
├── templates/        # Template files and themes
├── scripts/          # Utility scripts
├── logs/             # Application logs
├── backups/          # Database and file backups
├── error/            # Error pages
├── database/         # Database schema and data
└── documentation/    # Project documentation
```

## 🚀 Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- OpenSSL extension for PHP

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/camgovca.git
   cd camgovca
   ```

2. **Configure the database**
   - Import the database schema: `src/database/camgovca_database_schema.sql`
   - Import initial data: `src/database/camgovca_database_data.sql`

3. **Configure the application**
   - Copy `src/config/database.php.example` to `src/config/database.php`
   - Update database credentials in `src/config/database.php`
   - Configure email settings in `src/config/email_config.php`

4. **Set up web server**
   - Point your web server document root to `src/public/`
   - Ensure proper permissions for logs and uploads directories

5. **Run the setup script**
   ```bash
   php src/scripts/setup_database.php
   ```

## 🔧 Configuration

### Database Configuration
Edit `src/config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'camgovca');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### Email Configuration
Edit `src/config/email_config.php`:
```php
define('SMTP_HOST', 'your_smtp_host');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your_email');
define('SMTP_PASS', 'your_password');
```

## 📖 Usage

### For Administrators
1. Access the admin panel at `/admin/`
2. Manage users, organizations, and certificates
3. Monitor system logs and audit trails
4. Configure system settings

### For Users
1. Register or login to the system
2. Submit certificate requests
3. Manage existing certificates
4. Download and install certificates

## 🔒 Security Considerations

- All passwords are hashed using secure algorithms
- Session management includes timeout and regeneration
- Input validation prevents SQL injection and XSS attacks
- Audit logging tracks all sensitive operations
- Two-factor authentication available for enhanced security

## 📝 API Documentation

The system provides RESTful API endpoints for certificate operations:

- `POST /api/certificate/request` - Request a new certificate
- `GET /api/certificate/{id}` - Get certificate details
- `PUT /api/certificate/{id}/revoke` - Revoke a certificate
- `GET /api/certificate/{id}/status` - Check certificate status

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- Create an issue in the GitHub repository
- Contact the development team
- Check the documentation in `src/documentation/`

## 🔄 Version History

- **v1.0.0** - Initial release with core certificate management
- **v1.1.0** - Added 2FA support and enhanced security
- **v1.2.0** - Multi-language support and UI improvements

## 📞 Contact

- **Project Maintainer**: [Your Name]
- **Email**: [your.email@example.com]
- **Organization**: Cameroon Government IT Department

---

**Note**: This is a government system handling sensitive certificate operations. Please ensure proper security measures are in place before deployment. 