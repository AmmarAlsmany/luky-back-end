# Firebase Cloud Messaging (FCM) - Push Notifications Guide v2.0

## 📋 Overview

This document explains how the backend uses Firebase Cloud Messaging (FCM) to send real-time push notifications to the mobile app for payment updates and booking status changes.

---
## 🔧 Backend Implementation Summary

### ✅ Implemented Components

#### 1. FCM Service (`app/Services/FCMService.php`)

The FCM service handles sending push notifications to topics using Firebase Admin SDK.

**Key Methods**:
- `sendPaymentCompleted($booking, $payment)` - Sends payment success notification
- `sendPaymentFailed($booking, $payment, $errorMessage)` - Sends payment failure notification
- `sendPaymentTimeout($booking, $payment)` - Sends payment timeout notification

**Features**:
- ✅ 2-second delay before sending (prevents race condition)
- ✅ User-specific topics: `user_{userId}_booking_{bookingId}_payment`
- ✅ Comprehensive error logging
- ✅ Retry logic with exponential backoff (handled by base `sendToTopic` method)

**Example Topic**: `user_42_booking_123_payment` (only user #42 can subscribe)

#### 2. Payment Webhook Handler (`app/Http/Controllers/Api/PaymentController.php`)

**Updated Methods**:
- `processPaymentStatusV2()` - Processes webhook V2 data and sends FCM on success/failure

**Success Flow** (`PaymentController.php:625-638`):
```php
// After updating payment status to 'completed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentCompleted($payment->booking, $payment);
```

**Failure Flow** (`PaymentController.php:667-680`):
```php
// After updating payment status to 'failed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentFailed($payment->booking, $payment, $failureReason);
```

#### 3. Scheduled Job for Timeout (`app/Console/Commands/CheckExpiredPayments.php`)

**Command**: `php artisan payments:check-expired`

**Schedule**: Runs every minute (configured in `routes/console.php:15`)

**What It Does**:
1. Finds payments older than timeout setting (default 10 minutes)
2. Updates payment status to 'failed' with reason 'Payment timeout expired'
3. Sends FCM notification via `sendPaymentTimeout()`
4. Creates in-app notification for client

**Usage**:
```bash
# Manual test
php artisan payments:check-expired

# Runs automatically via cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

#### 4. Test Endpoints (Admin Only)

Three test endpoints for FCM verification:

| Endpoint | Method | Body | Purpose |
|----------|--------|------|---------|
| `/api/v1/admin/test/fcm/payment-completed` | POST | `{"booking_id": 1}` | Test success notification |
| `/api/v1/admin/test/fcm/payment-failed` | POST | `{"booking_id": 1, "error_message": "Card declined"}` | Test failure notification |
| `/api/v1/admin/test/fcm/payment-timeout` | POST | `{"booking_id": 1}` | Test timeout notification |

**Example Test**:
```bash
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

**Response**:
```json
{
  "success": true,
  "message": "FCM payment completed notification sent",
  "data": {
    "booking_id": 8,
    "payment_id": 12,
    "topic": "user_42_booking_8_payment",
    "fcm_result": true
  }
}
```

---

### 🔐 Security Implementation

**User-Specific Topics** (Implemented in `FCMService.php:266-268`, `305-307`, `341-343`):

Instead of: `booking_{bookingId}_payment` (anyone can subscribe)
We use: `user_{userId}_booking_{bookingId}_payment` (only that user's device)

**Benefits**:
- ✅ Prevents other users from subscribing to someone else's booking updates
- ✅ FCM doesn't validate topic subscribers, so we add user ID for access control
- ✅ Mobile app validates it's the correct user before subscribing

**Mobile App Must**:
1. Only subscribe to topics where `userId` matches logged-in user
2. Validate API response matches notification data
3. Use fallback polling if FCM fails

---

### 📊 Complete Data Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. CLIENT INITIATES PAYMENT                                     │
│    Mobile App → POST /api/v1/bookings/{id}/initiate-payment     │
│    Response: { payment_url: "https://myfatoorah.com/..." }      │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. MOBILE APP SUBSCRIBES TO TOPIC (with 2s delay)               │
│    topic = "user_{userId}_booking_{bookingId}_payment"          │
│    await messaging().subscribeToTopic(topic)                    │
│    await Future.delayed(Duration(seconds: 2))                   │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. USER COMPLETES PAYMENT ON MYFATOORAH                         │
│    MyFatoorah redirects to CallbackUrl OR sends Webhook         │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. BACKEND RECEIVES WEBHOOK/CALLBACK                            │
│    PaymentController::webhook() or ::paymentCallback()          │
│    → processPaymentStatusV2()                                   │
│    → Updates DB: payment.status = 'completed'                   │
│    → FCMService::sendPaymentCompleted() with 2s delay           │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. MOBILE APP RECEIVES FCM NOTIFICATION                         │
│    FirebaseMessaging.onMessage (foreground)                     │
│    FirebaseMessaging.onBackgroundMessage (background)           │
│    → Updates UI, shows success message                          │
│    → Calls API to verify: GET /api/v1/bookings/{id}             │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. CLEANUP                                                       │
│    Mobile app unsubscribes from topic                           │
│    await messaging().unsubscribeFromTopic(topic)                │
└─────────────────────────────────────────────────────────────────┘
```

**Timeout Flow** (if user abandons payment):
```
After 10 minutes → Scheduled job runs → Marks payment as failed
→ FCMService::sendPaymentTimeout() → Mobile receives notification
```

---

### 🧪 Testing Checklist

#### Backend Testing
- [x] FCM service methods created with proper error handling
- [x] Webhook sends FCM on payment success
- [x] Webhook sends FCM on payment failure
- [x] Scheduled job detects expired payments
- [x] Test endpoints available for manual FCM testing
- [x] 2-second delay implemented (race condition prevention)
- [x] User-specific topics implemented (security)
- [x] Comprehensive logging for debugging

#### Mobile Testing (For Mobile Developer)
- [ ] Subscribe to topic before initiating payment
- [ ] Receive notification when payment completes (foreground)
- [ ] Receive notification when payment completes (background)
- [ ] Receive notification when payment completes (app terminated)
- [ ] Receive notification on payment failure
- [ ] Receive notification on payment timeout
- [ ] Verify API response matches FCM data
- [ ] Test fallback polling when FCM unavailable
- [ ] Test unsubscribe after payment completion
- [ ] Test multiple concurrent payments (different bookings)

---

### 🐛 Debugging

#### Check FCM Logs
```bash
# Real-time logs
tail -f storage/logs/laravel.log | grep FCM

# Check specific payment
grep "booking_id.*8" storage/logs/laravel.log | grep FCM
```

#### Test FCM Manually
```bash
# Using admin test endpoint
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

#### Check Scheduled Job
```bash
# Run manually
php artisan payments:check-expired

# Check if scheduled
php artisan schedule:list | grep payment
```

#### Common Issues

| Issue | Solution |
|-------|----------|
| FCM not sending | Check `FCM_CREDENTIALS_PATH` in `.env` |
| Topic subscription fails | Verify topic naming: `user_{userId}_booking_{id}_payment` |
| No subscribers found | Mobile app must subscribe BEFORE backend sends |
| Race condition | Backend has 2s delay, mobile should also wait 2s after subscribe |
| Wrong user receives notification | Check user ID in topic name matches booking.client_id |

---

## 🎯 Why We Use FCM Topics

**Problem**: Mobile app doesn't know when:
- Payment is completed (success/failure)
- Payment timeout expires
- Booking status changes

**Solution**: Backend sends push notifications to FCM topics that mobile app subscribes to.

**Benefits**:
- ✅ Real-time updates (no polling/refreshing)
- ✅ Works for both test and live environments
- ✅ Battery efficient
- ✅ Handles edge cases (timeouts, failures)

---
## 🔧 Backend Implementation Summary

### ✅ Implemented Components

#### 1. FCM Service (`app/Services/FCMService.php`)

The FCM service handles sending push notifications to topics using Firebase Admin SDK.

**Key Methods**:
- `sendPaymentCompleted($booking, $payment)` - Sends payment success notification
- `sendPaymentFailed($booking, $payment, $errorMessage)` - Sends payment failure notification
- `sendPaymentTimeout($booking, $payment)` - Sends payment timeout notification

**Features**:
- ✅ 2-second delay before sending (prevents race condition)
- ✅ User-specific topics: `user_{userId}_booking_{bookingId}_payment`
- ✅ Comprehensive error logging
- ✅ Retry logic with exponential backoff (handled by base `sendToTopic` method)

**Example Topic**: `user_42_booking_123_payment` (only user #42 can subscribe)

#### 2. Payment Webhook Handler (`app/Http/Controllers/Api/PaymentController.php`)

**Updated Methods**:
- `processPaymentStatusV2()` - Processes webhook V2 data and sends FCM on success/failure

**Success Flow** (`PaymentController.php:625-638`):
```php
// After updating payment status to 'completed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentCompleted($payment->booking, $payment);
```

**Failure Flow** (`PaymentController.php:667-680`):
```php
// After updating payment status to 'failed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentFailed($payment->booking, $payment, $failureReason);
```

#### 3. Scheduled Job for Timeout (`app/Console/Commands/CheckExpiredPayments.php`)

**Command**: `php artisan payments:check-expired`

**Schedule**: Runs every minute (configured in `routes/console.php:15`)

**What It Does**:
1. Finds payments older than timeout setting (default 10 minutes)
2. Updates payment status to 'failed' with reason 'Payment timeout expired'
3. Sends FCM notification via `sendPaymentTimeout()`
4. Creates in-app notification for client

**Usage**:
```bash
# Manual test
php artisan payments:check-expired

# Runs automatically via cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

#### 4. Test Endpoints (Admin Only)

Three test endpoints for FCM verification:

| Endpoint | Method | Body | Purpose |
|----------|--------|------|---------|
| `/api/v1/admin/test/fcm/payment-completed` | POST | `{"booking_id": 1}` | Test success notification |
| `/api/v1/admin/test/fcm/payment-failed` | POST | `{"booking_id": 1, "error_message": "Card declined"}` | Test failure notification |
| `/api/v1/admin/test/fcm/payment-timeout` | POST | `{"booking_id": 1}` | Test timeout notification |

**Example Test**:
```bash
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

**Response**:
```json
{
  "success": true,
  "message": "FCM payment completed notification sent",
  "data": {
    "booking_id": 8,
    "payment_id": 12,
    "topic": "user_42_booking_8_payment",
    "fcm_result": true
  }
}
```

---

### 🔐 Security Implementation

**User-Specific Topics** (Implemented in `FCMService.php:266-268`, `305-307`, `341-343`):

Instead of: `booking_{bookingId}_payment` (anyone can subscribe)
We use: `user_{userId}_booking_{bookingId}_payment` (only that user's device)

**Benefits**:
- ✅ Prevents other users from subscribing to someone else's booking updates
- ✅ FCM doesn't validate topic subscribers, so we add user ID for access control
- ✅ Mobile app validates it's the correct user before subscribing

**Mobile App Must**:
1. Only subscribe to topics where `userId` matches logged-in user
2. Validate API response matches notification data
3. Use fallback polling if FCM fails

---

### 📊 Complete Data Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. CLIENT INITIATES PAYMENT                                     │
│    Mobile App → POST /api/v1/bookings/{id}/initiate-payment     │
│    Response: { payment_url: "https://myfatoorah.com/..." }      │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. MOBILE APP SUBSCRIBES TO TOPIC (with 2s delay)               │
│    topic = "user_{userId}_booking_{bookingId}_payment"          │
│    await messaging().subscribeToTopic(topic)                    │
│    await Future.delayed(Duration(seconds: 2))                   │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. USER COMPLETES PAYMENT ON MYFATOORAH                         │
│    MyFatoorah redirects to CallbackUrl OR sends Webhook         │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. BACKEND RECEIVES WEBHOOK/CALLBACK                            │
│    PaymentController::webhook() or ::paymentCallback()          │
│    → processPaymentStatusV2()                                   │
│    → Updates DB: payment.status = 'completed'                   │
│    → FCMService::sendPaymentCompleted() with 2s delay           │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. MOBILE APP RECEIVES FCM NOTIFICATION                         │
│    FirebaseMessaging.onMessage (foreground)                     │
│    FirebaseMessaging.onBackgroundMessage (background)           │
│    → Updates UI, shows success message                          │
│    → Calls API to verify: GET /api/v1/bookings/{id}             │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. CLEANUP                                                       │
│    Mobile app unsubscribes from topic                           │
│    await messaging().unsubscribeFromTopic(topic)                │
└─────────────────────────────────────────────────────────────────┘
```

**Timeout Flow** (if user abandons payment):
```
After 10 minutes → Scheduled job runs → Marks payment as failed
→ FCMService::sendPaymentTimeout() → Mobile receives notification
```

---

### 🧪 Testing Checklist

#### Backend Testing
- [x] FCM service methods created with proper error handling
- [x] Webhook sends FCM on payment success
- [x] Webhook sends FCM on payment failure
- [x] Scheduled job detects expired payments
- [x] Test endpoints available for manual FCM testing
- [x] 2-second delay implemented (race condition prevention)
- [x] User-specific topics implemented (security)
- [x] Comprehensive logging for debugging

#### Mobile Testing (For Mobile Developer)
- [ ] Subscribe to topic before initiating payment
- [ ] Receive notification when payment completes (foreground)
- [ ] Receive notification when payment completes (background)
- [ ] Receive notification when payment completes (app terminated)
- [ ] Receive notification on payment failure
- [ ] Receive notification on payment timeout
- [ ] Verify API response matches FCM data
- [ ] Test fallback polling when FCM unavailable
- [ ] Test unsubscribe after payment completion
- [ ] Test multiple concurrent payments (different bookings)

---

### 🐛 Debugging

#### Check FCM Logs
```bash
# Real-time logs
tail -f storage/logs/laravel.log | grep FCM

# Check specific payment
grep "booking_id.*8" storage/logs/laravel.log | grep FCM
```

#### Test FCM Manually
```bash
# Using admin test endpoint
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

#### Check Scheduled Job
```bash
# Run manually
php artisan payments:check-expired

# Check if scheduled
php artisan schedule:list | grep payment
```

#### Common Issues

| Issue | Solution |
|-------|----------|
| FCM not sending | Check `FCM_CREDENTIALS_PATH` in `.env` |
| Topic subscription fails | Verify topic naming: `user_{userId}_booking_{id}_payment` |
| No subscribers found | Mobile app must subscribe BEFORE backend sends |
| Race condition | Backend has 2s delay, mobile should also wait 2s after subscribe |
| Wrong user receives notification | Check user ID in topic name matches booking.client_id |

---

## 📱 FCM Topics Structure

### Topic Naming Convention

All topics follow this pattern: `{entity}_{id}_{event_type}`

**Important Constraints**:
- Topic names are case-sensitive
- Can only contain: `[a-zA-Z0-9-_.]`
- Maximum 900 bytes
- Cannot start with `/topics/` (FCM adds this automatically)

### Available Topics

| Topic Name | Purpose | When to Subscribe | When to Unsubscribe |
|------------|---------|-------------------|---------------------|
| `booking_{booking_id}_payment` | Payment status for specific booking | Before payment initiation | After payment completion/failure/timeout |

**Security Note**: Anyone who knows a booking ID can subscribe to `booking_{id}_payment`. For sensitive data, use device tokens instead of topics. Topics are suitable here because the mobile app will validate payment status with the backend API regardless of the push notification.

---
## 🔧 Backend Implementation Summary

### ✅ Implemented Components

#### 1. FCM Service (`app/Services/FCMService.php`)

The FCM service handles sending push notifications to topics using Firebase Admin SDK.

**Key Methods**:
- `sendPaymentCompleted($booking, $payment)` - Sends payment success notification
- `sendPaymentFailed($booking, $payment, $errorMessage)` - Sends payment failure notification
- `sendPaymentTimeout($booking, $payment)` - Sends payment timeout notification

**Features**:
- ✅ 2-second delay before sending (prevents race condition)
- ✅ User-specific topics: `user_{userId}_booking_{bookingId}_payment`
- ✅ Comprehensive error logging
- ✅ Retry logic with exponential backoff (handled by base `sendToTopic` method)

**Example Topic**: `user_42_booking_123_payment` (only user #42 can subscribe)

#### 2. Payment Webhook Handler (`app/Http/Controllers/Api/PaymentController.php`)

**Updated Methods**:
- `processPaymentStatusV2()` - Processes webhook V2 data and sends FCM on success/failure

**Success Flow** (`PaymentController.php:625-638`):
```php
// After updating payment status to 'completed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentCompleted($payment->booking, $payment);
```

**Failure Flow** (`PaymentController.php:667-680`):
```php
// After updating payment status to 'failed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentFailed($payment->booking, $payment, $failureReason);
```

#### 3. Scheduled Job for Timeout (`app/Console/Commands/CheckExpiredPayments.php`)

**Command**: `php artisan payments:check-expired`

**Schedule**: Runs every minute (configured in `routes/console.php:15`)

**What It Does**:
1. Finds payments older than timeout setting (default 10 minutes)
2. Updates payment status to 'failed' with reason 'Payment timeout expired'
3. Sends FCM notification via `sendPaymentTimeout()`
4. Creates in-app notification for client

**Usage**:
```bash
# Manual test
php artisan payments:check-expired

# Runs automatically via cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

#### 4. Test Endpoints (Admin Only)

Three test endpoints for FCM verification:

| Endpoint | Method | Body | Purpose |
|----------|--------|------|---------|
| `/api/v1/admin/test/fcm/payment-completed` | POST | `{"booking_id": 1}` | Test success notification |
| `/api/v1/admin/test/fcm/payment-failed` | POST | `{"booking_id": 1, "error_message": "Card declined"}` | Test failure notification |
| `/api/v1/admin/test/fcm/payment-timeout` | POST | `{"booking_id": 1}` | Test timeout notification |

**Example Test**:
```bash
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

**Response**:
```json
{
  "success": true,
  "message": "FCM payment completed notification sent",
  "data": {
    "booking_id": 8,
    "payment_id": 12,
    "topic": "user_42_booking_8_payment",
    "fcm_result": true
  }
}
```

---

### 🔐 Security Implementation

**User-Specific Topics** (Implemented in `FCMService.php:266-268`, `305-307`, `341-343`):

Instead of: `booking_{bookingId}_payment` (anyone can subscribe)
We use: `user_{userId}_booking_{bookingId}_payment` (only that user's device)

**Benefits**:
- ✅ Prevents other users from subscribing to someone else's booking updates
- ✅ FCM doesn't validate topic subscribers, so we add user ID for access control
- ✅ Mobile app validates it's the correct user before subscribing

**Mobile App Must**:
1. Only subscribe to topics where `userId` matches logged-in user
2. Validate API response matches notification data
3. Use fallback polling if FCM fails

---

### 📊 Complete Data Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. CLIENT INITIATES PAYMENT                                     │
│    Mobile App → POST /api/v1/bookings/{id}/initiate-payment     │
│    Response: { payment_url: "https://myfatoorah.com/..." }      │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. MOBILE APP SUBSCRIBES TO TOPIC (with 2s delay)               │
│    topic = "user_{userId}_booking_{bookingId}_payment"          │
│    await messaging().subscribeToTopic(topic)                    │
│    await Future.delayed(Duration(seconds: 2))                   │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. USER COMPLETES PAYMENT ON MYFATOORAH                         │
│    MyFatoorah redirects to CallbackUrl OR sends Webhook         │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. BACKEND RECEIVES WEBHOOK/CALLBACK                            │
│    PaymentController::webhook() or ::paymentCallback()          │
│    → processPaymentStatusV2()                                   │
│    → Updates DB: payment.status = 'completed'                   │
│    → FCMService::sendPaymentCompleted() with 2s delay           │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. MOBILE APP RECEIVES FCM NOTIFICATION                         │
│    FirebaseMessaging.onMessage (foreground)                     │
│    FirebaseMessaging.onBackgroundMessage (background)           │
│    → Updates UI, shows success message                          │
│    → Calls API to verify: GET /api/v1/bookings/{id}             │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. CLEANUP                                                       │
│    Mobile app unsubscribes from topic                           │
│    await messaging().unsubscribeFromTopic(topic)                │
└─────────────────────────────────────────────────────────────────┘
```

**Timeout Flow** (if user abandons payment):
```
After 10 minutes → Scheduled job runs → Marks payment as failed
→ FCMService::sendPaymentTimeout() → Mobile receives notification
```

---

### 🧪 Testing Checklist

#### Backend Testing
- [x] FCM service methods created with proper error handling
- [x] Webhook sends FCM on payment success
- [x] Webhook sends FCM on payment failure
- [x] Scheduled job detects expired payments
- [x] Test endpoints available for manual FCM testing
- [x] 2-second delay implemented (race condition prevention)
- [x] User-specific topics implemented (security)
- [x] Comprehensive logging for debugging

#### Mobile Testing (For Mobile Developer)
- [ ] Subscribe to topic before initiating payment
- [ ] Receive notification when payment completes (foreground)
- [ ] Receive notification when payment completes (background)
- [ ] Receive notification when payment completes (app terminated)
- [ ] Receive notification on payment failure
- [ ] Receive notification on payment timeout
- [ ] Verify API response matches FCM data
- [ ] Test fallback polling when FCM unavailable
- [ ] Test unsubscribe after payment completion
- [ ] Test multiple concurrent payments (different bookings)

---

### 🐛 Debugging

#### Check FCM Logs
```bash
# Real-time logs
tail -f storage/logs/laravel.log | grep FCM

# Check specific payment
grep "booking_id.*8" storage/logs/laravel.log | grep FCM
```

#### Test FCM Manually
```bash
# Using admin test endpoint
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

#### Check Scheduled Job
```bash
# Run manually
php artisan payments:check-expired

# Check if scheduled
php artisan schedule:list | grep payment
```

#### Common Issues

| Issue | Solution |
|-------|----------|
| FCM not sending | Check `FCM_CREDENTIALS_PATH` in `.env` |
| Topic subscription fails | Verify topic naming: `user_{userId}_booking_{id}_payment` |
| No subscribers found | Mobile app must subscribe BEFORE backend sends |
| Race condition | Backend has 2s delay, mobile should also wait 2s after subscribe |
| Wrong user receives notification | Check user ID in topic name matches booking.client_id |

---

## 🔔 Push Notification Events

### 1. Payment Completed ✅

**Topic**: `booking_{booking_id}_payment`

**Payload**:
```json
{
  "notification": {
    "title": "Payment Successful",
    "body": "Your payment has been confirmed"
  },
  "data": {
    "type": "payment_completed",
    "booking_id": "8",
    "payment_id": "61277376",
    "amount": "0.35",
    "currency": "SAR",
    "payment_status": "paid",
    "timestamp": "2025-11-17T22:53:28Z"
  }
}
```

**Mobile App Actions**:
1. Validate with backend API (GET `/api/v1/bookings/{id}`)
2. Update booking status to "paid" locally
3. Close payment webview/screen
4. Show success message with amount
5. Unsubscribe from `booking_{booking_id}_payment` topic
6. Refresh booking details

---
## 🔧 Backend Implementation Summary

### ✅ Implemented Components

#### 1. FCM Service (`app/Services/FCMService.php`)

The FCM service handles sending push notifications to topics using Firebase Admin SDK.

**Key Methods**:
- `sendPaymentCompleted($booking, $payment)` - Sends payment success notification
- `sendPaymentFailed($booking, $payment, $errorMessage)` - Sends payment failure notification
- `sendPaymentTimeout($booking, $payment)` - Sends payment timeout notification

**Features**:
- ✅ 2-second delay before sending (prevents race condition)
- ✅ User-specific topics: `user_{userId}_booking_{bookingId}_payment`
- ✅ Comprehensive error logging
- ✅ Retry logic with exponential backoff (handled by base `sendToTopic` method)

**Example Topic**: `user_42_booking_123_payment` (only user #42 can subscribe)

#### 2. Payment Webhook Handler (`app/Http/Controllers/Api/PaymentController.php`)

**Updated Methods**:
- `processPaymentStatusV2()` - Processes webhook V2 data and sends FCM on success/failure

**Success Flow** (`PaymentController.php:625-638`):
```php
// After updating payment status to 'completed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentCompleted($payment->booking, $payment);
```

**Failure Flow** (`PaymentController.php:667-680`):
```php
// After updating payment status to 'failed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentFailed($payment->booking, $payment, $failureReason);
```

#### 3. Scheduled Job for Timeout (`app/Console/Commands/CheckExpiredPayments.php`)

**Command**: `php artisan payments:check-expired`

**Schedule**: Runs every minute (configured in `routes/console.php:15`)

**What It Does**:
1. Finds payments older than timeout setting (default 10 minutes)
2. Updates payment status to 'failed' with reason 'Payment timeout expired'
3. Sends FCM notification via `sendPaymentTimeout()`
4. Creates in-app notification for client

**Usage**:
```bash
# Manual test
php artisan payments:check-expired

# Runs automatically via cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

#### 4. Test Endpoints (Admin Only)

Three test endpoints for FCM verification:

| Endpoint | Method | Body | Purpose |
|----------|--------|------|---------|
| `/api/v1/admin/test/fcm/payment-completed` | POST | `{"booking_id": 1}` | Test success notification |
| `/api/v1/admin/test/fcm/payment-failed` | POST | `{"booking_id": 1, "error_message": "Card declined"}` | Test failure notification |
| `/api/v1/admin/test/fcm/payment-timeout` | POST | `{"booking_id": 1}` | Test timeout notification |

**Example Test**:
```bash
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

**Response**:
```json
{
  "success": true,
  "message": "FCM payment completed notification sent",
  "data": {
    "booking_id": 8,
    "payment_id": 12,
    "topic": "user_42_booking_8_payment",
    "fcm_result": true
  }
}
```

---

### 🔐 Security Implementation

**User-Specific Topics** (Implemented in `FCMService.php:266-268`, `305-307`, `341-343`):

Instead of: `booking_{bookingId}_payment` (anyone can subscribe)
We use: `user_{userId}_booking_{bookingId}_payment` (only that user's device)

**Benefits**:
- ✅ Prevents other users from subscribing to someone else's booking updates
- ✅ FCM doesn't validate topic subscribers, so we add user ID for access control
- ✅ Mobile app validates it's the correct user before subscribing

**Mobile App Must**:
1. Only subscribe to topics where `userId` matches logged-in user
2. Validate API response matches notification data
3. Use fallback polling if FCM fails

---

### 📊 Complete Data Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. CLIENT INITIATES PAYMENT                                     │
│    Mobile App → POST /api/v1/bookings/{id}/initiate-payment     │
│    Response: { payment_url: "https://myfatoorah.com/..." }      │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. MOBILE APP SUBSCRIBES TO TOPIC (with 2s delay)               │
│    topic = "user_{userId}_booking_{bookingId}_payment"          │
│    await messaging().subscribeToTopic(topic)                    │
│    await Future.delayed(Duration(seconds: 2))                   │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. USER COMPLETES PAYMENT ON MYFATOORAH                         │
│    MyFatoorah redirects to CallbackUrl OR sends Webhook         │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. BACKEND RECEIVES WEBHOOK/CALLBACK                            │
│    PaymentController::webhook() or ::paymentCallback()          │
│    → processPaymentStatusV2()                                   │
│    → Updates DB: payment.status = 'completed'                   │
│    → FCMService::sendPaymentCompleted() with 2s delay           │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. MOBILE APP RECEIVES FCM NOTIFICATION                         │
│    FirebaseMessaging.onMessage (foreground)                     │
│    FirebaseMessaging.onBackgroundMessage (background)           │
│    → Updates UI, shows success message                          │
│    → Calls API to verify: GET /api/v1/bookings/{id}             │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. CLEANUP                                                       │
│    Mobile app unsubscribes from topic                           │
│    await messaging().unsubscribeFromTopic(topic)                │
└─────────────────────────────────────────────────────────────────┘
```

**Timeout Flow** (if user abandons payment):
```
After 10 minutes → Scheduled job runs → Marks payment as failed
→ FCMService::sendPaymentTimeout() → Mobile receives notification
```

---

### 🧪 Testing Checklist

#### Backend Testing
- [x] FCM service methods created with proper error handling
- [x] Webhook sends FCM on payment success
- [x] Webhook sends FCM on payment failure
- [x] Scheduled job detects expired payments
- [x] Test endpoints available for manual FCM testing
- [x] 2-second delay implemented (race condition prevention)
- [x] User-specific topics implemented (security)
- [x] Comprehensive logging for debugging

#### Mobile Testing (For Mobile Developer)
- [ ] Subscribe to topic before initiating payment
- [ ] Receive notification when payment completes (foreground)
- [ ] Receive notification when payment completes (background)
- [ ] Receive notification when payment completes (app terminated)
- [ ] Receive notification on payment failure
- [ ] Receive notification on payment timeout
- [ ] Verify API response matches FCM data
- [ ] Test fallback polling when FCM unavailable
- [ ] Test unsubscribe after payment completion
- [ ] Test multiple concurrent payments (different bookings)

---

### 🐛 Debugging

#### Check FCM Logs
```bash
# Real-time logs
tail -f storage/logs/laravel.log | grep FCM

# Check specific payment
grep "booking_id.*8" storage/logs/laravel.log | grep FCM
```

#### Test FCM Manually
```bash
# Using admin test endpoint
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

#### Check Scheduled Job
```bash
# Run manually
php artisan payments:check-expired

# Check if scheduled
php artisan schedule:list | grep payment
```

#### Common Issues

| Issue | Solution |
|-------|----------|
| FCM not sending | Check `FCM_CREDENTIALS_PATH` in `.env` |
| Topic subscription fails | Verify topic naming: `user_{userId}_booking_{id}_payment` |
| No subscribers found | Mobile app must subscribe BEFORE backend sends |
| Race condition | Backend has 2s delay, mobile should also wait 2s after subscribe |
| Wrong user receives notification | Check user ID in topic name matches booking.client_id |

---

### 2. Payment Failed ❌

**Topic**: `booking_{booking_id}_payment`

**Payload**:
```json
{
  "notification": {
    "title": "Payment Failed",
    "body": "Payment could not be processed"
  },
  "data": {
    "type": "payment_failed",
    "booking_id": "8",
    "payment_id": "61277376",
    "payment_status": "failed",
    "error_code": "card_declined",
    "error_message": "Your card was declined. Please try another payment method.",
    "timestamp": "2025-11-17T22:53:28Z"
  }
}
```

**Mobile App Actions**:
1. Update booking status to "failed" locally
2. Close payment screen
3. Show error message from `error_message` field
4. Display retry button
5. Unsubscribe from payment topic

---
## 🔧 Backend Implementation Summary

### ✅ Implemented Components

#### 1. FCM Service (`app/Services/FCMService.php`)

The FCM service handles sending push notifications to topics using Firebase Admin SDK.

**Key Methods**:
- `sendPaymentCompleted($booking, $payment)` - Sends payment success notification
- `sendPaymentFailed($booking, $payment, $errorMessage)` - Sends payment failure notification
- `sendPaymentTimeout($booking, $payment)` - Sends payment timeout notification

**Features**:
- ✅ 2-second delay before sending (prevents race condition)
- ✅ User-specific topics: `user_{userId}_booking_{bookingId}_payment`
- ✅ Comprehensive error logging
- ✅ Retry logic with exponential backoff (handled by base `sendToTopic` method)

**Example Topic**: `user_42_booking_123_payment` (only user #42 can subscribe)

#### 2. Payment Webhook Handler (`app/Http/Controllers/Api/PaymentController.php`)

**Updated Methods**:
- `processPaymentStatusV2()` - Processes webhook V2 data and sends FCM on success/failure

**Success Flow** (`PaymentController.php:625-638`):
```php
// After updating payment status to 'completed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentCompleted($payment->booking, $payment);
```

**Failure Flow** (`PaymentController.php:667-680`):
```php
// After updating payment status to 'failed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentFailed($payment->booking, $payment, $failureReason);
```

#### 3. Scheduled Job for Timeout (`app/Console/Commands/CheckExpiredPayments.php`)

**Command**: `php artisan payments:check-expired`

**Schedule**: Runs every minute (configured in `routes/console.php:15`)

**What It Does**:
1. Finds payments older than timeout setting (default 10 minutes)
2. Updates payment status to 'failed' with reason 'Payment timeout expired'
3. Sends FCM notification via `sendPaymentTimeout()`
4. Creates in-app notification for client

**Usage**:
```bash
# Manual test
php artisan payments:check-expired

# Runs automatically via cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

#### 4. Test Endpoints (Admin Only)

Three test endpoints for FCM verification:

| Endpoint | Method | Body | Purpose |
|----------|--------|------|---------|
| `/api/v1/admin/test/fcm/payment-completed` | POST | `{"booking_id": 1}` | Test success notification |
| `/api/v1/admin/test/fcm/payment-failed` | POST | `{"booking_id": 1, "error_message": "Card declined"}` | Test failure notification |
| `/api/v1/admin/test/fcm/payment-timeout` | POST | `{"booking_id": 1}` | Test timeout notification |

**Example Test**:
```bash
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

**Response**:
```json
{
  "success": true,
  "message": "FCM payment completed notification sent",
  "data": {
    "booking_id": 8,
    "payment_id": 12,
    "topic": "user_42_booking_8_payment",
    "fcm_result": true
  }
}
```

---

### 🔐 Security Implementation

**User-Specific Topics** (Implemented in `FCMService.php:266-268`, `305-307`, `341-343`):

Instead of: `booking_{bookingId}_payment` (anyone can subscribe)
We use: `user_{userId}_booking_{bookingId}_payment` (only that user's device)

**Benefits**:
- ✅ Prevents other users from subscribing to someone else's booking updates
- ✅ FCM doesn't validate topic subscribers, so we add user ID for access control
- ✅ Mobile app validates it's the correct user before subscribing

**Mobile App Must**:
1. Only subscribe to topics where `userId` matches logged-in user
2. Validate API response matches notification data
3. Use fallback polling if FCM fails

---

### 📊 Complete Data Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. CLIENT INITIATES PAYMENT                                     │
│    Mobile App → POST /api/v1/bookings/{id}/initiate-payment     │
│    Response: { payment_url: "https://myfatoorah.com/..." }      │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. MOBILE APP SUBSCRIBES TO TOPIC (with 2s delay)               │
│    topic = "user_{userId}_booking_{bookingId}_payment"          │
│    await messaging().subscribeToTopic(topic)                    │
│    await Future.delayed(Duration(seconds: 2))                   │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. USER COMPLETES PAYMENT ON MYFATOORAH                         │
│    MyFatoorah redirects to CallbackUrl OR sends Webhook         │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. BACKEND RECEIVES WEBHOOK/CALLBACK                            │
│    PaymentController::webhook() or ::paymentCallback()          │
│    → processPaymentStatusV2()                                   │
│    → Updates DB: payment.status = 'completed'                   │
│    → FCMService::sendPaymentCompleted() with 2s delay           │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. MOBILE APP RECEIVES FCM NOTIFICATION                         │
│    FirebaseMessaging.onMessage (foreground)                     │
│    FirebaseMessaging.onBackgroundMessage (background)           │
│    → Updates UI, shows success message                          │
│    → Calls API to verify: GET /api/v1/bookings/{id}             │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. CLEANUP                                                       │
│    Mobile app unsubscribes from topic                           │
│    await messaging().unsubscribeFromTopic(topic)                │
└─────────────────────────────────────────────────────────────────┘
```

**Timeout Flow** (if user abandons payment):
```
After 10 minutes → Scheduled job runs → Marks payment as failed
→ FCMService::sendPaymentTimeout() → Mobile receives notification
```

---

### 🧪 Testing Checklist

#### Backend Testing
- [x] FCM service methods created with proper error handling
- [x] Webhook sends FCM on payment success
- [x] Webhook sends FCM on payment failure
- [x] Scheduled job detects expired payments
- [x] Test endpoints available for manual FCM testing
- [x] 2-second delay implemented (race condition prevention)
- [x] User-specific topics implemented (security)
- [x] Comprehensive logging for debugging

#### Mobile Testing (For Mobile Developer)
- [ ] Subscribe to topic before initiating payment
- [ ] Receive notification when payment completes (foreground)
- [ ] Receive notification when payment completes (background)
- [ ] Receive notification when payment completes (app terminated)
- [ ] Receive notification on payment failure
- [ ] Receive notification on payment timeout
- [ ] Verify API response matches FCM data
- [ ] Test fallback polling when FCM unavailable
- [ ] Test unsubscribe after payment completion
- [ ] Test multiple concurrent payments (different bookings)

---

### 🐛 Debugging

#### Check FCM Logs
```bash
# Real-time logs
tail -f storage/logs/laravel.log | grep FCM

# Check specific payment
grep "booking_id.*8" storage/logs/laravel.log | grep FCM
```

#### Test FCM Manually
```bash
# Using admin test endpoint
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

#### Check Scheduled Job
```bash
# Run manually
php artisan payments:check-expired

# Check if scheduled
php artisan schedule:list | grep payment
```

#### Common Issues

| Issue | Solution |
|-------|----------|
| FCM not sending | Check `FCM_CREDENTIALS_PATH` in `.env` |
| Topic subscription fails | Verify topic naming: `user_{userId}_booking_{id}_payment` |
| No subscribers found | Mobile app must subscribe BEFORE backend sends |
| Race condition | Backend has 2s delay, mobile should also wait 2s after subscribe |
| Wrong user receives notification | Check user ID in topic name matches booking.client_id |

---

### 3. Payment Timeout ⏱️

**Topic**: `booking_{booking_id}_payment`

**Payload**:
```json
{
  "notification": {
    "title": "Payment Time Expired",
    "body": "Payment session has expired"
  },
  "data": {
    "type": "payment_timeout",
    "booking_id": "8",
    "payment_status": "expired",
    "timeout_minutes": "10",
    "expired_at": "2025-11-17T22:58:28Z",
    "timestamp": "2025-11-17T22:58:28Z"
  }
}
```

**Mobile App Actions**:
1. Close payment webview/screen automatically
2. Show timeout message with `timeout_minutes` value
3. Update booking to show "Payment Required" again
4. Unsubscribe from payment topic

---
## 🔧 Backend Implementation Summary

### ✅ Implemented Components

#### 1. FCM Service (`app/Services/FCMService.php`)

The FCM service handles sending push notifications to topics using Firebase Admin SDK.

**Key Methods**:
- `sendPaymentCompleted($booking, $payment)` - Sends payment success notification
- `sendPaymentFailed($booking, $payment, $errorMessage)` - Sends payment failure notification
- `sendPaymentTimeout($booking, $payment)` - Sends payment timeout notification

**Features**:
- ✅ 2-second delay before sending (prevents race condition)
- ✅ User-specific topics: `user_{userId}_booking_{bookingId}_payment`
- ✅ Comprehensive error logging
- ✅ Retry logic with exponential backoff (handled by base `sendToTopic` method)

**Example Topic**: `user_42_booking_123_payment` (only user #42 can subscribe)

#### 2. Payment Webhook Handler (`app/Http/Controllers/Api/PaymentController.php`)

**Updated Methods**:
- `processPaymentStatusV2()` - Processes webhook V2 data and sends FCM on success/failure

**Success Flow** (`PaymentController.php:625-638`):
```php
// After updating payment status to 'completed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentCompleted($payment->booking, $payment);
```

**Failure Flow** (`PaymentController.php:667-680`):
```php
// After updating payment status to 'failed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentFailed($payment->booking, $payment, $failureReason);
```

#### 3. Scheduled Job for Timeout (`app/Console/Commands/CheckExpiredPayments.php`)

**Command**: `php artisan payments:check-expired`

**Schedule**: Runs every minute (configured in `routes/console.php:15`)

**What It Does**:
1. Finds payments older than timeout setting (default 10 minutes)
2. Updates payment status to 'failed' with reason 'Payment timeout expired'
3. Sends FCM notification via `sendPaymentTimeout()`
4. Creates in-app notification for client

**Usage**:
```bash
# Manual test
php artisan payments:check-expired

# Runs automatically via cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

#### 4. Test Endpoints (Admin Only)

Three test endpoints for FCM verification:

| Endpoint | Method | Body | Purpose |
|----------|--------|------|---------|
| `/api/v1/admin/test/fcm/payment-completed` | POST | `{"booking_id": 1}` | Test success notification |
| `/api/v1/admin/test/fcm/payment-failed` | POST | `{"booking_id": 1, "error_message": "Card declined"}` | Test failure notification |
| `/api/v1/admin/test/fcm/payment-timeout` | POST | `{"booking_id": 1}` | Test timeout notification |

**Example Test**:
```bash
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

**Response**:
```json
{
  "success": true,
  "message": "FCM payment completed notification sent",
  "data": {
    "booking_id": 8,
    "payment_id": 12,
    "topic": "user_42_booking_8_payment",
    "fcm_result": true
  }
}
```

---

### 🔐 Security Implementation

**User-Specific Topics** (Implemented in `FCMService.php:266-268`, `305-307`, `341-343`):

Instead of: `booking_{bookingId}_payment` (anyone can subscribe)
We use: `user_{userId}_booking_{bookingId}_payment` (only that user's device)

**Benefits**:
- ✅ Prevents other users from subscribing to someone else's booking updates
- ✅ FCM doesn't validate topic subscribers, so we add user ID for access control
- ✅ Mobile app validates it's the correct user before subscribing

**Mobile App Must**:
1. Only subscribe to topics where `userId` matches logged-in user
2. Validate API response matches notification data
3. Use fallback polling if FCM fails

---

### 📊 Complete Data Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. CLIENT INITIATES PAYMENT                                     │
│    Mobile App → POST /api/v1/bookings/{id}/initiate-payment     │
│    Response: { payment_url: "https://myfatoorah.com/..." }      │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. MOBILE APP SUBSCRIBES TO TOPIC (with 2s delay)               │
│    topic = "user_{userId}_booking_{bookingId}_payment"          │
│    await messaging().subscribeToTopic(topic)                    │
│    await Future.delayed(Duration(seconds: 2))                   │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. USER COMPLETES PAYMENT ON MYFATOORAH                         │
│    MyFatoorah redirects to CallbackUrl OR sends Webhook         │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. BACKEND RECEIVES WEBHOOK/CALLBACK                            │
│    PaymentController::webhook() or ::paymentCallback()          │
│    → processPaymentStatusV2()                                   │
│    → Updates DB: payment.status = 'completed'                   │
│    → FCMService::sendPaymentCompleted() with 2s delay           │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. MOBILE APP RECEIVES FCM NOTIFICATION                         │
│    FirebaseMessaging.onMessage (foreground)                     │
│    FirebaseMessaging.onBackgroundMessage (background)           │
│    → Updates UI, shows success message                          │
│    → Calls API to verify: GET /api/v1/bookings/{id}             │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. CLEANUP                                                       │
│    Mobile app unsubscribes from topic                           │
│    await messaging().unsubscribeFromTopic(topic)                │
└─────────────────────────────────────────────────────────────────┘
```

**Timeout Flow** (if user abandons payment):
```
After 10 minutes → Scheduled job runs → Marks payment as failed
→ FCMService::sendPaymentTimeout() → Mobile receives notification
```

---

### 🧪 Testing Checklist

#### Backend Testing
- [x] FCM service methods created with proper error handling
- [x] Webhook sends FCM on payment success
- [x] Webhook sends FCM on payment failure
- [x] Scheduled job detects expired payments
- [x] Test endpoints available for manual FCM testing
- [x] 2-second delay implemented (race condition prevention)
- [x] User-specific topics implemented (security)
- [x] Comprehensive logging for debugging

#### Mobile Testing (For Mobile Developer)
- [ ] Subscribe to topic before initiating payment
- [ ] Receive notification when payment completes (foreground)
- [ ] Receive notification when payment completes (background)
- [ ] Receive notification when payment completes (app terminated)
- [ ] Receive notification on payment failure
- [ ] Receive notification on payment timeout
- [ ] Verify API response matches FCM data
- [ ] Test fallback polling when FCM unavailable
- [ ] Test unsubscribe after payment completion
- [ ] Test multiple concurrent payments (different bookings)

---

### 🐛 Debugging

#### Check FCM Logs
```bash
# Real-time logs
tail -f storage/logs/laravel.log | grep FCM

# Check specific payment
grep "booking_id.*8" storage/logs/laravel.log | grep FCM
```

#### Test FCM Manually
```bash
# Using admin test endpoint
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

#### Check Scheduled Job
```bash
# Run manually
php artisan payments:check-expired

# Check if scheduled
php artisan schedule:list | grep payment
```

#### Common Issues

| Issue | Solution |
|-------|----------|
| FCM not sending | Check `FCM_CREDENTIALS_PATH` in `.env` |
| Topic subscription fails | Verify topic naming: `user_{userId}_booking_{id}_payment` |
| No subscribers found | Mobile app must subscribe BEFORE backend sends |
| Race condition | Backend has 2s delay, mobile should also wait 2s after subscribe |
| Wrong user receives notification | Check user ID in topic name matches booking.client_id |

---

## 🔧 Mobile App Implementation Guide

### Step 0: Request Permissions (iOS Required)

```dart
import 'package:firebase_messaging/firebase_messaging.dart';

Future<void> requestNotificationPermissions() async {
  NotificationSettings settings = await FirebaseMessaging.instance.requestPermission(
    alert: true,
    badge: true,
    sound: true,
    provisional: false,
  );

  if (settings.authorizationStatus == AuthorizationStatus.authorized) {
    print('✅ User granted notification permission');
  } else if (settings.authorizationStatus == AuthorizationStatus.provisional) {
    print('⚠️ User granted provisional permission');
  } else {
    print('❌ User declined notification permission');
    // Handle gracefully - show in-app messages instead
  }
}
```

### Step 1: Initialize FCM

```dart
class FCMService {
  final FirebaseMessaging _fcm = FirebaseMessaging.instance;
  
  Future<void> initialize() async {
    // Request permissions
    await requestNotificationPermissions();
    
    // Get FCM token (for debugging/logging)
    String? token = await _fcm.getToken();
    print('FCM Token: $token');
    
    // Handle foreground messages
    FirebaseMessaging.onMessage.listen(_handleForegroundMessage);
    
    // Handle background messages (user taps notification)
    FirebaseMessaging.onMessageOpenedApp.listen(_handleBackgroundMessage);
    
    // Handle terminated state (app opened via notification)
    _handleTerminatedState();
  }
  
  Future<void> _handleTerminatedState() async {
    RemoteMessage? initialMessage = await _fcm.getInitialMessage();
    if (initialMessage != null) {
      print('App opened from terminated state via notification');
      _handleBackgroundMessage(initialMessage);
    }
  }
}
```

### Step 2: Subscribe to Topics (with Error Handling)

```dart
class PaymentTopicManager {
  final FirebaseMessaging _fcm = FirebaseMessaging.instance;
  
  Future<bool> subscribeToBookingPayment(int bookingId) async {
    try {
      final topic = 'booking_${bookingId}_payment';
      
      // Validate topic name
      if (!_isValidTopicName(topic)) {
        print('❌ Invalid topic name: $topic');
        return false;
      }
      
      await _fcm.subscribeToTopic(topic);
      print('✅ Subscribed to $topic');
      
      // Store subscription locally for cleanup
      await _saveSubscription(topic);
      
      return true;
    } catch (e) {
      print('❌ Subscription failed: $e');
      // Log to analytics/error tracking
      return false;
    }
  }
  
  Future<bool> unsubscribeFromBookingPayment(int bookingId) async {
    try {
      final topic = 'booking_${bookingId}_payment';
      await _fcm.unsubscribeFromTopic(topic);
      print('✅ Unsubscribed from $topic');
      
      // Remove from local storage
      await _removeSubscription(topic);
      
      return true;
    } catch (e) {
      print('❌ Unsubscription failed: $e');
      return false;
    }
  }
  
  bool _isValidTopicName(String topic) {
    // FCM topic validation
    if (topic.length > 900) return false;
    if (topic.startsWith('/topics/')) return false;
    
    final validPattern = RegExp(r'^[a-zA-Z0-9-_.]+$');
    return validPattern.hasMatch(topic);
  }
  
  Future<void> _saveSubscription(String topic) async {
    // Save to SharedPreferences or local DB
    final prefs = await SharedPreferences.getInstance();
    List<String> topics = prefs.getStringList('fcm_topics') ?? [];
    if (!topics.contains(topic)) {
      topics.add(topic);
      await prefs.setStringList('fcm_topics', topics);
    }
  }
  
  Future<void> _removeSubscription(String topic) async {
    final prefs = await SharedPreferences.getInstance();
    List<String> topics = prefs.getStringList('fcm_topics') ?? [];
    topics.remove(topic);
    await prefs.setStringList('fcm_topics', topics);
  }
  
  // Cleanup all subscriptions on logout
  Future<void> cleanupAllSubscriptions() async {
    final prefs = await SharedPreferences.getInstance();
    List<String> topics = prefs.getStringList('fcm_topics') ?? [];
    
    for (String topic in topics) {
      try {
        await _fcm.unsubscribeFromTopic(topic);
        print('Unsubscribed from $topic');
      } catch (e) {
        print('Failed to unsubscribe from $topic: $e');
      }
    }
    
    await prefs.remove('fcm_topics');
  }
}
```

### Step 3: Handle Messages (All States)

```dart
class FCMMessageHandler {
  final NavigationService _navigationService = Get.find(); // or your nav solution
  
  void _handleForegroundMessage(RemoteMessage message) {
    print('📨 Foreground message: ${message.data}');
    _processMessage(message.data, isBackground: false);
  }
  
  void _handleBackgroundMessage(RemoteMessage message) {
    print('📨 Background message: ${message.data}');
    _processMessage(message.data, isBackground: true);
  }
  
  void _processMessage(Map<String, dynamic> data, {required bool isBackground}) {
    String type = data['type'] ?? '';
    
    switch (type) {
      case 'payment_completed':
        _handlePaymentCompleted(data, isBackground: isBackground);
        break;
      case 'payment_failed':
        _handlePaymentFailed(data, isBackground: isBackground);
        break;
      case 'payment_timeout':
        _handlePaymentTimeout(data, isBackground: isBackground);
        break;
      default:
        print('Unknown notification type: $type');
    }
  }
}
```

### Step 4: Handle Payment Events (with Validation)

```dart
Future<void> _handlePaymentCompleted(Map<String, dynamic> data, {required bool isBackground}) async {
  try {
    int bookingId = int.parse(data['booking_id']);
    String paymentId = data['payment_id'] ?? '';
    String amount = data['amount'] ?? '0';
    
    // IMPORTANT: Validate with backend
    final booking = await _apiService.getBooking(bookingId);
    if (booking.paymentStatus != 'paid') {
      print('⚠️ Backend shows payment not completed, ignoring FCM');
      return;
    }
    
    // Update local state
    _bookingController.updateBookingStatus(bookingId, 'paid');
    
    // Unsubscribe from topic
    await PaymentTopicManager().unsubscribeFromBookingPayment(bookingId);
    
    if (isBackground) {
      // User tapped notification - navigate to booking details
      _navigationService.navigateTo('/booking/$bookingId');
    } else {
      // App is open - close payment screen and show success
      _navigationService.popUntilFirst();
      _showSuccessDialog(
        'Payment Successful!',
        'Amount: $amount SAR\nPayment ID: $paymentId'
      );
    }
    
    // Refresh booking list
    _bookingController.refreshBookings();
    
  } catch (e) {
    print('❌ Error handling payment completed: $e');
    // Log to error tracking
  }
}

Future<void> _handlePaymentTimeout(Map<String, dynamic> data, {required bool isBackground}) async {
  try {
    int bookingId = int.parse(data['booking_id']);
    String timeoutMinutes = data['timeout_minutes'] ?? '10';
    
    // Unsubscribe from topic
    await PaymentTopicManager().unsubscribeFromBookingPayment(bookingId);
    
    if (!isBackground) {
      // Close payment screen if open
      _navigationService.popUntilFirst();
    }
    
    // Show timeout message
    _showErrorDialog(
      'Payment Expired',
      'Your payment session expired after $timeoutMinutes minutes. Please try again.'
    );
    
  } catch (e) {
    print('❌ Error handling payment timeout: $e');
  }
}

Future<void> _handlePaymentFailed(Map<String, dynamic> data, {required bool isBackground}) async {
  try {
    int bookingId = int.parse(data['booking_id']);
    String errorMessage = data['error_message'] ?? 'Payment failed. Please try again.';
    
    // Unsubscribe from topic
    await PaymentTopicManager().unsubscribeFromBookingPayment(bookingId);
    
    if (!isBackground) {
      _navigationService.popUntilFirst();
    }
    
    // Show error with retry option
    _showPaymentFailedDialog(
      'Payment Failed',
      errorMessage,
      onRetry: () => _retryPayment(bookingId)
    );
    
  } catch (e) {
    print('❌ Error handling payment failed: $e');
  }
}
```

### Step 5: Race Condition Handling

```dart
class PaymentFlowManager {
  Future<void> initiatePayment(int bookingId) async {
    try {
      // 1. Subscribe to FCM topic FIRST
      bool subscribed = await PaymentTopicManager().subscribeToBookingPayment(bookingId);
      
      if (!subscribed) {
        _showError('Failed to initialize payment notifications');
        return;
      }
      
      // 2. Wait for subscription to propagate (FCM needs ~1-2 seconds)
      await Future.delayed(Duration(seconds: 2));
      
      // 3. Start local timeout timer as backup
      _startPaymentTimeoutTimer(bookingId);
      
      // 4. Now initiate payment on backend
      final response = await _apiService.initiatePayment(bookingId);
      
      // 5. Open payment webview
      _openPaymentWebView(response.paymentUrl);
      
    } catch (e) {
      print('❌ Payment initiation failed: $e');
      await PaymentTopicManager().unsubscribeFromBookingPayment(bookingId);
    }
  }
  
  Timer? _paymentTimer;
  
  void _startPaymentTimeoutTimer(int bookingId) {
    // Backup timer in case FCM doesn't arrive
    _paymentTimer = Timer(Duration(minutes: 10), () {
      print('⏱️ Local payment timeout reached');
      _handlePaymentTimeout({'booking_id': bookingId.toString()}, isBackground: false);
    });
  }
  
  void _cancelPaymentTimer() {
    _paymentTimer?.cancel();
    _paymentTimer = null;
  }
  
  // Call this when payment completes or user cancels
  Future<void> cleanupPaymentFlow(int bookingId) async {
    _cancelPaymentTimer();
    await PaymentTopicManager().unsubscribeFromBookingPayment(bookingId);
  }
}
```

---
## 🔧 Backend Implementation Summary

### ✅ Implemented Components

#### 1. FCM Service (`app/Services/FCMService.php`)

The FCM service handles sending push notifications to topics using Firebase Admin SDK.

**Key Methods**:
- `sendPaymentCompleted($booking, $payment)` - Sends payment success notification
- `sendPaymentFailed($booking, $payment, $errorMessage)` - Sends payment failure notification
- `sendPaymentTimeout($booking, $payment)` - Sends payment timeout notification

**Features**:
- ✅ 2-second delay before sending (prevents race condition)
- ✅ User-specific topics: `user_{userId}_booking_{bookingId}_payment`
- ✅ Comprehensive error logging
- ✅ Retry logic with exponential backoff (handled by base `sendToTopic` method)

**Example Topic**: `user_42_booking_123_payment` (only user #42 can subscribe)

#### 2. Payment Webhook Handler (`app/Http/Controllers/Api/PaymentController.php`)

**Updated Methods**:
- `processPaymentStatusV2()` - Processes webhook V2 data and sends FCM on success/failure

**Success Flow** (`PaymentController.php:625-638`):
```php
// After updating payment status to 'completed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentCompleted($payment->booking, $payment);
```

**Failure Flow** (`PaymentController.php:667-680`):
```php
// After updating payment status to 'failed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentFailed($payment->booking, $payment, $failureReason);
```

#### 3. Scheduled Job for Timeout (`app/Console/Commands/CheckExpiredPayments.php`)

**Command**: `php artisan payments:check-expired`

**Schedule**: Runs every minute (configured in `routes/console.php:15`)

**What It Does**:
1. Finds payments older than timeout setting (default 10 minutes)
2. Updates payment status to 'failed' with reason 'Payment timeout expired'
3. Sends FCM notification via `sendPaymentTimeout()`
4. Creates in-app notification for client

**Usage**:
```bash
# Manual test
php artisan payments:check-expired

# Runs automatically via cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

#### 4. Test Endpoints (Admin Only)

Three test endpoints for FCM verification:

| Endpoint | Method | Body | Purpose |
|----------|--------|------|---------|
| `/api/v1/admin/test/fcm/payment-completed` | POST | `{"booking_id": 1}` | Test success notification |
| `/api/v1/admin/test/fcm/payment-failed` | POST | `{"booking_id": 1, "error_message": "Card declined"}` | Test failure notification |
| `/api/v1/admin/test/fcm/payment-timeout` | POST | `{"booking_id": 1}` | Test timeout notification |

**Example Test**:
```bash
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

**Response**:
```json
{
  "success": true,
  "message": "FCM payment completed notification sent",
  "data": {
    "booking_id": 8,
    "payment_id": 12,
    "topic": "user_42_booking_8_payment",
    "fcm_result": true
  }
}
```

---

### 🔐 Security Implementation

**User-Specific Topics** (Implemented in `FCMService.php:266-268`, `305-307`, `341-343`):

Instead of: `booking_{bookingId}_payment` (anyone can subscribe)
We use: `user_{userId}_booking_{bookingId}_payment` (only that user's device)

**Benefits**:
- ✅ Prevents other users from subscribing to someone else's booking updates
- ✅ FCM doesn't validate topic subscribers, so we add user ID for access control
- ✅ Mobile app validates it's the correct user before subscribing

**Mobile App Must**:
1. Only subscribe to topics where `userId` matches logged-in user
2. Validate API response matches notification data
3. Use fallback polling if FCM fails

---

### 📊 Complete Data Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. CLIENT INITIATES PAYMENT                                     │
│    Mobile App → POST /api/v1/bookings/{id}/initiate-payment     │
│    Response: { payment_url: "https://myfatoorah.com/..." }      │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. MOBILE APP SUBSCRIBES TO TOPIC (with 2s delay)               │
│    topic = "user_{userId}_booking_{bookingId}_payment"          │
│    await messaging().subscribeToTopic(topic)                    │
│    await Future.delayed(Duration(seconds: 2))                   │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. USER COMPLETES PAYMENT ON MYFATOORAH                         │
│    MyFatoorah redirects to CallbackUrl OR sends Webhook         │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. BACKEND RECEIVES WEBHOOK/CALLBACK                            │
│    PaymentController::webhook() or ::paymentCallback()          │
│    → processPaymentStatusV2()                                   │
│    → Updates DB: payment.status = 'completed'                   │
│    → FCMService::sendPaymentCompleted() with 2s delay           │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. MOBILE APP RECEIVES FCM NOTIFICATION                         │
│    FirebaseMessaging.onMessage (foreground)                     │
│    FirebaseMessaging.onBackgroundMessage (background)           │
│    → Updates UI, shows success message                          │
│    → Calls API to verify: GET /api/v1/bookings/{id}             │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. CLEANUP                                                       │
│    Mobile app unsubscribes from topic                           │
│    await messaging().unsubscribeFromTopic(topic)                │
└─────────────────────────────────────────────────────────────────┘
```

**Timeout Flow** (if user abandons payment):
```
After 10 minutes → Scheduled job runs → Marks payment as failed
→ FCMService::sendPaymentTimeout() → Mobile receives notification
```

---

### 🧪 Testing Checklist

#### Backend Testing
- [x] FCM service methods created with proper error handling
- [x] Webhook sends FCM on payment success
- [x] Webhook sends FCM on payment failure
- [x] Scheduled job detects expired payments
- [x] Test endpoints available for manual FCM testing
- [x] 2-second delay implemented (race condition prevention)
- [x] User-specific topics implemented (security)
- [x] Comprehensive logging for debugging

#### Mobile Testing (For Mobile Developer)
- [ ] Subscribe to topic before initiating payment
- [ ] Receive notification when payment completes (foreground)
- [ ] Receive notification when payment completes (background)
- [ ] Receive notification when payment completes (app terminated)
- [ ] Receive notification on payment failure
- [ ] Receive notification on payment timeout
- [ ] Verify API response matches FCM data
- [ ] Test fallback polling when FCM unavailable
- [ ] Test unsubscribe after payment completion
- [ ] Test multiple concurrent payments (different bookings)

---

### 🐛 Debugging

#### Check FCM Logs
```bash
# Real-time logs
tail -f storage/logs/laravel.log | grep FCM

# Check specific payment
grep "booking_id.*8" storage/logs/laravel.log | grep FCM
```

#### Test FCM Manually
```bash
# Using admin test endpoint
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

#### Check Scheduled Job
```bash
# Run manually
php artisan payments:check-expired

# Check if scheduled
php artisan schedule:list | grep payment
```

#### Common Issues

| Issue | Solution |
|-------|----------|
| FCM not sending | Check `FCM_CREDENTIALS_PATH` in `.env` |
| Topic subscription fails | Verify topic naming: `user_{userId}_booking_{id}_payment` |
| No subscribers found | Mobile app must subscribe BEFORE backend sends |
| Race condition | Backend has 2s delay, mobile should also wait 2s after subscribe |
| Wrong user receives notification | Check user ID in topic name matches booking.client_id |

---

## 🧪 Testing Guide

### Test Payment Completed

1. **From FCM Console**:
   - Go to Firebase Console → Cloud Messaging
   - Select "Send test message to topic"
   - Topic: `booking_8_payment`
   - Payload:
   ```json
   {
     "type": "payment_completed",
     "booking_id": "8",
     "payment_id": "test123",
     "amount": "10.00",
     "currency": "SAR",
     "payment_status": "paid"
   }
   ```

2. **From Backend**:
   ```bash
   curl -X POST https://techspireksa.com/api/v1/test/fcm \
     -H "Content-Type: application/json" \
     -d '{"booking_id": 8, "event": "payment_completed"}'
   ```

### Test Payment Timeout

```dart
// In your app, subscribe and wait
await PaymentTopicManager().subscribeToBookingPayment(999);
// Then manually trigger timeout from backend
```

### Testing Checklist

- [ ] Foreground notification received
- [ ] Background notification received
- [ ] Terminated state notification works
- [ ] UI updates correctly
- [ ] Subscription/unsubscription works
- [ ] Timeout timer cancels on success
- [ ] Cleanup on logout works
- [ ] Race condition handled (subscription before payment)

---
## 🔧 Backend Implementation Summary

### ✅ Implemented Components

#### 1. FCM Service (`app/Services/FCMService.php`)

The FCM service handles sending push notifications to topics using Firebase Admin SDK.

**Key Methods**:
- `sendPaymentCompleted($booking, $payment)` - Sends payment success notification
- `sendPaymentFailed($booking, $payment, $errorMessage)` - Sends payment failure notification
- `sendPaymentTimeout($booking, $payment)` - Sends payment timeout notification

**Features**:
- ✅ 2-second delay before sending (prevents race condition)
- ✅ User-specific topics: `user_{userId}_booking_{bookingId}_payment`
- ✅ Comprehensive error logging
- ✅ Retry logic with exponential backoff (handled by base `sendToTopic` method)

**Example Topic**: `user_42_booking_123_payment` (only user #42 can subscribe)

#### 2. Payment Webhook Handler (`app/Http/Controllers/Api/PaymentController.php`)

**Updated Methods**:
- `processPaymentStatusV2()` - Processes webhook V2 data and sends FCM on success/failure

**Success Flow** (`PaymentController.php:625-638`):
```php
// After updating payment status to 'completed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentCompleted($payment->booking, $payment);
```

**Failure Flow** (`PaymentController.php:667-680`):
```php
// After updating payment status to 'failed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentFailed($payment->booking, $payment, $failureReason);
```

#### 3. Scheduled Job for Timeout (`app/Console/Commands/CheckExpiredPayments.php`)

**Command**: `php artisan payments:check-expired`

**Schedule**: Runs every minute (configured in `routes/console.php:15`)

**What It Does**:
1. Finds payments older than timeout setting (default 10 minutes)
2. Updates payment status to 'failed' with reason 'Payment timeout expired'
3. Sends FCM notification via `sendPaymentTimeout()`
4. Creates in-app notification for client

**Usage**:
```bash
# Manual test
php artisan payments:check-expired

# Runs automatically via cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

#### 4. Test Endpoints (Admin Only)

Three test endpoints for FCM verification:

| Endpoint | Method | Body | Purpose |
|----------|--------|------|---------|
| `/api/v1/admin/test/fcm/payment-completed` | POST | `{"booking_id": 1}` | Test success notification |
| `/api/v1/admin/test/fcm/payment-failed` | POST | `{"booking_id": 1, "error_message": "Card declined"}` | Test failure notification |
| `/api/v1/admin/test/fcm/payment-timeout` | POST | `{"booking_id": 1}` | Test timeout notification |

**Example Test**:
```bash
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

**Response**:
```json
{
  "success": true,
  "message": "FCM payment completed notification sent",
  "data": {
    "booking_id": 8,
    "payment_id": 12,
    "topic": "user_42_booking_8_payment",
    "fcm_result": true
  }
}
```

---

### 🔐 Security Implementation

**User-Specific Topics** (Implemented in `FCMService.php:266-268`, `305-307`, `341-343`):

Instead of: `booking_{bookingId}_payment` (anyone can subscribe)
We use: `user_{userId}_booking_{bookingId}_payment` (only that user's device)

**Benefits**:
- ✅ Prevents other users from subscribing to someone else's booking updates
- ✅ FCM doesn't validate topic subscribers, so we add user ID for access control
- ✅ Mobile app validates it's the correct user before subscribing

**Mobile App Must**:
1. Only subscribe to topics where `userId` matches logged-in user
2. Validate API response matches notification data
3. Use fallback polling if FCM fails

---

### 📊 Complete Data Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. CLIENT INITIATES PAYMENT                                     │
│    Mobile App → POST /api/v1/bookings/{id}/initiate-payment     │
│    Response: { payment_url: "https://myfatoorah.com/..." }      │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. MOBILE APP SUBSCRIBES TO TOPIC (with 2s delay)               │
│    topic = "user_{userId}_booking_{bookingId}_payment"          │
│    await messaging().subscribeToTopic(topic)                    │
│    await Future.delayed(Duration(seconds: 2))                   │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. USER COMPLETES PAYMENT ON MYFATOORAH                         │
│    MyFatoorah redirects to CallbackUrl OR sends Webhook         │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. BACKEND RECEIVES WEBHOOK/CALLBACK                            │
│    PaymentController::webhook() or ::paymentCallback()          │
│    → processPaymentStatusV2()                                   │
│    → Updates DB: payment.status = 'completed'                   │
│    → FCMService::sendPaymentCompleted() with 2s delay           │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. MOBILE APP RECEIVES FCM NOTIFICATION                         │
│    FirebaseMessaging.onMessage (foreground)                     │
│    FirebaseMessaging.onBackgroundMessage (background)           │
│    → Updates UI, shows success message                          │
│    → Calls API to verify: GET /api/v1/bookings/{id}             │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. CLEANUP                                                       │
│    Mobile app unsubscribes from topic                           │
│    await messaging().unsubscribeFromTopic(topic)                │
└─────────────────────────────────────────────────────────────────┘
```

**Timeout Flow** (if user abandons payment):
```
After 10 minutes → Scheduled job runs → Marks payment as failed
→ FCMService::sendPaymentTimeout() → Mobile receives notification
```

---

### 🧪 Testing Checklist

#### Backend Testing
- [x] FCM service methods created with proper error handling
- [x] Webhook sends FCM on payment success
- [x] Webhook sends FCM on payment failure
- [x] Scheduled job detects expired payments
- [x] Test endpoints available for manual FCM testing
- [x] 2-second delay implemented (race condition prevention)
- [x] User-specific topics implemented (security)
- [x] Comprehensive logging for debugging

#### Mobile Testing (For Mobile Developer)
- [ ] Subscribe to topic before initiating payment
- [ ] Receive notification when payment completes (foreground)
- [ ] Receive notification when payment completes (background)
- [ ] Receive notification when payment completes (app terminated)
- [ ] Receive notification on payment failure
- [ ] Receive notification on payment timeout
- [ ] Verify API response matches FCM data
- [ ] Test fallback polling when FCM unavailable
- [ ] Test unsubscribe after payment completion
- [ ] Test multiple concurrent payments (different bookings)

---

### 🐛 Debugging

#### Check FCM Logs
```bash
# Real-time logs
tail -f storage/logs/laravel.log | grep FCM

# Check specific payment
grep "booking_id.*8" storage/logs/laravel.log | grep FCM
```

#### Test FCM Manually
```bash
# Using admin test endpoint
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

#### Check Scheduled Job
```bash
# Run manually
php artisan payments:check-expired

# Check if scheduled
php artisan schedule:list | grep payment
```

#### Common Issues

| Issue | Solution |
|-------|----------|
| FCM not sending | Check `FCM_CREDENTIALS_PATH` in `.env` |
| Topic subscription fails | Verify topic naming: `user_{userId}_booking_{id}_payment` |
| No subscribers found | Mobile app must subscribe BEFORE backend sends |
| Race condition | Backend has 2s delay, mobile should also wait 2s after subscribe |
| Wrong user receives notification | Check user ID in topic name matches booking.client_id |

---

## 🔧 Platform-Specific Setup

### Android (android/app/src/main/AndroidManifest.xml)

```xml
<!-- Inside <application> tag -->
<meta-data
    android:name="com.google.firebase.messaging.default_notification_channel_id"
    android:value="high_importance_channel" />
```

### iOS (AppDelegate.swift)

```swift
import Firebase
import UserNotifications

@UIApplicationMain
class AppDelegate: FlutterAppDelegate {
  override func application(
    _ application: UIApplication,
    didFinishLaunchingWithOptions launchOptions: [UIApplication.LaunchOptionsKey: Any]?
  ) -> Bool {
    FirebaseApp.configure()
    
    if #available(iOS 10.0, *) {
      UNUserNotificationCenter.current().delegate = self
    }
    
    return super.application(application, didFinishLaunchingWithOptions: launchOptions)
  }
}
```

---
## 🔧 Backend Implementation Summary

### ✅ Implemented Components

#### 1. FCM Service (`app/Services/FCMService.php`)

The FCM service handles sending push notifications to topics using Firebase Admin SDK.

**Key Methods**:
- `sendPaymentCompleted($booking, $payment)` - Sends payment success notification
- `sendPaymentFailed($booking, $payment, $errorMessage)` - Sends payment failure notification
- `sendPaymentTimeout($booking, $payment)` - Sends payment timeout notification

**Features**:
- ✅ 2-second delay before sending (prevents race condition)
- ✅ User-specific topics: `user_{userId}_booking_{bookingId}_payment`
- ✅ Comprehensive error logging
- ✅ Retry logic with exponential backoff (handled by base `sendToTopic` method)

**Example Topic**: `user_42_booking_123_payment` (only user #42 can subscribe)

#### 2. Payment Webhook Handler (`app/Http/Controllers/Api/PaymentController.php`)

**Updated Methods**:
- `processPaymentStatusV2()` - Processes webhook V2 data and sends FCM on success/failure

**Success Flow** (`PaymentController.php:625-638`):
```php
// After updating payment status to 'completed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentCompleted($payment->booking, $payment);
```

**Failure Flow** (`PaymentController.php:667-680`):
```php
// After updating payment status to 'failed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentFailed($payment->booking, $payment, $failureReason);
```

#### 3. Scheduled Job for Timeout (`app/Console/Commands/CheckExpiredPayments.php`)

**Command**: `php artisan payments:check-expired`

**Schedule**: Runs every minute (configured in `routes/console.php:15`)

**What It Does**:
1. Finds payments older than timeout setting (default 10 minutes)
2. Updates payment status to 'failed' with reason 'Payment timeout expired'
3. Sends FCM notification via `sendPaymentTimeout()`
4. Creates in-app notification for client

**Usage**:
```bash
# Manual test
php artisan payments:check-expired

# Runs automatically via cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

#### 4. Test Endpoints (Admin Only)

Three test endpoints for FCM verification:

| Endpoint | Method | Body | Purpose |
|----------|--------|------|---------|
| `/api/v1/admin/test/fcm/payment-completed` | POST | `{"booking_id": 1}` | Test success notification |
| `/api/v1/admin/test/fcm/payment-failed` | POST | `{"booking_id": 1, "error_message": "Card declined"}` | Test failure notification |
| `/api/v1/admin/test/fcm/payment-timeout` | POST | `{"booking_id": 1}` | Test timeout notification |

**Example Test**:
```bash
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

**Response**:
```json
{
  "success": true,
  "message": "FCM payment completed notification sent",
  "data": {
    "booking_id": 8,
    "payment_id": 12,
    "topic": "user_42_booking_8_payment",
    "fcm_result": true
  }
}
```

---

### 🔐 Security Implementation

**User-Specific Topics** (Implemented in `FCMService.php:266-268`, `305-307`, `341-343`):

Instead of: `booking_{bookingId}_payment` (anyone can subscribe)
We use: `user_{userId}_booking_{bookingId}_payment` (only that user's device)

**Benefits**:
- ✅ Prevents other users from subscribing to someone else's booking updates
- ✅ FCM doesn't validate topic subscribers, so we add user ID for access control
- ✅ Mobile app validates it's the correct user before subscribing

**Mobile App Must**:
1. Only subscribe to topics where `userId` matches logged-in user
2. Validate API response matches notification data
3. Use fallback polling if FCM fails

---

### 📊 Complete Data Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. CLIENT INITIATES PAYMENT                                     │
│    Mobile App → POST /api/v1/bookings/{id}/initiate-payment     │
│    Response: { payment_url: "https://myfatoorah.com/..." }      │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. MOBILE APP SUBSCRIBES TO TOPIC (with 2s delay)               │
│    topic = "user_{userId}_booking_{bookingId}_payment"          │
│    await messaging().subscribeToTopic(topic)                    │
│    await Future.delayed(Duration(seconds: 2))                   │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. USER COMPLETES PAYMENT ON MYFATOORAH                         │
│    MyFatoorah redirects to CallbackUrl OR sends Webhook         │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. BACKEND RECEIVES WEBHOOK/CALLBACK                            │
│    PaymentController::webhook() or ::paymentCallback()          │
│    → processPaymentStatusV2()                                   │
│    → Updates DB: payment.status = 'completed'                   │
│    → FCMService::sendPaymentCompleted() with 2s delay           │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. MOBILE APP RECEIVES FCM NOTIFICATION                         │
│    FirebaseMessaging.onMessage (foreground)                     │
│    FirebaseMessaging.onBackgroundMessage (background)           │
│    → Updates UI, shows success message                          │
│    → Calls API to verify: GET /api/v1/bookings/{id}             │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. CLEANUP                                                       │
│    Mobile app unsubscribes from topic                           │
│    await messaging().unsubscribeFromTopic(topic)                │
└─────────────────────────────────────────────────────────────────┘
```

**Timeout Flow** (if user abandons payment):
```
After 10 minutes → Scheduled job runs → Marks payment as failed
→ FCMService::sendPaymentTimeout() → Mobile receives notification
```

---

### 🧪 Testing Checklist

#### Backend Testing
- [x] FCM service methods created with proper error handling
- [x] Webhook sends FCM on payment success
- [x] Webhook sends FCM on payment failure
- [x] Scheduled job detects expired payments
- [x] Test endpoints available for manual FCM testing
- [x] 2-second delay implemented (race condition prevention)
- [x] User-specific topics implemented (security)
- [x] Comprehensive logging for debugging

#### Mobile Testing (For Mobile Developer)
- [ ] Subscribe to topic before initiating payment
- [ ] Receive notification when payment completes (foreground)
- [ ] Receive notification when payment completes (background)
- [ ] Receive notification when payment completes (app terminated)
- [ ] Receive notification on payment failure
- [ ] Receive notification on payment timeout
- [ ] Verify API response matches FCM data
- [ ] Test fallback polling when FCM unavailable
- [ ] Test unsubscribe after payment completion
- [ ] Test multiple concurrent payments (different bookings)

---

### 🐛 Debugging

#### Check FCM Logs
```bash
# Real-time logs
tail -f storage/logs/laravel.log | grep FCM

# Check specific payment
grep "booking_id.*8" storage/logs/laravel.log | grep FCM
```

#### Test FCM Manually
```bash
# Using admin test endpoint
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

#### Check Scheduled Job
```bash
# Run manually
php artisan payments:check-expired

# Check if scheduled
php artisan schedule:list | grep payment
```

#### Common Issues

| Issue | Solution |
|-------|----------|
| FCM not sending | Check `FCM_CREDENTIALS_PATH` in `.env` |
| Topic subscription fails | Verify topic naming: `user_{userId}_booking_{id}_payment` |
| No subscribers found | Mobile app must subscribe BEFORE backend sends |
| Race condition | Backend has 2s delay, mobile should also wait 2s after subscribe |
| Wrong user receives notification | Check user ID in topic name matches booking.client_id |

---

## 🐛 Troubleshooting

### FCM not receiving messages

1. **Check Firebase configuration**:
   - `google-services.json` (Android) in correct location
   - `GoogleService-Info.plist` (iOS) in correct location
   - Bundle ID matches Firebase project

2. **Check topic subscription**:
   ```dart
   // Log all subscriptions
   final prefs = await SharedPreferences.getInstance();
   print('Subscribed topics: ${prefs.getStringList('fcm_topics')}');
   ```

3. **Check FCM token**:
   ```dart
   String? token = await FirebaseMessaging.instance.getToken();
   print('FCM Token: $token');
   // Send to backend for debugging
   ```

4. **Enable Firebase debug logging**:
   - Android: `adb shell setprop log.tag.FA VERBOSE`
   - iOS: Add `-FIRDebugEnabled` to scheme arguments

### Notifications not showing

1. **Check permissions** (iOS):
   ```dart
   NotificationSettings settings = await FirebaseMessaging.instance.getNotificationSettings();
   print('Notification permission: ${settings.authorizationStatus}');
   ```

2. **Check notification channels** (Android):
   - High importance channel must be created
   - Check app notification settings

### Messages delayed

- FCM uses Google Play Services (Android) / APNs (iOS)
- Delays are normal (1-10 seconds)
- In China, use alternative push services

---
## 🔧 Backend Implementation Summary

### ✅ Implemented Components

#### 1. FCM Service (`app/Services/FCMService.php`)

The FCM service handles sending push notifications to topics using Firebase Admin SDK.

**Key Methods**:
- `sendPaymentCompleted($booking, $payment)` - Sends payment success notification
- `sendPaymentFailed($booking, $payment, $errorMessage)` - Sends payment failure notification
- `sendPaymentTimeout($booking, $payment)` - Sends payment timeout notification

**Features**:
- ✅ 2-second delay before sending (prevents race condition)
- ✅ User-specific topics: `user_{userId}_booking_{bookingId}_payment`
- ✅ Comprehensive error logging
- ✅ Retry logic with exponential backoff (handled by base `sendToTopic` method)

**Example Topic**: `user_42_booking_123_payment` (only user #42 can subscribe)

#### 2. Payment Webhook Handler (`app/Http/Controllers/Api/PaymentController.php`)

**Updated Methods**:
- `processPaymentStatusV2()` - Processes webhook V2 data and sends FCM on success/failure

**Success Flow** (`PaymentController.php:625-638`):
```php
// After updating payment status to 'completed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentCompleted($payment->booking, $payment);
```

**Failure Flow** (`PaymentController.php:667-680`):
```php
// After updating payment status to 'failed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentFailed($payment->booking, $payment, $failureReason);
```

#### 3. Scheduled Job for Timeout (`app/Console/Commands/CheckExpiredPayments.php`)

**Command**: `php artisan payments:check-expired`

**Schedule**: Runs every minute (configured in `routes/console.php:15`)

**What It Does**:
1. Finds payments older than timeout setting (default 10 minutes)
2. Updates payment status to 'failed' with reason 'Payment timeout expired'
3. Sends FCM notification via `sendPaymentTimeout()`
4. Creates in-app notification for client

**Usage**:
```bash
# Manual test
php artisan payments:check-expired

# Runs automatically via cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

#### 4. Test Endpoints (Admin Only)

Three test endpoints for FCM verification:

| Endpoint | Method | Body | Purpose |
|----------|--------|------|---------|
| `/api/v1/admin/test/fcm/payment-completed` | POST | `{"booking_id": 1}` | Test success notification |
| `/api/v1/admin/test/fcm/payment-failed` | POST | `{"booking_id": 1, "error_message": "Card declined"}` | Test failure notification |
| `/api/v1/admin/test/fcm/payment-timeout` | POST | `{"booking_id": 1}` | Test timeout notification |

**Example Test**:
```bash
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

**Response**:
```json
{
  "success": true,
  "message": "FCM payment completed notification sent",
  "data": {
    "booking_id": 8,
    "payment_id": 12,
    "topic": "user_42_booking_8_payment",
    "fcm_result": true
  }
}
```

---

### 🔐 Security Implementation

**User-Specific Topics** (Implemented in `FCMService.php:266-268`, `305-307`, `341-343`):

Instead of: `booking_{bookingId}_payment` (anyone can subscribe)
We use: `user_{userId}_booking_{bookingId}_payment` (only that user's device)

**Benefits**:
- ✅ Prevents other users from subscribing to someone else's booking updates
- ✅ FCM doesn't validate topic subscribers, so we add user ID for access control
- ✅ Mobile app validates it's the correct user before subscribing

**Mobile App Must**:
1. Only subscribe to topics where `userId` matches logged-in user
2. Validate API response matches notification data
3. Use fallback polling if FCM fails

---

### 📊 Complete Data Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. CLIENT INITIATES PAYMENT                                     │
│    Mobile App → POST /api/v1/bookings/{id}/initiate-payment     │
│    Response: { payment_url: "https://myfatoorah.com/..." }      │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. MOBILE APP SUBSCRIBES TO TOPIC (with 2s delay)               │
│    topic = "user_{userId}_booking_{bookingId}_payment"          │
│    await messaging().subscribeToTopic(topic)                    │
│    await Future.delayed(Duration(seconds: 2))                   │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. USER COMPLETES PAYMENT ON MYFATOORAH                         │
│    MyFatoorah redirects to CallbackUrl OR sends Webhook         │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. BACKEND RECEIVES WEBHOOK/CALLBACK                            │
│    PaymentController::webhook() or ::paymentCallback()          │
│    → processPaymentStatusV2()                                   │
│    → Updates DB: payment.status = 'completed'                   │
│    → FCMService::sendPaymentCompleted() with 2s delay           │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. MOBILE APP RECEIVES FCM NOTIFICATION                         │
│    FirebaseMessaging.onMessage (foreground)                     │
│    FirebaseMessaging.onBackgroundMessage (background)           │
│    → Updates UI, shows success message                          │
│    → Calls API to verify: GET /api/v1/bookings/{id}             │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. CLEANUP                                                       │
│    Mobile app unsubscribes from topic                           │
│    await messaging().unsubscribeFromTopic(topic)                │
└─────────────────────────────────────────────────────────────────┘
```

**Timeout Flow** (if user abandons payment):
```
After 10 minutes → Scheduled job runs → Marks payment as failed
→ FCMService::sendPaymentTimeout() → Mobile receives notification
```

---

### 🧪 Testing Checklist

#### Backend Testing
- [x] FCM service methods created with proper error handling
- [x] Webhook sends FCM on payment success
- [x] Webhook sends FCM on payment failure
- [x] Scheduled job detects expired payments
- [x] Test endpoints available for manual FCM testing
- [x] 2-second delay implemented (race condition prevention)
- [x] User-specific topics implemented (security)
- [x] Comprehensive logging for debugging

#### Mobile Testing (For Mobile Developer)
- [ ] Subscribe to topic before initiating payment
- [ ] Receive notification when payment completes (foreground)
- [ ] Receive notification when payment completes (background)
- [ ] Receive notification when payment completes (app terminated)
- [ ] Receive notification on payment failure
- [ ] Receive notification on payment timeout
- [ ] Verify API response matches FCM data
- [ ] Test fallback polling when FCM unavailable
- [ ] Test unsubscribe after payment completion
- [ ] Test multiple concurrent payments (different bookings)

---

### 🐛 Debugging

#### Check FCM Logs
```bash
# Real-time logs
tail -f storage/logs/laravel.log | grep FCM

# Check specific payment
grep "booking_id.*8" storage/logs/laravel.log | grep FCM
```

#### Test FCM Manually
```bash
# Using admin test endpoint
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

#### Check Scheduled Job
```bash
# Run manually
php artisan payments:check-expired

# Check if scheduled
php artisan schedule:list | grep payment
```

#### Common Issues

| Issue | Solution |
|-------|----------|
| FCM not sending | Check `FCM_CREDENTIALS_PATH` in `.env` |
| Topic subscription fails | Verify topic naming: `user_{userId}_booking_{id}_payment` |
| No subscribers found | Mobile app must subscribe BEFORE backend sends |
| Race condition | Backend has 2s delay, mobile should also wait 2s after subscribe |
| Wrong user receives notification | Check user ID in topic name matches booking.client_id |

---

## 📊 Complete Flow Diagram

```
[Mobile App] User clicks "Pay Now"
      ↓
[Mobile] Subscribe to: booking_8_payment
      ↓
[Mobile] Wait 2 seconds (subscription propagation)
      ↓
[Mobile] Call API: POST /api/v1/payments/initiate
      ↓
[Mobile] Start local 10-min timeout timer
      ↓
[Mobile] Open MyFatoorah payment webview
      ↓
      ├─→ [User completes payment] ─→ [MyFatoorah] ─→ Webhook ─→ [Backend]
      │                                                              ↓
      │                                                    Update DB (paid)
      │                                                              ↓
      │                                                    Send FCM to topic
      │                                                              ↓
      ├──────────────────────────────────────────────────────────→ FCM
      │                                                              ↓
      ↓                                                         [Mobile]
[Mobile] Receive FCM push                                           
      ↓
[Mobile] Validate with API
      ↓
[Mobile] Close payment screen
      ↓
[Mobile] Show success message
      ↓
[Mobile] Cancel local timeout timer
      ↓
[Mobile] Unsubscribe from topic
      ↓
[Mobile] Refresh booking list
```

---
## 🔧 Backend Implementation Summary

### ✅ Implemented Components

#### 1. FCM Service (`app/Services/FCMService.php`)

The FCM service handles sending push notifications to topics using Firebase Admin SDK.

**Key Methods**:
- `sendPaymentCompleted($booking, $payment)` - Sends payment success notification
- `sendPaymentFailed($booking, $payment, $errorMessage)` - Sends payment failure notification
- `sendPaymentTimeout($booking, $payment)` - Sends payment timeout notification

**Features**:
- ✅ 2-second delay before sending (prevents race condition)
- ✅ User-specific topics: `user_{userId}_booking_{bookingId}_payment`
- ✅ Comprehensive error logging
- ✅ Retry logic with exponential backoff (handled by base `sendToTopic` method)

**Example Topic**: `user_42_booking_123_payment` (only user #42 can subscribe)

#### 2. Payment Webhook Handler (`app/Http/Controllers/Api/PaymentController.php`)

**Updated Methods**:
- `processPaymentStatusV2()` - Processes webhook V2 data and sends FCM on success/failure

**Success Flow** (`PaymentController.php:625-638`):
```php
// After updating payment status to 'completed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentCompleted($payment->booking, $payment);
```

**Failure Flow** (`PaymentController.php:667-680`):
```php
// After updating payment status to 'failed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentFailed($payment->booking, $payment, $failureReason);
```

#### 3. Scheduled Job for Timeout (`app/Console/Commands/CheckExpiredPayments.php`)

**Command**: `php artisan payments:check-expired`

**Schedule**: Runs every minute (configured in `routes/console.php:15`)

**What It Does**:
1. Finds payments older than timeout setting (default 10 minutes)
2. Updates payment status to 'failed' with reason 'Payment timeout expired'
3. Sends FCM notification via `sendPaymentTimeout()`
4. Creates in-app notification for client

**Usage**:
```bash
# Manual test
php artisan payments:check-expired

# Runs automatically via cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

#### 4. Test Endpoints (Admin Only)

Three test endpoints for FCM verification:

| Endpoint | Method | Body | Purpose |
|----------|--------|------|---------|
| `/api/v1/admin/test/fcm/payment-completed` | POST | `{"booking_id": 1}` | Test success notification |
| `/api/v1/admin/test/fcm/payment-failed` | POST | `{"booking_id": 1, "error_message": "Card declined"}` | Test failure notification |
| `/api/v1/admin/test/fcm/payment-timeout` | POST | `{"booking_id": 1}` | Test timeout notification |

**Example Test**:
```bash
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

**Response**:
```json
{
  "success": true,
  "message": "FCM payment completed notification sent",
  "data": {
    "booking_id": 8,
    "payment_id": 12,
    "topic": "user_42_booking_8_payment",
    "fcm_result": true
  }
}
```

---

### 🔐 Security Implementation

**User-Specific Topics** (Implemented in `FCMService.php:266-268`, `305-307`, `341-343`):

Instead of: `booking_{bookingId}_payment` (anyone can subscribe)
We use: `user_{userId}_booking_{bookingId}_payment` (only that user's device)

**Benefits**:
- ✅ Prevents other users from subscribing to someone else's booking updates
- ✅ FCM doesn't validate topic subscribers, so we add user ID for access control
- ✅ Mobile app validates it's the correct user before subscribing

**Mobile App Must**:
1. Only subscribe to topics where `userId` matches logged-in user
2. Validate API response matches notification data
3. Use fallback polling if FCM fails

---

### 📊 Complete Data Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. CLIENT INITIATES PAYMENT                                     │
│    Mobile App → POST /api/v1/bookings/{id}/initiate-payment     │
│    Response: { payment_url: "https://myfatoorah.com/..." }      │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. MOBILE APP SUBSCRIBES TO TOPIC (with 2s delay)               │
│    topic = "user_{userId}_booking_{bookingId}_payment"          │
│    await messaging().subscribeToTopic(topic)                    │
│    await Future.delayed(Duration(seconds: 2))                   │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. USER COMPLETES PAYMENT ON MYFATOORAH                         │
│    MyFatoorah redirects to CallbackUrl OR sends Webhook         │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. BACKEND RECEIVES WEBHOOK/CALLBACK                            │
│    PaymentController::webhook() or ::paymentCallback()          │
│    → processPaymentStatusV2()                                   │
│    → Updates DB: payment.status = 'completed'                   │
│    → FCMService::sendPaymentCompleted() with 2s delay           │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. MOBILE APP RECEIVES FCM NOTIFICATION                         │
│    FirebaseMessaging.onMessage (foreground)                     │
│    FirebaseMessaging.onBackgroundMessage (background)           │
│    → Updates UI, shows success message                          │
│    → Calls API to verify: GET /api/v1/bookings/{id}             │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. CLEANUP                                                       │
│    Mobile app unsubscribes from topic                           │
│    await messaging().unsubscribeFromTopic(topic)                │
└─────────────────────────────────────────────────────────────────┘
```

**Timeout Flow** (if user abandons payment):
```
After 10 minutes → Scheduled job runs → Marks payment as failed
→ FCMService::sendPaymentTimeout() → Mobile receives notification
```

---

### 🧪 Testing Checklist

#### Backend Testing
- [x] FCM service methods created with proper error handling
- [x] Webhook sends FCM on payment success
- [x] Webhook sends FCM on payment failure
- [x] Scheduled job detects expired payments
- [x] Test endpoints available for manual FCM testing
- [x] 2-second delay implemented (race condition prevention)
- [x] User-specific topics implemented (security)
- [x] Comprehensive logging for debugging

#### Mobile Testing (For Mobile Developer)
- [ ] Subscribe to topic before initiating payment
- [ ] Receive notification when payment completes (foreground)
- [ ] Receive notification when payment completes (background)
- [ ] Receive notification when payment completes (app terminated)
- [ ] Receive notification on payment failure
- [ ] Receive notification on payment timeout
- [ ] Verify API response matches FCM data
- [ ] Test fallback polling when FCM unavailable
- [ ] Test unsubscribe after payment completion
- [ ] Test multiple concurrent payments (different bookings)

---

### 🐛 Debugging

#### Check FCM Logs
```bash
# Real-time logs
tail -f storage/logs/laravel.log | grep FCM

# Check specific payment
grep "booking_id.*8" storage/logs/laravel.log | grep FCM
```

#### Test FCM Manually
```bash
# Using admin test endpoint
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

#### Check Scheduled Job
```bash
# Run manually
php artisan payments:check-expired

# Check if scheduled
php artisan schedule:list | grep payment
```

#### Common Issues

| Issue | Solution |
|-------|----------|
| FCM not sending | Check `FCM_CREDENTIALS_PATH` in `.env` |
| Topic subscription fails | Verify topic naming: `user_{userId}_booking_{id}_payment` |
| No subscribers found | Mobile app must subscribe BEFORE backend sends |
| Race condition | Backend has 2s delay, mobile should also wait 2s after subscribe |
| Wrong user receives notification | Check user ID in topic name matches booking.client_id |

---

## 🎯 Summary for Mobile Developer

### Minimum Required Implementation:

1. ✅ **Initialize FCM** with permissions
2. ✅ **Subscribe** to `booking_{bookingId}_payment` BEFORE payment
3. ✅ **Wait 2 seconds** after subscription
4. ✅ **Handle 3 states**: foreground, background, terminated
5. ✅ **Handle 3 events**: completed, failed, timeout
6. ✅ **Validate** with backend API
7. ✅ **Unsubscribe** after event
8. ✅ **Cleanup** on logout

### Critical Points:

- ⚠️ Subscribe BEFORE initiating payment (race condition)
- ⚠️ Always validate FCM data with backend API (security)
- ⚠️ Use NavigationService/global key for navigation (not BuildContext)
- ⚠️ Handle all 3 app states (foreground/background/terminated)
- ⚠️ Implement local timeout timer as backup
- ⚠️ Clean up subscriptions on logout

---
## 🔧 Backend Implementation Summary

### ✅ Implemented Components

#### 1. FCM Service (`app/Services/FCMService.php`)

The FCM service handles sending push notifications to topics using Firebase Admin SDK.

**Key Methods**:
- `sendPaymentCompleted($booking, $payment)` - Sends payment success notification
- `sendPaymentFailed($booking, $payment, $errorMessage)` - Sends payment failure notification
- `sendPaymentTimeout($booking, $payment)` - Sends payment timeout notification

**Features**:
- ✅ 2-second delay before sending (prevents race condition)
- ✅ User-specific topics: `user_{userId}_booking_{bookingId}_payment`
- ✅ Comprehensive error logging
- ✅ Retry logic with exponential backoff (handled by base `sendToTopic` method)

**Example Topic**: `user_42_booking_123_payment` (only user #42 can subscribe)

#### 2. Payment Webhook Handler (`app/Http/Controllers/Api/PaymentController.php`)

**Updated Methods**:
- `processPaymentStatusV2()` - Processes webhook V2 data and sends FCM on success/failure

**Success Flow** (`PaymentController.php:625-638`):
```php
// After updating payment status to 'completed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentCompleted($payment->booking, $payment);
```

**Failure Flow** (`PaymentController.php:667-680`):
```php
// After updating payment status to 'failed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentFailed($payment->booking, $payment, $failureReason);
```

#### 3. Scheduled Job for Timeout (`app/Console/Commands/CheckExpiredPayments.php`)

**Command**: `php artisan payments:check-expired`

**Schedule**: Runs every minute (configured in `routes/console.php:15`)

**What It Does**:
1. Finds payments older than timeout setting (default 10 minutes)
2. Updates payment status to 'failed' with reason 'Payment timeout expired'
3. Sends FCM notification via `sendPaymentTimeout()`
4. Creates in-app notification for client

**Usage**:
```bash
# Manual test
php artisan payments:check-expired

# Runs automatically via cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

#### 4. Test Endpoints (Admin Only)

Three test endpoints for FCM verification:

| Endpoint | Method | Body | Purpose |
|----------|--------|------|---------|
| `/api/v1/admin/test/fcm/payment-completed` | POST | `{"booking_id": 1}` | Test success notification |
| `/api/v1/admin/test/fcm/payment-failed` | POST | `{"booking_id": 1, "error_message": "Card declined"}` | Test failure notification |
| `/api/v1/admin/test/fcm/payment-timeout` | POST | `{"booking_id": 1}` | Test timeout notification |

**Example Test**:
```bash
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

**Response**:
```json
{
  "success": true,
  "message": "FCM payment completed notification sent",
  "data": {
    "booking_id": 8,
    "payment_id": 12,
    "topic": "user_42_booking_8_payment",
    "fcm_result": true
  }
}
```

---

### 🔐 Security Implementation

**User-Specific Topics** (Implemented in `FCMService.php:266-268`, `305-307`, `341-343`):

Instead of: `booking_{bookingId}_payment` (anyone can subscribe)
We use: `user_{userId}_booking_{bookingId}_payment` (only that user's device)

**Benefits**:
- ✅ Prevents other users from subscribing to someone else's booking updates
- ✅ FCM doesn't validate topic subscribers, so we add user ID for access control
- ✅ Mobile app validates it's the correct user before subscribing

**Mobile App Must**:
1. Only subscribe to topics where `userId` matches logged-in user
2. Validate API response matches notification data
3. Use fallback polling if FCM fails

---

### 📊 Complete Data Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. CLIENT INITIATES PAYMENT                                     │
│    Mobile App → POST /api/v1/bookings/{id}/initiate-payment     │
│    Response: { payment_url: "https://myfatoorah.com/..." }      │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. MOBILE APP SUBSCRIBES TO TOPIC (with 2s delay)               │
│    topic = "user_{userId}_booking_{bookingId}_payment"          │
│    await messaging().subscribeToTopic(topic)                    │
│    await Future.delayed(Duration(seconds: 2))                   │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. USER COMPLETES PAYMENT ON MYFATOORAH                         │
│    MyFatoorah redirects to CallbackUrl OR sends Webhook         │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. BACKEND RECEIVES WEBHOOK/CALLBACK                            │
│    PaymentController::webhook() or ::paymentCallback()          │
│    → processPaymentStatusV2()                                   │
│    → Updates DB: payment.status = 'completed'                   │
│    → FCMService::sendPaymentCompleted() with 2s delay           │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. MOBILE APP RECEIVES FCM NOTIFICATION                         │
│    FirebaseMessaging.onMessage (foreground)                     │
│    FirebaseMessaging.onBackgroundMessage (background)           │
│    → Updates UI, shows success message                          │
│    → Calls API to verify: GET /api/v1/bookings/{id}             │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. CLEANUP                                                       │
│    Mobile app unsubscribes from topic                           │
│    await messaging().unsubscribeFromTopic(topic)                │
└─────────────────────────────────────────────────────────────────┘
```

**Timeout Flow** (if user abandons payment):
```
After 10 minutes → Scheduled job runs → Marks payment as failed
→ FCMService::sendPaymentTimeout() → Mobile receives notification
```

---

### 🧪 Testing Checklist

#### Backend Testing
- [x] FCM service methods created with proper error handling
- [x] Webhook sends FCM on payment success
- [x] Webhook sends FCM on payment failure
- [x] Scheduled job detects expired payments
- [x] Test endpoints available for manual FCM testing
- [x] 2-second delay implemented (race condition prevention)
- [x] User-specific topics implemented (security)
- [x] Comprehensive logging for debugging

#### Mobile Testing (For Mobile Developer)
- [ ] Subscribe to topic before initiating payment
- [ ] Receive notification when payment completes (foreground)
- [ ] Receive notification when payment completes (background)
- [ ] Receive notification when payment completes (app terminated)
- [ ] Receive notification on payment failure
- [ ] Receive notification on payment timeout
- [ ] Verify API response matches FCM data
- [ ] Test fallback polling when FCM unavailable
- [ ] Test unsubscribe after payment completion
- [ ] Test multiple concurrent payments (different bookings)

---

### 🐛 Debugging

#### Check FCM Logs
```bash
# Real-time logs
tail -f storage/logs/laravel.log | grep FCM

# Check specific payment
grep "booking_id.*8" storage/logs/laravel.log | grep FCM
```

#### Test FCM Manually
```bash
# Using admin test endpoint
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

#### Check Scheduled Job
```bash
# Run manually
php artisan payments:check-expired

# Check if scheduled
php artisan schedule:list | grep payment
```

#### Common Issues

| Issue | Solution |
|-------|----------|
| FCM not sending | Check `FCM_CREDENTIALS_PATH` in `.env` |
| Topic subscription fails | Verify topic naming: `user_{userId}_booking_{id}_payment` |
| No subscribers found | Mobile app must subscribe BEFORE backend sends |
| Race condition | Backend has 2s delay, mobile should also wait 2s after subscribe |
| Wrong user receives notification | Check user ID in topic name matches booking.client_id |

---

## 📞 Backend API Endpoints

### Endpoints that trigger FCM:

| Endpoint | Event | Topic |
|----------|-------|-------|
| `POST /api/v1/payments/webhook` | payment_completed | booking_{id}_payment |
| `POST /api/v1/payments/webhook` | payment_failed | booking_{id}_payment |
| `schedule:run` (cron) | payment_timeout | booking_{id}_payment |


---
## 🔧 Backend Implementation Summary

### ✅ Implemented Components

#### 1. FCM Service (`app/Services/FCMService.php`)

The FCM service handles sending push notifications to topics using Firebase Admin SDK.

**Key Methods**:
- `sendPaymentCompleted($booking, $payment)` - Sends payment success notification
- `sendPaymentFailed($booking, $payment, $errorMessage)` - Sends payment failure notification
- `sendPaymentTimeout($booking, $payment)` - Sends payment timeout notification

**Features**:
- ✅ 2-second delay before sending (prevents race condition)
- ✅ User-specific topics: `user_{userId}_booking_{bookingId}_payment`
- ✅ Comprehensive error logging
- ✅ Retry logic with exponential backoff (handled by base `sendToTopic` method)

**Example Topic**: `user_42_booking_123_payment` (only user #42 can subscribe)

#### 2. Payment Webhook Handler (`app/Http/Controllers/Api/PaymentController.php`)

**Updated Methods**:
- `processPaymentStatusV2()` - Processes webhook V2 data and sends FCM on success/failure

**Success Flow** (`PaymentController.php:625-638`):
```php
// After updating payment status to 'completed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentCompleted($payment->booking, $payment);
```

**Failure Flow** (`PaymentController.php:667-680`):
```php
// After updating payment status to 'failed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentFailed($payment->booking, $payment, $failureReason);
```

#### 3. Scheduled Job for Timeout (`app/Console/Commands/CheckExpiredPayments.php`)

**Command**: `php artisan payments:check-expired`

**Schedule**: Runs every minute (configured in `routes/console.php:15`)

**What It Does**:
1. Finds payments older than timeout setting (default 10 minutes)
2. Updates payment status to 'failed' with reason 'Payment timeout expired'
3. Sends FCM notification via `sendPaymentTimeout()`
4. Creates in-app notification for client

**Usage**:
```bash
# Manual test
php artisan payments:check-expired

# Runs automatically via cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

#### 4. Test Endpoints (Admin Only)

Three test endpoints for FCM verification:

| Endpoint | Method | Body | Purpose |
|----------|--------|------|---------|
| `/api/v1/admin/test/fcm/payment-completed` | POST | `{"booking_id": 1}` | Test success notification |
| `/api/v1/admin/test/fcm/payment-failed` | POST | `{"booking_id": 1, "error_message": "Card declined"}` | Test failure notification |
| `/api/v1/admin/test/fcm/payment-timeout` | POST | `{"booking_id": 1}` | Test timeout notification |

**Example Test**:
```bash
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

**Response**:
```json
{
  "success": true,
  "message": "FCM payment completed notification sent",
  "data": {
    "booking_id": 8,
    "payment_id": 12,
    "topic": "user_42_booking_8_payment",
    "fcm_result": true
  }
}
```

---

### 🔐 Security Implementation

**User-Specific Topics** (Implemented in `FCMService.php:266-268`, `305-307`, `341-343`):

Instead of: `booking_{bookingId}_payment` (anyone can subscribe)
We use: `user_{userId}_booking_{bookingId}_payment` (only that user's device)

**Benefits**:
- ✅ Prevents other users from subscribing to someone else's booking updates
- ✅ FCM doesn't validate topic subscribers, so we add user ID for access control
- ✅ Mobile app validates it's the correct user before subscribing

**Mobile App Must**:
1. Only subscribe to topics where `userId` matches logged-in user
2. Validate API response matches notification data
3. Use fallback polling if FCM fails

---

### 📊 Complete Data Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. CLIENT INITIATES PAYMENT                                     │
│    Mobile App → POST /api/v1/bookings/{id}/initiate-payment     │
│    Response: { payment_url: "https://myfatoorah.com/..." }      │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. MOBILE APP SUBSCRIBES TO TOPIC (with 2s delay)               │
│    topic = "user_{userId}_booking_{bookingId}_payment"          │
│    await messaging().subscribeToTopic(topic)                    │
│    await Future.delayed(Duration(seconds: 2))                   │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. USER COMPLETES PAYMENT ON MYFATOORAH                         │
│    MyFatoorah redirects to CallbackUrl OR sends Webhook         │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. BACKEND RECEIVES WEBHOOK/CALLBACK                            │
│    PaymentController::webhook() or ::paymentCallback()          │
│    → processPaymentStatusV2()                                   │
│    → Updates DB: payment.status = 'completed'                   │
│    → FCMService::sendPaymentCompleted() with 2s delay           │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. MOBILE APP RECEIVES FCM NOTIFICATION                         │
│    FirebaseMessaging.onMessage (foreground)                     │
│    FirebaseMessaging.onBackgroundMessage (background)           │
│    → Updates UI, shows success message                          │
│    → Calls API to verify: GET /api/v1/bookings/{id}             │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. CLEANUP                                                       │
│    Mobile app unsubscribes from topic                           │
│    await messaging().unsubscribeFromTopic(topic)                │
└─────────────────────────────────────────────────────────────────┘
```

**Timeout Flow** (if user abandons payment):
```
After 10 minutes → Scheduled job runs → Marks payment as failed
→ FCMService::sendPaymentTimeout() → Mobile receives notification
```

---

### 🧪 Testing Checklist

#### Backend Testing
- [x] FCM service methods created with proper error handling
- [x] Webhook sends FCM on payment success
- [x] Webhook sends FCM on payment failure
- [x] Scheduled job detects expired payments
- [x] Test endpoints available for manual FCM testing
- [x] 2-second delay implemented (race condition prevention)
- [x] User-specific topics implemented (security)
- [x] Comprehensive logging for debugging

#### Mobile Testing (For Mobile Developer)
- [ ] Subscribe to topic before initiating payment
- [ ] Receive notification when payment completes (foreground)
- [ ] Receive notification when payment completes (background)
- [ ] Receive notification when payment completes (app terminated)
- [ ] Receive notification on payment failure
- [ ] Receive notification on payment timeout
- [ ] Verify API response matches FCM data
- [ ] Test fallback polling when FCM unavailable
- [ ] Test unsubscribe after payment completion
- [ ] Test multiple concurrent payments (different bookings)

---

### 🐛 Debugging

#### Check FCM Logs
```bash
# Real-time logs
tail -f storage/logs/laravel.log | grep FCM

# Check specific payment
grep "booking_id.*8" storage/logs/laravel.log | grep FCM
```

#### Test FCM Manually
```bash
# Using admin test endpoint
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

#### Check Scheduled Job
```bash
# Run manually
php artisan payments:check-expired

# Check if scheduled
php artisan schedule:list | grep payment
```

#### Common Issues

| Issue | Solution |
|-------|----------|
| FCM not sending | Check `FCM_CREDENTIALS_PATH` in `.env` |
| Topic subscription fails | Verify topic naming: `user_{userId}_booking_{id}_payment` |
| No subscribers found | Mobile app must subscribe BEFORE backend sends |
| Race condition | Backend has 2s delay, mobile should also wait 2s after subscribe |
| Wrong user receives notification | Check user ID in topic name matches booking.client_id |

---

---
## 🔧 Backend Implementation Summary

### ✅ Implemented Components

#### 1. FCM Service (`app/Services/FCMService.php`)

The FCM service handles sending push notifications to topics using Firebase Admin SDK.

**Key Methods**:
- `sendPaymentCompleted($booking, $payment)` - Sends payment success notification
- `sendPaymentFailed($booking, $payment, $errorMessage)` - Sends payment failure notification
- `sendPaymentTimeout($booking, $payment)` - Sends payment timeout notification

**Features**:
- ✅ 2-second delay before sending (prevents race condition)
- ✅ User-specific topics: `user_{userId}_booking_{bookingId}_payment`
- ✅ Comprehensive error logging
- ✅ Retry logic with exponential backoff (handled by base `sendToTopic` method)

**Example Topic**: `user_42_booking_123_payment` (only user #42 can subscribe)

#### 2. Payment Webhook Handler (`app/Http/Controllers/Api/PaymentController.php`)

**Updated Methods**:
- `processPaymentStatusV2()` - Processes webhook V2 data and sends FCM on success/failure

**Success Flow** (`PaymentController.php:625-638`):
```php
// After updating payment status to 'completed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentCompleted($payment->booking, $payment);
```

**Failure Flow** (`PaymentController.php:667-680`):
```php
// After updating payment status to 'failed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentFailed($payment->booking, $payment, $failureReason);
```

#### 3. Scheduled Job for Timeout (`app/Console/Commands/CheckExpiredPayments.php`)

**Command**: `php artisan payments:check-expired`

**Schedule**: Runs every minute (configured in `routes/console.php:15`)

**What It Does**:
1. Finds payments older than timeout setting (default 10 minutes)
2. Updates payment status to 'failed' with reason 'Payment timeout expired'
3. Sends FCM notification via `sendPaymentTimeout()`
4. Creates in-app notification for client

**Usage**:
```bash
# Manual test
php artisan payments:check-expired

# Runs automatically via cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

#### 4. Test Endpoints (Admin Only)

Three test endpoints for FCM verification:

| Endpoint | Method | Body | Purpose |
|----------|--------|------|---------|
| `/api/v1/admin/test/fcm/payment-completed` | POST | `{"booking_id": 1}` | Test success notification |
| `/api/v1/admin/test/fcm/payment-failed` | POST | `{"booking_id": 1, "error_message": "Card declined"}` | Test failure notification |
| `/api/v1/admin/test/fcm/payment-timeout` | POST | `{"booking_id": 1}` | Test timeout notification |

**Example Test**:
```bash
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

**Response**:
```json
{
  "success": true,
  "message": "FCM payment completed notification sent",
  "data": {
    "booking_id": 8,
    "payment_id": 12,
    "topic": "user_42_booking_8_payment",
    "fcm_result": true
  }
}
```

---

### 🔐 Security Implementation

**User-Specific Topics** (Implemented in `FCMService.php:266-268`, `305-307`, `341-343`):

Instead of: `booking_{bookingId}_payment` (anyone can subscribe)
We use: `user_{userId}_booking_{bookingId}_payment` (only that user's device)

**Benefits**:
- ✅ Prevents other users from subscribing to someone else's booking updates
- ✅ FCM doesn't validate topic subscribers, so we add user ID for access control
- ✅ Mobile app validates it's the correct user before subscribing

**Mobile App Must**:
1. Only subscribe to topics where `userId` matches logged-in user
2. Validate API response matches notification data
3. Use fallback polling if FCM fails

---

### 📊 Complete Data Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. CLIENT INITIATES PAYMENT                                     │
│    Mobile App → POST /api/v1/bookings/{id}/initiate-payment     │
│    Response: { payment_url: "https://myfatoorah.com/..." }      │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. MOBILE APP SUBSCRIBES TO TOPIC (with 2s delay)               │
│    topic = "user_{userId}_booking_{bookingId}_payment"          │
│    await messaging().subscribeToTopic(topic)                    │
│    await Future.delayed(Duration(seconds: 2))                   │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. USER COMPLETES PAYMENT ON MYFATOORAH                         │
│    MyFatoorah redirects to CallbackUrl OR sends Webhook         │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. BACKEND RECEIVES WEBHOOK/CALLBACK                            │
│    PaymentController::webhook() or ::paymentCallback()          │
│    → processPaymentStatusV2()                                   │
│    → Updates DB: payment.status = 'completed'                   │
│    → FCMService::sendPaymentCompleted() with 2s delay           │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. MOBILE APP RECEIVES FCM NOTIFICATION                         │
│    FirebaseMessaging.onMessage (foreground)                     │
│    FirebaseMessaging.onBackgroundMessage (background)           │
│    → Updates UI, shows success message                          │
│    → Calls API to verify: GET /api/v1/bookings/{id}             │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. CLEANUP                                                       │
│    Mobile app unsubscribes from topic                           │
│    await messaging().unsubscribeFromTopic(topic)                │
└─────────────────────────────────────────────────────────────────┘
```

**Timeout Flow** (if user abandons payment):
```
After 10 minutes → Scheduled job runs → Marks payment as failed
→ FCMService::sendPaymentTimeout() → Mobile receives notification
```

---

### 🧪 Testing Checklist

#### Backend Testing
- [x] FCM service methods created with proper error handling
- [x] Webhook sends FCM on payment success
- [x] Webhook sends FCM on payment failure
- [x] Scheduled job detects expired payments
- [x] Test endpoints available for manual FCM testing
- [x] 2-second delay implemented (race condition prevention)
- [x] User-specific topics implemented (security)
- [x] Comprehensive logging for debugging

#### Mobile Testing (For Mobile Developer)
- [ ] Subscribe to topic before initiating payment
- [ ] Receive notification when payment completes (foreground)
- [ ] Receive notification when payment completes (background)
- [ ] Receive notification when payment completes (app terminated)
- [ ] Receive notification on payment failure
- [ ] Receive notification on payment timeout
- [ ] Verify API response matches FCM data
- [ ] Test fallback polling when FCM unavailable
- [ ] Test unsubscribe after payment completion
- [ ] Test multiple concurrent payments (different bookings)

---

### 🐛 Debugging

#### Check FCM Logs
```bash
# Real-time logs
tail -f storage/logs/laravel.log | grep FCM

# Check specific payment
grep "booking_id.*8" storage/logs/laravel.log | grep FCM
```

#### Test FCM Manually
```bash
# Using admin test endpoint
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

#### Check Scheduled Job
```bash
# Run manually
php artisan payments:check-expired

# Check if scheduled
php artisan schedule:list | grep payment
```

#### Common Issues

| Issue | Solution |
|-------|----------|
| FCM not sending | Check `FCM_CREDENTIALS_PATH` in `.env` |
| Topic subscription fails | Verify topic naming: `user_{userId}_booking_{id}_payment` |
| No subscribers found | Mobile app must subscribe BEFORE backend sends |
| Race condition | Backend has 2s delay, mobile should also wait 2s after subscribe |
| Wrong user receives notification | Check user ID in topic name matches booking.client_id |

---

## 📅 Version History

- **v2.1** (2025-11-18): Backend implementation complete with test endpoints and security
- **v2.0** (2025-11-18): Added error handling, all app states, race condition handling, testing guide
- **v1.0** (2025-11-17): Initial FCM implementation

---
## 🔧 Backend Implementation Summary

### ✅ Implemented Components

#### 1. FCM Service (`app/Services/FCMService.php`)

The FCM service handles sending push notifications to topics using Firebase Admin SDK.

**Key Methods**:
- `sendPaymentCompleted($booking, $payment)` - Sends payment success notification
- `sendPaymentFailed($booking, $payment, $errorMessage)` - Sends payment failure notification
- `sendPaymentTimeout($booking, $payment)` - Sends payment timeout notification

**Features**:
- ✅ 2-second delay before sending (prevents race condition)
- ✅ User-specific topics: `user_{userId}_booking_{bookingId}_payment`
- ✅ Comprehensive error logging
- ✅ Retry logic with exponential backoff (handled by base `sendToTopic` method)

**Example Topic**: `user_42_booking_123_payment` (only user #42 can subscribe)

#### 2. Payment Webhook Handler (`app/Http/Controllers/Api/PaymentController.php`)

**Updated Methods**:
- `processPaymentStatusV2()` - Processes webhook V2 data and sends FCM on success/failure

**Success Flow** (`PaymentController.php:625-638`):
```php
// After updating payment status to 'completed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentCompleted($payment->booking, $payment);
```

**Failure Flow** (`PaymentController.php:667-680`):
```php
// After updating payment status to 'failed'
$fcmService = app(\App\Services\FCMService::class);
$fcmService->sendPaymentFailed($payment->booking, $payment, $failureReason);
```

#### 3. Scheduled Job for Timeout (`app/Console/Commands/CheckExpiredPayments.php`)

**Command**: `php artisan payments:check-expired`

**Schedule**: Runs every minute (configured in `routes/console.php:15`)

**What It Does**:
1. Finds payments older than timeout setting (default 10 minutes)
2. Updates payment status to 'failed' with reason 'Payment timeout expired'
3. Sends FCM notification via `sendPaymentTimeout()`
4. Creates in-app notification for client

**Usage**:
```bash
# Manual test
php artisan payments:check-expired

# Runs automatically via cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

#### 4. Test Endpoints (Admin Only)

Three test endpoints for FCM verification:

| Endpoint | Method | Body | Purpose |
|----------|--------|------|---------|
| `/api/v1/admin/test/fcm/payment-completed` | POST | `{"booking_id": 1}` | Test success notification |
| `/api/v1/admin/test/fcm/payment-failed` | POST | `{"booking_id": 1, "error_message": "Card declined"}` | Test failure notification |
| `/api/v1/admin/test/fcm/payment-timeout` | POST | `{"booking_id": 1}` | Test timeout notification |

**Example Test**:
```bash
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

**Response**:
```json
{
  "success": true,
  "message": "FCM payment completed notification sent",
  "data": {
    "booking_id": 8,
    "payment_id": 12,
    "topic": "user_42_booking_8_payment",
    "fcm_result": true
  }
}
```

---

### 🔐 Security Implementation

**User-Specific Topics** (Implemented in `FCMService.php:266-268`, `305-307`, `341-343`):

Instead of: `booking_{bookingId}_payment` (anyone can subscribe)
We use: `user_{userId}_booking_{bookingId}_payment` (only that user's device)

**Benefits**:
- ✅ Prevents other users from subscribing to someone else's booking updates
- ✅ FCM doesn't validate topic subscribers, so we add user ID for access control
- ✅ Mobile app validates it's the correct user before subscribing

**Mobile App Must**:
1. Only subscribe to topics where `userId` matches logged-in user
2. Validate API response matches notification data
3. Use fallback polling if FCM fails

---

### 📊 Complete Data Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. CLIENT INITIATES PAYMENT                                     │
│    Mobile App → POST /api/v1/bookings/{id}/initiate-payment     │
│    Response: { payment_url: "https://myfatoorah.com/..." }      │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. MOBILE APP SUBSCRIBES TO TOPIC (with 2s delay)               │
│    topic = "user_{userId}_booking_{bookingId}_payment"          │
│    await messaging().subscribeToTopic(topic)                    │
│    await Future.delayed(Duration(seconds: 2))                   │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. USER COMPLETES PAYMENT ON MYFATOORAH                         │
│    MyFatoorah redirects to CallbackUrl OR sends Webhook         │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. BACKEND RECEIVES WEBHOOK/CALLBACK                            │
│    PaymentController::webhook() or ::paymentCallback()          │
│    → processPaymentStatusV2()                                   │
│    → Updates DB: payment.status = 'completed'                   │
│    → FCMService::sendPaymentCompleted() with 2s delay           │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. MOBILE APP RECEIVES FCM NOTIFICATION                         │
│    FirebaseMessaging.onMessage (foreground)                     │
│    FirebaseMessaging.onBackgroundMessage (background)           │
│    → Updates UI, shows success message                          │
│    → Calls API to verify: GET /api/v1/bookings/{id}             │
└─────────────────────────────────────────────────────────────────┘
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. CLEANUP                                                       │
│    Mobile app unsubscribes from topic                           │
│    await messaging().unsubscribeFromTopic(topic)                │
└─────────────────────────────────────────────────────────────────┘
```

**Timeout Flow** (if user abandons payment):
```
After 10 minutes → Scheduled job runs → Marks payment as failed
→ FCMService::sendPaymentTimeout() → Mobile receives notification
```

---

### 🧪 Testing Checklist

#### Backend Testing
- [x] FCM service methods created with proper error handling
- [x] Webhook sends FCM on payment success
- [x] Webhook sends FCM on payment failure
- [x] Scheduled job detects expired payments
- [x] Test endpoints available for manual FCM testing
- [x] 2-second delay implemented (race condition prevention)
- [x] User-specific topics implemented (security)
- [x] Comprehensive logging for debugging

#### Mobile Testing (For Mobile Developer)
- [ ] Subscribe to topic before initiating payment
- [ ] Receive notification when payment completes (foreground)
- [ ] Receive notification when payment completes (background)
- [ ] Receive notification when payment completes (app terminated)
- [ ] Receive notification on payment failure
- [ ] Receive notification on payment timeout
- [ ] Verify API response matches FCM data
- [ ] Test fallback polling when FCM unavailable
- [ ] Test unsubscribe after payment completion
- [ ] Test multiple concurrent payments (different bookings)

---

### 🐛 Debugging

#### Check FCM Logs
```bash
# Real-time logs
tail -f storage/logs/laravel.log | grep FCM

# Check specific payment
grep "booking_id.*8" storage/logs/laravel.log | grep FCM
```

#### Test FCM Manually
```bash
# Using admin test endpoint
curl -X POST https://api.luky.com/api/v1/admin/test/fcm/payment-completed \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"booking_id": 8}'
```

#### Check Scheduled Job
```bash
# Run manually
php artisan payments:check-expired

# Check if scheduled
php artisan schedule:list | grep payment
```

#### Common Issues

| Issue | Solution |
|-------|----------|
| FCM not sending | Check `FCM_CREDENTIALS_PATH` in `.env` |
| Topic subscription fails | Verify topic naming: `user_{userId}_booking_{id}_payment` |
| No subscribers found | Mobile app must subscribe BEFORE backend sends |
| Race condition | Backend has 2s delay, mobile should also wait 2s after subscribe |
| Wrong user receives notification | Check user ID in topic name matches booking.client_id |

---

Generated: 2025-11-18
Updated: 2025-11-18 (Backend implementation summary added)
