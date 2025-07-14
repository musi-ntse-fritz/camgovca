-- =====================================================
-- CamGovCA Database Schema
-- Cameroon Government Certification Authority
-- =====================================================

CREATE DATABASE IF NOT EXISTS camgovca_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE camgovca_db;

-- =====================================================
-- 1. USER MANAGEMENT TABLES
-- =====================================================

CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    user_type ENUM('admin', 'operator', 'client', 'ra_operator') NOT NULL,
    status ENUM('active', 'inactive', 'suspended', 'pending') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    failed_login_attempts INT DEFAULT 0,
    account_locked BOOLEAN DEFAULT FALSE,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_user_type (user_type),
    INDEX idx_status (status)
);

CREATE TABLE user_profiles (
    profile_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    national_id VARCHAR(50),
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    address TEXT,
    city VARCHAR(100),
    country VARCHAR(100) DEFAULT 'Cameroon',
    organization VARCHAR(200),
    position VARCHAR(100),
    department VARCHAR(100),
    profile_picture VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_national_id (national_id),
    INDEX idx_organization (organization)
);

-- =====================================================
-- 2. CERTIFICATE MANAGEMENT TABLES
-- =====================================================

CREATE TABLE certificate_authorities (
    ca_id INT PRIMARY KEY AUTO_INCREMENT,
    ca_name VARCHAR(200) NOT NULL,
    ca_type ENUM('root', 'intermediate', 'subordinate') NOT NULL,
    ca_dn VARCHAR(500) NOT NULL,
    ca_serial VARCHAR(100) UNIQUE NOT NULL,
    public_key TEXT NOT NULL,
    private_key_path VARCHAR(255),
    certificate_pem TEXT,
    valid_from TIMESTAMP NOT NULL,
    valid_to TIMESTAMP NOT NULL,
    status ENUM('active', 'inactive', 'revoked', 'expired') DEFAULT 'active',
    parent_ca_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_ca_id) REFERENCES certificate_authorities(ca_id),
    INDEX idx_ca_type (ca_type),
    INDEX idx_status (status),
    INDEX idx_valid_to (valid_to)
);

CREATE TABLE certificates (
    cert_id INT PRIMARY KEY AUTO_INCREMENT,
    serial_number VARCHAR(100) UNIQUE NOT NULL,
    subject_dn VARCHAR(500) NOT NULL,
    issuer_dn VARCHAR(500) NOT NULL,
    public_key TEXT NOT NULL,
    key_size INT NOT NULL,
    signature_algorithm VARCHAR(50) NOT NULL,
    certificate_pem TEXT NOT NULL,
    valid_from TIMESTAMP NOT NULL,
    valid_to TIMESTAMP NOT NULL,
    status ENUM('active', 'inactive', 'revoked', 'expired', 'suspended') DEFAULT 'active',
    cert_type ENUM('individual', 'organization', 'ssl', 'code_signing', 'email') NOT NULL,
    user_id INT NOT NULL,
    ca_id INT NOT NULL,
    auth_code VARCHAR(50),
    ref_code VARCHAR(50),
    revocation_reason VARCHAR(200),
    revoked_at TIMESTAMP NULL,
    revoked_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (ca_id) REFERENCES certificate_authorities(ca_id),
    FOREIGN KEY (revoked_by) REFERENCES users(user_id),
    INDEX idx_serial_number (serial_number),
    INDEX idx_status (status),
    INDEX idx_cert_type (cert_type),
    INDEX idx_valid_to (valid_to),
    INDEX idx_auth_code (auth_code),
    INDEX idx_ref_code (ref_code)
);

CREATE TABLE certificate_requests (
    request_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    request_type ENUM('new', 'renewal', 'reissue', 'revocation') NOT NULL,
    cert_type ENUM('individual', 'organization', 'ssl', 'code_signing', 'email') NOT NULL,
    subject_dn VARCHAR(500) NOT NULL,
    public_key TEXT NOT NULL,
    key_size INT NOT NULL,
    signature_algorithm VARCHAR(50) NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') DEFAULT 'pending',
    auth_code VARCHAR(50),
    ref_code VARCHAR(50),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    processed_by INT NULL,
    rejection_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (processed_by) REFERENCES users(user_id),
    INDEX idx_status (status),
    INDEX idx_request_type (request_type),
    INDEX idx_auth_code (auth_code),
    INDEX idx_ref_code (ref_code)
);

