# Localization Documentation

## Overview
This Laravel project supports localization in both English (en) and Arabic (ar) languages.

## Supported Languages
- English (en) - Default language
- Arabic (ar)

## How Language Detection Works

The `SetLocale` middleware automatically detects and sets the appropriate language based on the following priority:

1. **Accept-Language Header**: Send the language code in the `Accept-Language` HTTP header
2. **Request Parameter**: Include `locale` parameter in request body
3. **Query Parameter**: Add `?locale=ar` to the URL
4. **Default**: Falls back to English if no language is specified

## API Usage Examples

### Setting Language via Header
```bash
curl -X POST http://localhost/api/v1/sendotp \
  -H "Accept-Language: ar" \
  -H "Content-Type: application/json" \
  -d '{"phone_number": "1234567890", "purpose": "register"}'
```

### Setting Language via Query Parameter
```bash
curl -X POST "http://localhost/api/v1/sendotp?locale=ar" \
  -H "Content-Type: application/json" \
  -d '{"phone_number": "1234567890", "purpose": "register"}'
```

### Setting Language via Request Body
```bash
curl -X POST http://localhost/api/v1/sendotp \
  -H "Content-Type: application/json" \
  -d '{"phone_number": "1234567890", "purpose": "register", "locale": "ar"}'
```

## Testing Localization

### Test Current Locale
```bash
curl -X GET "http://localhost/api/v1/general/locale"
```

### Test Setting Locale
```bash
curl -X POST "http://localhost/api/v1/general/locale" \
  -H "Content-Type: application/json" \
  -d '{"locale": "ar"}'
```

## Available Translation Keys

### Authentication Messages (`auth.php`)
- `invalid_or_expired_otp` - Invalid or expired OTP
- `user_registered_successfully` - User registered successfully
- `invalid_credentials` - Invalid credentials
- `login_successful` - Login successful
- `logout_successful` - Logout successful
- `password_reset_successfully` - Password reset successfully
- `current_password_incorrect` - Current password is incorrect
- `password_changed_successfully` - Password changed successfully

### OTP Messages (`otp.php`)
- `otp_sent_successfully` - OTP sent successfully
- `otp_verified_successfully` - OTP verified successfully
- `invalid_or_expired_otp` - Invalid or expired OTP

### User Messages (`user.php`)
- `profile_updated_successfully` - Profile updated successfully
- `fcm_token_registered_successfully` - FCM token registered successfully

### Upload Messages (`upload.php`)
- `uploaded_successfully` - Uploaded Successfully

### Admin Messages (`admin.php`)
- `user_deleted_successfully` - User with ID: :id deleted successfully
- `user_status_updated` - User with ID: :id has been :status
- `notification_already_sent_cannot_delete` - Notification with ID: :id is already sent and cannot be deleted
- `notification_already_sent` - Notification with ID: :id is already sent

## File Structure
```
resources/lang/
├── en/
│   ├── auth.php
│   ├── otp.php
│   ├── user.php
│   ├── upload.php
│   ├── admin.php
│   ├── validation.php
│   └── attributes.php
└── ar/
    ├── auth.php
    ├── otp.php
    ├── user.php
    ├── upload.php
    ├── admin.php
    ├── validation.php
    └── attributes.php
```

## Adding New Translations

1. Add the key-value pair to the appropriate language file in `resources/lang/en/` and `resources/lang/ar/`
2. Use the translation in your controller: `__('file.key')`
3. For parametric translations, use: `__('file.key', ['param' => $value])`

## Example Response
```json
{
  "message": "تم تسجيل الدخول بنجاح", // Arabic
  "token": "...",
  "user": {...}
}
```

```json
{
  "message": "Login successful.", // English
  "token": "...",
  "user": {...}
}
```
