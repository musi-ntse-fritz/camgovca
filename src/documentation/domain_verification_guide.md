# Domain Verification Guide for CamGovCA

## 🔧 Resend Domain Verification

### Current Setup (Testing)
- **From Email:** `onboarding@resend.dev` (Resend's verified domain)
- **Status:** ✅ Working for testing

### Production Setup (Recommended)
To use `noreply@camgovca.cm` in production, you need to verify your domain:

#### Step 1: Add Domain to Resend
1. Go to [https://resend.com/domains](https://resend.com/domains)
2. Click "Add Domain"
3. Enter: `camgovca.cm`
4. Click "Add Domain"

#### Step 2: Configure DNS Records
Resend will provide DNS records to add to your domain:

**Example DNS Records:**
```
Type: TXT
Name: @
Value: resend-verification=abc123...

Type: CNAME
Name: resend
Value: track.resend.com
```

#### Step 3: Wait for Verification
- DNS changes can take up to 24 hours
- Resend will automatically verify when DNS is correct

#### Step 4: Update Configuration
Once verified, update `config/email_config.php`:
```php
'resend' => [
    'api_key' => 're_NbTtBomg_Q91YxYyCnviYX93qP45x3ZFn',
    'from_email' => 'noreply@camgovca.cm', // Now verified
    'from_name' => 'CamGovCA'
],
```

## 🧪 Testing Without Domain Verification

### Current Working Setup
- ✅ **From:** `onboarding@resend.dev`
- ✅ **To:** Your real email address
- ✅ **Functionality:** Full 2FA email delivery

### Test URLs
- **Email Service Test:** `http://localhost:8080/test_email_service.php`
- **2FA Test:** `http://localhost:8080/test_2fa_basic.php`
- **Complete 2FA Test:** `http://localhost:8080/test_2fa_comprehensive.php`

## 🚀 Alternative: Use Gmail SMTP (Free)

If you prefer not to verify the domain, you can use Gmail SMTP:

### Gmail SMTP Setup
1. Enable 2-factor authentication on your Gmail account
2. Generate an App Password
3. Configure in `config/email_config.php`:

```php
'gmail' => [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => 'your-email@gmail.com',
    'smtp_password' => 'your-app-password',
    'from_email' => 'your-email@gmail.com',
    'from_name' => 'CamGovCA'
],
```

## 📧 Current Status
- ✅ **Resend API:** Working with `onboarding@resend.dev`
- ✅ **2FA Emails:** Fully functional
- ✅ **Testing:** Complete
- ⏳ **Production:** Ready after domain verification

## 🔗 Quick Links
- [Resend Domains](https://resend.com/domains)
- [Resend Documentation](https://resend.com/docs)
- [Email Service Test](http://localhost:8080/test_email_service.php) 