-- =====================================================
-- 3. ORGANIZATION MANAGEMENT
-- =====================================================

CREATE TABLE organizations (
    org_id INT PRIMARY KEY AUTO_INCREMENT,
    org_name VARCHAR(200) NOT NULL,
    org_type ENUM('government', 'private', 'ngo', 'foreign') NOT NULL,
    registration_number VARCHAR(100) UNIQUE,
    tax_id VARCHAR(100),
    address TEXT,
    city VARCHAR(100),
    country VARCHAR(100) DEFAULT 'Cameroon',
    phone VARCHAR(20),
    email VARCHAR(100),
    website VARCHAR(200),
    contact_person VARCHAR(100),
    contact_phone VARCHAR(20),
    contact_email VARCHAR(100),
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_org_type (org_type),
    INDEX idx_registration_number (registration_number),
    INDEX idx_status (status)
);

CREATE TABLE organization_users (
    org_user_id INT PRIMARY KEY AUTO_INCREMENT,
    org_id INT NOT NULL,
    user_id INT NOT NULL,
    role VARCHAR(100),
    is_primary_contact BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (org_id) REFERENCES organizations(org_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    UNIQUE KEY unique_org_user (org_id, user_id),
    INDEX idx_org_id (org_id),
    INDEX idx_user_id (user_id)
);

-- =====================================================
-- 4. REGISTRATION AUTHORITY (RA) MANAGEMENT
-- =====================================================

CREATE TABLE registration_authorities (
    ra_id INT PRIMARY KEY AUTO_INCREMENT,
    ra_name VARCHAR(200) NOT NULL,
    ra_code VARCHAR(50) UNIQUE NOT NULL,
    ra_type ENUM('central', 'regional', 'sectoral') NOT NULL,
    address TEXT,
    city VARCHAR(100),
    region VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    contact_person VARCHAR(100),
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ra_code (ra_code),
    INDEX idx_ra_type (ra_type),
    INDEX idx_status (status)
);

CREATE TABLE ra_operators (
    ra_operator_id INT PRIMARY KEY AUTO_INCREMENT,
    ra_id INT NOT NULL,
    user_id INT NOT NULL,
    role VARCHAR(100),
    permissions JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ra_id) REFERENCES registration_authorities(ra_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    UNIQUE KEY unique_ra_user (ra_id, user_id),
    INDEX idx_ra_id (ra_id),
    INDEX idx_user_id (user_id)
);

-- =====================================================
-- 5. CERTIFICATE REVOCATION AND STATUS
-- =====================================================

CREATE TABLE certificate_revocation_lists (
    crl_id INT PRIMARY KEY AUTO_INCREMENT,
    ca_id INT NOT NULL,
    crl_number INT NOT NULL,
    this_update TIMESTAMP NOT NULL,
    next_update TIMESTAMP NOT NULL,
    crl_pem TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ca_id) REFERENCES certificate_authorities(ca_id),
    UNIQUE KEY unique_ca_crl (ca_id, crl_number),
    INDEX idx_ca_id (ca_id),
    INDEX idx_next_update (next_update)
);

CREATE TABLE revoked_certificates (
    revocation_id INT PRIMARY KEY AUTO_INCREMENT,
    cert_id INT NOT NULL,
    revocation_date TIMESTAMP NOT NULL,
    revocation_reason ENUM('unspecified', 'key_compromise', 'ca_compromise', 'affiliation_changed', 'superseded', 'cessation_of_operation', 'certificate_hold', 'remove_from_crl', 'privilege_withdrawn', 'aa_compromise') NOT NULL,
    revoked_by INT NOT NULL,
    crl_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cert_id) REFERENCES certificates(cert_id),
    FOREIGN KEY (revoked_by) REFERENCES users(user_id),
    FOREIGN KEY (crl_id) REFERENCES certificate_revocation_lists(crl_id),
    INDEX idx_cert_id (cert_id),
    INDEX idx_revocation_date (revocation_date),
    INDEX idx_revocation_reason (revocation_reason)
);

