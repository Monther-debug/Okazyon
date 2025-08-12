# API Routes Summary

This document provides a complete mapping of all controllers, their methods, and corresponding API endpoints.

## 🔐 User API Routes (`/api/v1/`)

### AuthController
| Method | Endpoint | HTTP Method | Middleware | Description |
|--------|----------|-------------|------------|-------------|
| `register()` | `/register` | POST | `throttle:register` | Register new user |
| `login()` | `/login` | POST | `throttle:login` | Login user |
| `logout()` | `/logout` | POST | `auth:sanctum` | Logout user |
| `reSetPassword()` | `/reset-password` | POST | `throttle:reset-password` | Reset user password |

### OTPController  
| Method | Endpoint | HTTP Method | Middleware | Description |
|--------|----------|-------------|------------|-------------|
| `generateOTP()` | `/sendotp` | POST | `throttle:otp` | Send OTP to phone |
| `verifyOTP()` | `/verifyotp` | POST | `throttle:otp` | Verify OTP code |

### UserController
| Method | Endpoint | HTTP Method | Middleware | Description |
|--------|----------|-------------|------------|-------------|
| `profile()` | `/profile` | GET | `auth:sanctum` | Get user profile |
| `updateProfile()` | `/profile` | PUT | `auth:sanctum` | Update user profile |
| `changePassword()` | `/change-password` | POST | `auth:sanctum` | Change user password |

### FCMController
| Method | Endpoint | HTTP Method | Middleware | Description |
|--------|----------|-------------|------------|-------------|
| `registerToken()` | `/fcm-token` | POST | `auth:sanctum` | Register FCM token |

---

## 🛡️ Admin API Routes (`/api/v1/admin/`)

### UserController
| Method | Endpoint | HTTP Method | Middleware | Description |
|--------|----------|-------------|------------|-------------|
| `index()` | `/users` | GET | `auth:sanctum` | List all users |
| `show()` | `/users/{user}` | GET | `auth:sanctum` | Show specific user |
| `destroy()` | `/users/{user}` | DELETE | `auth:sanctum` | Delete user |
| `alterBan()` | `/users/{user}/alter-ban` | POST | `auth:sanctum` | Ban/Unban user |

### NotificationController
| Method | Endpoint | HTTP Method | Middleware | Description |
|--------|----------|-------------|------------|-------------|
| `index()` | `/notifications` | GET | `auth:sanctum` | List all notifications |
| `store()` | `/notifications` | POST | `auth:sanctum` | Create new notification |
| `show()` | `/notifications/{notification}` | GET | `auth:sanctum` | Show specific notification |
| `update()` | `/notifications/{notification}` | PUT/PATCH | `auth:sanctum` | Update notification |
| `destroy()` | `/notifications/{notification}` | DELETE | `auth:sanctum` | Delete notification |
| `send()` | `/notifications/{notification}/send` | POST | `auth:sanctum` | Send notification |

---

## 🌐 General API Routes (`/api/v1/general/`)

### TempUploadController
| Method | Endpoint | HTTP Method | Middleware | Description |
|--------|----------|-------------|------------|-------------|
| `uploadImage()` | `/upload-image` | POST | `auth:sanctum` | Upload temporary image |

### LocalizationController
| Method | Endpoint | HTTP Method | Middleware | Description |
|--------|----------|-------------|------------|-------------|
| `getLocale()` | `/locale` | GET | - | Get current locale info |
| `setLocale()` | `/locale` | POST | - | Set locale for testing |

---

## 📊 Route Coverage Status

✅ **Complete Coverage**: All controller methods have corresponding API routes

### Controllers with All Routes Mapped:
- ✅ `User\AuthController` - 4/4 methods
- ✅ `User\OTPController` - 2/2 methods  
- ✅ `User\UserController` - 3/3 methods
- ✅ `User\FCMController` - 1/1 methods
- ✅ `Admin\UserController` - 4/4 methods
- ✅ `Admin\NotificationController` - 6/6 methods
- ✅ `General\TempUploadController` - 1/1 methods
- ✅ `General\LocalizationController` - 2/2 methods

### Total Route Count:
- **User Routes**: 10 routes
- **Admin Routes**: 10 routes  
- **General Routes**: 3 routes
- **Total**: 23 API endpoints

---

## 🔧 Rate Limiting

The following rate limiters are configured:
- `throttle:otp` - 5 requests per minute per phone number
- `throttle:register` - 10 requests per minute per IP
- `throttle:login` - 10 requests per minute per phone number
- `throttle:reset-password` - 5 requests per minute per IP
- `throttle:check-phone-number` - 20 requests per minute per IP

---

## 📋 Missing Features to Consider

While all current controller methods have routes, consider adding these common API endpoints:

### User Management
- `GET /api/v1/profile/notifications` - Get user notifications
- `POST /api/v1/profile/avatar` - Upload user avatar
- `DELETE /api/v1/profile/avatar` - Remove user avatar

### Admin Features
- `GET /api/v1/admin/dashboard` - Admin dashboard stats
- `GET /api/v1/admin/users/{user}/notifications` - User's notifications
- `POST /api/v1/admin/users/{user}/reset-password` - Admin reset user password

### General Features
- `GET /api/v1/general/health` - Health check endpoint
- `GET /api/v1/general/version` - API version info
- `GET /api/v1/general/config` - Public configuration

This API structure provides a solid foundation for a mobile application with user management, notifications, and file uploads.
