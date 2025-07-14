-- =====================================================
-- CamGovCA Database - Default Data Insertion
-- =====================================================

USE camgovca_db;

-- =====================================================
-- INSERT DEFAULT SYSTEM SETTINGS
-- =====================================================

INSERT INTO system_settings (setting_key, setting_value, setting_type, description, is_public) VALUES
('site_name', 'CamGovCA - Autorité de Certification Gouvernementale du Cameroun', 'string', 'Website name', TRUE),
('site_description', 'Public Key Infrastructure Center - ANTIC - CAMEROON', 'string', 'Website description', TRUE),
('contact_email', 'pki@antic.cm', 'string', 'Contact email address', TRUE),
('contact_phone', '+237 242 08 64 97', 'string', 'Contact phone number', TRUE),
('contact_fax', '222 20 39 31', 'string', 'Contact fax number', TRUE),
('default_certificate_validity_days', '365', 'integer', 'Default certificate validity period in days', FALSE),
('max_certificate_validity_days', '1095', 'integer', 'Maximum certificate validity period in days', FALSE),
('min_key_size', '2048', 'integer', 'Minimum RSA key size', FALSE),
('max_key_size', '4096', 'integer', 'Maximum RSA key size', FALSE),
('crl_update_interval_hours', '24', 'integer', 'CRL update interval in hours', FALSE),
('session_timeout_minutes', '30', 'integer', 'User session timeout in minutes', FALSE),
('max_login_attempts', '5', 'integer', 'Maximum failed login attempts before account lock', FALSE),
('account_lockout_duration_minutes', '30', 'integer', 'Account lockout duration in minutes', FALSE);

-- =====================================================
-- INSERT DEFAULT CERTIFICATE POLICIES
-- =====================================================

INSERT INTO certificate_policies (policy_name, policy_oid, policy_description, cert_type, key_size_min, key_size_max, validity_period_days, signature_algorithms, status) VALUES
('Individual Certificate Policy', '1.3.6.1.4.1.12345.1.1', 'Policy for individual digital certificates', 'individual', 2048, 4096, 365, '["sha256WithRSAEncryption", "sha384WithRSAEncryption"]', 'active'),
('Organization Certificate Policy', '1.3.6.1.4.1.12345.1.2', 'Policy for organizational digital certificates', 'organization', 2048, 4096, 365, '["sha256WithRSAEncryption", "sha384WithRSAEncryption"]', 'active'),
('SSL Certificate Policy', '1.3.6.1.4.1.12345.1.3', 'Policy for SSL/TLS certificates', 'ssl', 2048, 4096, 365, '["sha256WithRSAEncryption", "sha384WithRSAEncryption"]', 'active'),
('Code Signing Certificate Policy', '1.3.6.1.4.1.12345.1.4', 'Policy for code signing certificates', 'code_signing', 2048, 4096, 365, '["sha256WithRSAEncryption", "sha384WithRSAEncryption"]', 'active'),
('Email Certificate Policy', '1.3.6.1.4.1.12345.1.5', 'Policy for email certificates', 'email', 2048, 4096, 365, '["sha256WithRSAEncryption", "sha384WithRSAEncryption"]', 'active');

-- =====================================================
-- INSERT DEFAULT EMAIL TEMPLATES
-- =====================================================

INSERT INTO email_templates (template_name, subject, body_template, language) VALUES
('certificate_issued', 'Votre certificat numérique a été émis', 'Bonjour {first_name},\n\nVotre certificat numérique a été émis avec succès.\n\nNuméro de série: {serial_number}\nDate de validité: {valid_from} à {valid_to}\n\nCordialement,\nL''équipe CamGovCA', 'fr'),
('certificate_renewed', 'Votre certificat numérique a été renouvelé', 'Bonjour {first_name},\n\nVotre certificat numérique a été renouvelé avec succès.\n\nNuméro de série: {serial_number}\nNouvelle date de validité: {valid_from} à {valid_to}\n\nCordialement,\nL''équipe CamGovCA', 'fr'),
('certificate_revoked', 'Votre certificat numérique a été révoqué', 'Bonjour {first_name},\n\nVotre certificat numérique a été révoqué.\n\nNuméro de série: {serial_number}\nRaison: {revocation_reason}\n\nCordialement,\nL''équipe CamGovCA', 'fr'),
('password_reset', 'Réinitialisation de votre mot de passe', 'Bonjour {first_name},\n\nVous avez demandé la réinitialisation de votre mot de passe.\n\nLien de réinitialisation: {reset_link}\n\nCe lien expire dans 24 heures.\n\nCordialement,\nL''équipe CamGovCA', 'fr');

-- =====================================================
-- INSERT DEFAULT FAQ ENTRIES
-- =====================================================