-- =====================================================
-- 6. AUDIT AND LOGGING TABLES
-- =====================================================

CREATE TABLE audit_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    session_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_table_name (table_name),
    INDEX idx_created_at (created_at)
);

CREATE TABLE certificate_operations (
    operation_id INT PRIMARY KEY AUTO_INCREMENT,
    cert_id INT NULL,
    user_id INT NOT NULL,
    operation_type ENUM('issue', 'renew', 'reissue', 'revoke', 'suspend', 'copy', 'verify', 'password_change') NOT NULL,
    operation_details JSON,
    status ENUM('success', 'failed', 'pending') NOT NULL,
    error_message TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cert_id) REFERENCES certificates(cert_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    INDEX idx_cert_id (cert_id),
    INDEX idx_operation_type (operation_type),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- =====================================================
-- 7. SYSTEM CONFIGURATION TABLES
-- =====================================================

CREATE TABLE system_settings (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key),
    INDEX idx_is_public (is_public)
);

CREATE TABLE certificate_policies (
    policy_id INT PRIMARY KEY AUTO_INCREMENT,
    policy_name VARCHAR(200) NOT NULL,
    policy_oid VARCHAR(100) UNIQUE,
    policy_description TEXT,
    cert_type ENUM('individual', 'organization', 'ssl', 'code_signing', 'email') NOT NULL,
    key_size_min INT NOT NULL,
    key_size_max INT NOT NULL,
    validity_period_days INT NOT NULL,
    signature_algorithms JSON,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_policy_oid (policy_oid),
    INDEX idx_cert_type (cert_type),
    INDEX idx_status (status)
);

-- =====================================================
-- 8. CONTENT MANAGEMENT TABLES
-- =====================================================

CREATE TABLE pages (
    page_id INT PRIMARY KEY AUTO_INCREMENT,
    page_title VARCHAR(200) NOT NULL,
    page_slug VARCHAR(200) UNIQUE NOT NULL,
    page_content LONGTEXT,
    meta_description TEXT,
    meta_keywords TEXT,
    language ENUM('fr', 'en') DEFAULT 'fr',
    status ENUM('published', 'draft', 'archived') DEFAULT 'draft',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id),
    INDEX idx_page_slug (page_slug),
    INDEX idx_language (language),
    INDEX idx_status (status)
);

CREATE TABLE faq_entries (
    faq_id INT PRIMARY KEY AUTO_INCREMENT,
    question TEXT NOT NULL,
    answer LONGTEXT NOT NULL,
    category VARCHAR(100),
    language ENUM('fr', 'en') DEFAULT 'fr',
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_language (language),
    INDEX idx_status (status),
    INDEX idx_sort_order (sort_order)
);

-- =====================================================
-- 9. NOTIFICATION AND COMMUNICATION
-- =====================================================

CREATE TABLE email_templates (
    template_id INT PRIMARY KEY AUTO_INCREMENT,
    template_name VARCHAR(100) UNIQUE NOT NULL,
    subject VARCHAR(200) NOT NULL,
    body_template LONGTEXT NOT NULL,
    language ENUM('fr', 'en') DEFAULT 'fr',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_template_name (template_name),
    INDEX idx_language (language),
    INDEX idx_is_active (is_active)
);

CREATE TABLE email_notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    template_id INT NOT NULL,
    recipient_email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    body LONGTEXT NOT NULL,
    status ENUM('pending', 'sent', 'failed', 'cancelled') DEFAULT 'pending',
    sent_at TIMESTAMP NULL,
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (template_id) REFERENCES email_templates(template_id),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- =====================================================
-- 10. SECURITY AND SESSIONS
-- =====================================================

CREATE TABLE user_sessions (
    session_id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity)
);

CREATE TABLE password_reset_tokens (
    token_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    INDEX idx_token (token),
    INDEX idx_expires_at (expires_at)
); 