# Database Seeding Documentation

This document provides information about the database seeders and factories available in this Laravel template.

## 🌱 Available Seeders

### UserSeeder
Seeds the database with sample users including admins and regular users.

**What it creates:**
- 2 Admin users with known credentials
- 6 Regular users with different statuses (active, banned, inactive)
- 20 Additional random users via factory

**Test Credentials:**
- Admin: `+1234567890` / Password: `password123`
- User: `+1234567892` / Password: `password123`

### NotificationSeeder  
Creates sample notifications for testing the notification system.

**What it creates:**
- 3 General notifications (sent to all users)
- 3 Specific user notifications (sent to individual users)
- 2 Scheduled notifications (for future delivery)

**Notification Types:**
- Welcome messages
- System maintenance alerts
- Feature announcements
- User-specific reminders

### FcmTokenSeeder
Creates FCM tokens for testing push notifications.

**What it creates:**
- FCM tokens for the first 10 users
- Mix of Android and iOS device types
- 1-2 tokens per user (simulating multiple devices)

## 🏭 Available Factories

### UserFactory
Generates realistic user data with proper attributes.

**Available States:**
- `active()` - Creates active users
- `admin()` - Creates admin users
- `user()` - Creates regular users  
- `banned()` - Creates banned users

**Usage Examples:**
```php
User::factory()->count(10)->create();
User::factory()->admin()->count(3)->create();
User::factory()->banned()->create();
```

### NotificationFactory
Generates sample notifications with bilingual content.

**Available States:**
- `sent()` - Creates sent notifications
- `pending()` - Creates pending notifications
- `scheduled()` - Creates scheduled notifications
- `forAllUsers()` - Creates notifications for all users
- `forSpecificUser($userId)` - Creates user-specific notifications

**Usage Examples:**
```php
Notification::factory()->count(5)->create();
Notification::factory()->sent()->forAllUsers()->create();
Notification::factory()->scheduled()->create();
```

### FcmTokenFactory
Generates FCM tokens for testing push notifications.

**Available States:**
- `android()` - Creates Android device tokens
- `ios()` - Creates iOS device tokens
- `forUser($user)` - Creates token for specific user

**Usage Examples:**
```php
FcmToken::factory()->count(10)->create();
FcmToken::factory()->android()->create();
FcmToken::factory()->forUser($user)->create();
```

## 🚀 Running Seeders

### Seed All Data
```bash
php artisan db:seed
```

### Seed Specific Seeder
```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=NotificationSeeder
php artisan db:seed --class=FcmTokenSeeder
```

### Fresh Migration with Seeding
```bash
php artisan migrate:fresh --seed
```

## 📊 Seeded Data Summary

After running all seeders, you'll have:

| Model | Count | Description |
|-------|-------|-------------|
| Users | ~28 | 2 admins + 26 regular users |
| Notifications | 8 | Mix of general, specific, and scheduled |
| FCM Tokens | ~15 | Tokens for first 10 users |

## 🔐 Test Accounts

### Admin Accounts
1. **Primary Admin**
   - Phone: `+1234567890`
   - Password: `password123`
   - Name: Admin User

2. **Secondary Admin**  
   - Phone: `+1234567891`
   - Password: `password123`
   - Name: Sarah Administrator

### Regular User Accounts
1. **Active User**
   - Phone: `+1234567892`
   - Password: `password123`
   - Name: Ahmed Ali

2. **Banned User**
   - Phone: `+1234567896`
   - Password: `password123`
   - Name: Banned User

## 🌐 Multilingual Content

All seeded content includes both English and Arabic versions:
- User names include Arabic-friendly names
- Notifications have both `en_title`/`ar_title` and `en_body`/`ar_body`
- Proper Unicode support for Arabic text

## 🧪 Testing Features

The seeded data is perfect for testing:

### Authentication
- Login with different user types (admin/user)
- Test banned/inactive user restrictions
- Password reset functionality

### Notifications
- View all-user notifications
- Test user-specific notifications  
- Scheduled notification delivery
- Push notification tokens

### Admin Features
- User management (ban/unban)
- Notification creation and sending
- User filtering and search

### Localization
- Test Arabic/English responses
- Multilingual notification content

## 🔄 Refreshing Data

To refresh all seeded data:
```bash
php artisan migrate:fresh --seed
```

To add more sample data without losing existing:
```bash
php artisan db:seed --class=UserSeeder
```

## 📝 Customization

You can modify the seeders to:
- Change default passwords
- Add more sample users
- Create different notification types
- Adjust user statuses and types

Each seeder is well-documented and easy to customize for your specific needs.