INSERT INTO faq_entries (question, answer, category, language, sort_order) VALUES
('Qu''est-ce qu''un certificat numérique ?', 'Un certificat numérique est un document électronique qui atteste l''identité d''une personne ou d''une organisation sur Internet.', 'general', 'fr', 1),
('Comment obtenir un certificat numérique ?', 'Pour obtenir un certificat numérique, vous devez vous adresser à une Autorité d''Enregistrement (AE) agréée par l''ANTIC.', 'certificates', 'fr', 1),
('Quels sont les types de certificats disponibles ?', 'Les types de certificats disponibles sont: certificat individuel, certificat organisation, certificat SSL, certificat de signature de code, et certificat email.', 'certificates', 'fr', 2),
('Combien coûte un certificat numérique ?', 'Les tarifs varient selon le type de certificat et l''Autorité d''Enregistrement. Contactez une AE pour connaître les tarifs exacts.', 'pricing', 'fr', 1),
('Comment renouveler mon certificat ?', 'Vous pouvez renouveler votre certificat en ligne via notre plateforme ou en vous adressant à votre Autorité d''Enregistrement.', 'certificates', 'fr', 3),
('What is a digital certificate?', 'A digital certificate is an electronic document that attests to the identity of a person or organization on the Internet.', 'general', 'en', 1),
('How to obtain a digital certificate?', 'To obtain a digital certificate, you must contact a Registration Authority (RA) approved by ANTIC.', 'certificates', 'en', 1),
('What types of certificates are available?', 'Available certificate types are: individual certificate, organization certificate, SSL certificate, code signing certificate, and email certificate.', 'certificates', 'en', 2);

-- =====================================================
-- INSERT DEFAULT CERTIFICATE AUTHORITIES
-- =====================================================

INSERT INTO certificate_authorities (ca_name, ca_type, ca_dn, ca_serial, public_key, valid_from, valid_to, status) VALUES
('CamRootCA', 'root', 'CN=CamRootCA,OU=Cameroon Root Certification Authority,O=ANTIC,C=CM', 'ROOT001', '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', '2015-01-01 00:00:00', '2035-01-01 00:00:00', 'active'),
('CamGovCA', 'intermediate', 'CN=CamGovCA,OU=Cameroon Government Certification Authority,O=ANTIC CA,C=CM', 'INT001', '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', '2015-01-01 00:00:00', '2030-01-01 00:00:00', 'active');

-- =====================================================
-- INSERT DEFAULT REGISTRATION AUTHORITIES
-- =====================================================

INSERT INTO registration_authorities (ra_name, ra_code, ra_type, address, city, region, phone, email, contact_person, status) VALUES
('ANTIC Central RA', 'RA001', 'central', 'Yaoundé, Cameroun', 'Yaoundé', 'Centre', '+237 242 08 64 97', 'ra@antic.cm', 'Directeur Général', 'active'),
('Douala Regional RA', 'RA002', 'regional', 'Douala, Cameroun', 'Douala', 'Littoral', '+237 233 42 15 67', 'ra.douala@antic.cm', 'Responsable Régional', 'active'),
('Garoua Regional RA', 'RA003', 'regional', 'Garoua, Cameroun', 'Garoua', 'Nord', '+237 222 27 14 32', 'ra.garoua@antic.cm', 'Responsable Régional', 'active');

-- =====================================================
-- INSERT DEFAULT ORGANIZATIONS
-- =====================================================

INSERT INTO organizations (org_name, org_type, registration_number, address, city, phone, email, contact_person, status) VALUES
('Ministère des Finances', 'government', 'MINFIN001', 'Yaoundé, Cameroun', 'Yaoundé', '+237 222 23 45 67', 'contact@minfin.cm', 'Directeur des Systèmes d''Information', 'active'),
('Banque des États de l''Afrique Centrale', 'government', 'BEAC001', 'Yaoundé, Cameroun', 'Yaoundé', '+237 222 20 12 34', 'contact@beac.cm', 'Directeur Technique', 'active'),
('Société Nationale des Hydrocarbures', 'government', 'SNH001', 'Douala, Cameroun', 'Douala', '+237 233 42 15 67', 'contact@snh.cm', 'Directeur IT', 'active');

-- =====================================================
-- INSERT DEFAULT ADMIN USER
-- =====================================================

-- Note: In production, use proper password hashing
INSERT INTO users (username, email, password_hash, first_name, last_name, user_type, status) VALUES
('admin', 'admin@camgovca.cm', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrateur', 'Système', 'admin', 'active');

-- =====================================================
-- INSERT DEFAULT PAGES
-- =====================================================

INSERT INTO pages (page_title, page_slug, page_content, meta_description, language, status, created_by) VALUES
('Accueil', 'accueil', '<h1>Bienvenue sur CamGovCA</h1><p>Autorité de Certification Gouvernementale du Cameroun</p>', 'CamGovCA - Autorité de Certification Gouvernementale du Cameroun', 'fr', 'published', 1),
('À propos de l''ANTIC', 'a-propos-de-l-antic', '<h1>À propos de l''ANTIC</h1><p>L''Agence Nationale des Technologies de l''Information et de la Communication...</p>', 'Informations sur l''ANTIC', 'fr', 'published', 1),
('Guide du Certificat', 'guide-du-certificat', '<h1>Guide du Certificat</h1><p>Guide complet sur les certificats numériques...</p>', 'Guide des certificats numériques', 'fr', 'published', 1),
('Services et Produits', 'services-et-produits', '<h1>Services et Produits</h1><p>Nos services de certification...</p>', 'Services de certification numérique', 'fr', 'published', 1),
('Contactez-nous', 'contactez-nous', '<h1>Contactez-nous</h1><p>Pour toute question, contactez-nous...</p>', 'Contact CamGovCA', 'fr', 'published', 1);

-- =====================================================
-- INSERT DEFAULT FAQ CATEGORIES
-- =====================================================

-- Additional FAQ entries for different categories
INSERT INTO faq_entries (question, answer, category, language, sort_order) VALUES
('Comment installer mon certificat ?', 'Suivez les instructions d''installation fournies avec votre certificat. Vous pouvez également consulter notre guide d''installation.', 'installation', 'fr', 1),
('Mon certificat a expiré, que faire ?', 'Vous devez renouveler votre certificat avant expiration. Contactez votre Autorité d''Enregistrement pour le renouvellement.', 'renewal', 'fr', 1),
('Comment révoquer un certificat ?', 'Pour révoquer un certificat, contactez l''Autorité d''Enregistrement Centrale de l''ANTIC.', 'revocation', 'fr', 1),
('Quels sont les algorithmes de sécurité supportés ?', 'Nous supportons les algorithmes RSA avec SHA-256 et SHA-384.', 'security', 'fr', 1),
('Comment vérifier la validité d''un certificat ?', 'Utilisez notre service de vérification en temps réel ou consultez les listes de révocation (CRL).', 'verification', 'fr', 1);

-- =====================================================
-- INSERT SAMPLE CERTIFICATE REQUESTS
-- =====================================================

INSERT INTO certificate_requests (user_id, request_type, cert_type, subject_dn, public_key, key_size, signature_algorithm, auth_code, ref_code, status) VALUES
(1, 'new', 'individual', 'CN=John Doe,OU=Individual,O=CamGovCA,C=CM', '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'sha256WithRSAEncryption', 'AUTH123', 'REF456', 'pending');

-- =====================================================
-- INSERT SAMPLE CERTIFICATES
-- =====================================================

INSERT INTO certificates (serial_number, subject_dn, issuer_dn, public_key, key_size, signature_algorithm, certificate_pem, valid_from, valid_to, cert_type, user_id, ca_id, auth_code, ref_code) VALUES
('CERT20250101001', 'CN=John Doe,OU=Individual,O=CamGovCA,C=CM', 'CN=CamGovCA,OU=Cameroon Government Certification Authority,O=ANTIC CA,C=CM', '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'sha256WithRSAEncryption', '-----BEGIN CERTIFICATE-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END CERTIFICATE-----', '2025-01-01 00:00:00', '2026-01-01 00:00:00', 'individual', 1, 2, 'AUTH123', 'REF456');

-- =====================================================
-- INSERT SAMPLE AUDIT LOGS
-- =====================================================

INSERT INTO audit_logs (user_id, action, table_name, record_id, ip_address, user_agent) VALUES
(1, 'LOGIN', 'users', 1, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(1, 'CREATE', 'certificates', 1, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

-- =====================================================
-- INSERT SAMPLE CERTIFICATE OPERATIONS
-- =====================================================

INSERT INTO certificate_operations (cert_id, user_id, operation_type, operation_details, status, ip_address) VALUES
(1, 1, 'issue', '{"cert_type": "individual", "auth_code": "AUTH123", "ref_code": "REF456"}', 'success', '192.168.1.100'),
(1, 1, 'verify', '{"verification_method": "online"}', 'success', '192.168.1.100');

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================

-- Display summary of inserted data
SELECT 'System Settings' as table_name, COUNT(*) as record_count FROM system_settings
UNION ALL
SELECT 'Certificate Policies', COUNT(*) FROM certificate_policies
UNION ALL
SELECT 'Email Templates', COUNT(*) FROM email_templates
UNION ALL
SELECT 'FAQ Entries', COUNT(*) FROM faq_entries
UNION ALL
SELECT 'Certificate Authorities', COUNT(*) FROM certificate_authorities
UNION ALL
SELECT 'Registration Authorities', COUNT(*) FROM registration_authorities
UNION ALL
SELECT 'Organizations', COUNT(*) FROM organizations
UNION ALL
SELECT 'Users', COUNT(*) FROM users
UNION ALL
SELECT 'Pages', COUNT(*) FROM pages
UNION ALL
SELECT 'Certificate Requests', COUNT(*) FROM certificate_requests
UNION ALL
SELECT 'Certificates', COUNT(*) FROM certificates
UNION ALL
SELECT 'Audit Logs', COUNT(*) FROM audit_logs
UNION ALL
SELECT 'Certificate Operations', COUNT(*) FROM certificate_operations; 