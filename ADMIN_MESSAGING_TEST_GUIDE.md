# Admin Messaging & Notification System - Test Guide

## 📋 Overview

This guide provides comprehensive testing instructions for the admin messaging and notification system that allows admins to send messages and notifications to clients and providers from the dashboard.

## ✅ Test Results Summary

### Database-Level Tests: **PASSED** ✓

All 4 tests passed successfully:
- ✓ Client notification creation
- ✓ Client message (SMS) creation  
- ✓ Provider notification creation
- ✓ Provider message creation

**Test Users Found:**
- **Client**: ammar (ID: 6) - amaarboss1@outlook.com
- **Provider**: صالون نورة للتجميل (Provider ID: 1, User ID: 2)

## 🔧 Available Endpoints

### 1. Client Endpoints

#### Send Notification to Client
```
POST /admin/clients/{id}/send-notification
```
**Parameters:**
- `title` (required, string, max:255) - Notification title
- `message` (required, string, max:500) - Notification message

**Response:**
```json
{
  "success": true,
  "message": "Notification sent successfully to {client_name}"
}
```

#### Send Message to Client
```
POST /admin/clients/{id}/send-message
```
**Parameters:**
- `message` (required, string, max:500) - SMS message

**Response:**
```json
{
  "success": true,
  "message": "Message queued to be sent to {phone}"
}
```

### 2. Provider Endpoints

#### Send Notification to Provider
```
POST /admin/provider/{id}/send-notification
```
**Parameters:**
- `title` (required, string, max:255) - Notification title
- `message` (required, string, max:500) - Notification message
- `type` (optional, string) - One of: general, promotional, informational, alert

**Response:**
```json
{
  "success": true,
  "message": "Notification sent successfully to {business_name}"
}
```

#### Send Message to Provider
```
POST /admin/provider/{id}/send-message
```
**Parameters:**
- `message` (required, string, max:500) - Message content

**Response:**
```json
{
  "success": true,
  "message": "Message sent successfully to {business_name}"
}
```

### 3. General Notification Endpoint

#### Send Notification (Universal)
```
POST /admin/notifications/send
```
**Parameters:**
- `audience` (required, string) - Either "client" or "provider"
- `recipient_id` (required, integer) - User ID (must exist in users table)
- `title` (optional, string, max:100) - Notification title
- `message` (required, string, max:500) - Notification message

**Response:**
```json
{
  "success": true,
  "message": "Notification sent successfully",
  "data": {
    "notification_id": 123,
    "formatted_id": "NTF-000123",
    "recipient_name": "User Name",
    "recipient_type": "Client",
    "datetime": "09 Nov 2025, 05:49 PM",
    "status": "Sent"
  }
}
```

## 🧪 Testing Methods

### Method 1: Browser-Based Testing (Recommended)

1. **Open the Test UI:**
   - Navigate to: `http://your-domain/test-admin-messaging-ui.html`
   - Or open the file directly in your browser

2. **Login to Admin Dashboard:**
   - Make sure you're logged in as admin (admin@luky.sa)

3. **Fill in the Forms:**
   - Enter valid Client ID (e.g., 6)
   - Enter valid Provider ID (e.g., 1)
   - Fill in title and message fields
   - Click "Send" buttons

4. **Check Responses:**
   - Success messages will appear in green
   - Error messages will appear in red
   - Full response data will be displayed

### Method 2: Command Line Testing

#### Run Database Test:
```bash
php test-admin-messaging.php
```

This test will:
- Find test users (client and provider)
- Create notifications directly in database
- Verify notifications are stored correctly
- Check FCM service configuration
- Display all available endpoints

#### Run HTTP Test:
```bash
php test-admin-messaging-http.php
```

This test will:
- Authenticate as admin (admin@luky.sa)
- Simulate HTTP requests to all endpoints
- Test validation rules
- Test error handling

### Method 3: Manual API Testing (Postman/Insomnia)

1. **Login to get session cookie**
2. **Send POST requests to endpoints**

Example for client notification:
```bash
curl -X POST http://your-domain/admin/clients/6/send-notification \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-CSRF-TOKEN: your-csrf-token" \
  -d '{
    "title": "Test Notification",
    "message": "This is a test message"
  }'
```

### Method 4: Admin Dashboard UI

1. **Navigate to Clients Page:**
   - Go to `/admin/clients`
   - Click on a client
   - Look for "Send Notification" or "Send Message" buttons

2. **Navigate to Providers Page:**
   - Go to `/admin/providers`
   - Click on a provider
   - Look for "Send Notification" or "Send Message" buttons

3. **Navigate to Notifications Page:**
   - Go to `/admin/notifications/list`
   - Use the "Send Notification" form
   - Select audience (client/provider)
   - Select recipient
   - Fill in message details

## 📊 Verification Steps

### 1. Database Verification

Check notifications table:
```sql
SELECT * FROM notifications 
WHERE user_id IN (2, 6) 
ORDER BY created_at DESC 
LIMIT 10;
```

Check test notifications:
```sql
SELECT * FROM notifications 
WHERE data->>'test_mode' = 'true';
```

### 2. Mobile App Verification

- Open the mobile app as the test client (ID: 6)
- Check notifications section
- Verify notification appears
- Check notification details

### 3. Push Notification Verification

- Ensure FCM is configured in `config/services.php`
- Check that FCM tokens exist for users
- Verify push notifications are received on devices

### 4. Log Verification

Check Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

Look for:
- Notification creation logs
- FCM send logs
- Any error messages

## ⚠️ Known Issues & Notes

### 1. FCM Configuration
**Status:** ⚠️ Not Configured

The system shows:
```
⚠ Firebase credentials file not found or not configured
  - Configure in config/services.php
  - Push notifications will not be sent
```

**Action Required:**
- Add Firebase credentials file
- Update `config/services.php` with credentials path
- Restart the application

### 2. FCM Tokens Table
**Status:** ⚠️ Table Missing

Error: `relation "fcm_tokens" does not exist`

**Action Required:**
- Create migration for `fcm_tokens` table
- Or verify table name in database

### 3. SMS Integration
**Status:** 📝 TODO

Client messages use SMS type but actual SMS sending is not implemented.

**Action Required:**
- Integrate SMS provider (Twilio, Nexmo, etc.)
- Update `ClientController::sendMessage()` method

### 4. Admin-Provider Conversations
**Status:** 📝 Note

Provider messages are stored as notifications, not in conversations table.

**Note from code:**
> "For admin messages, we'll use the notification system instead of conversations since the conversations table is designed for client-provider chats only"

## 🎯 Test Scenarios

### Scenario 1: Send Notification to Client
1. Use Client ID: 6
2. Title: "Special Offer"
3. Message: "Get 20% off your next booking!"
4. Expected: Notification created, appears in app

### Scenario 2: Send Message to Provider
1. Use Provider ID: 1
2. Message: "Please update your service availability"
3. Expected: Message stored as notification

### Scenario 3: Broadcast to Multiple Users
1. Use general notification endpoint
2. Loop through multiple recipient IDs
3. Expected: All users receive notification

### Scenario 4: Validation Testing
1. Try sending without required fields
2. Try sending to non-existent user ID
3. Try sending with message > 500 characters
4. Expected: Proper validation errors

## 📈 Success Criteria

✅ **All tests passed if:**
1. Notifications are created in database
2. Notifications appear in mobile apps
3. Push notifications are received (if FCM configured)
4. No errors in Laravel logs
5. Proper validation on invalid inputs
6. Success/error messages are clear

## 🔄 Cleanup

To remove test notifications:
```sql
DELETE FROM notifications 
WHERE JSON_EXTRACT(data, '$.test_mode') = true;
```

Or keep them for reference and mark as read:
```sql
UPDATE notifications 
SET is_read = true 
WHERE JSON_EXTRACT(data, '$.test_mode') = true;
```

## 📝 Next Steps

1. **Configure FCM for Push Notifications**
   - Add Firebase credentials
   - Create/verify fcm_tokens table
   - Test push notifications on devices

2. **Implement SMS Integration**
   - Choose SMS provider
   - Add credentials
   - Test SMS sending

3. **UI/UX Improvements**
   - Add send notification buttons to client/provider detail pages
   - Add notification history view
   - Add bulk notification feature

4. **Monitoring & Analytics**
   - Track notification delivery rates
   - Monitor FCM success/failure rates
   - Add notification analytics dashboard

## 🆘 Troubleshooting

### Issue: "No admin user found"
**Solution:** Verify admin@luky.sa exists in database

### Issue: "CSRF token mismatch"
**Solution:** Ensure you're logged in and have valid session

### Issue: "Route not found"
**Solution:** Check routes/web.php for correct route definitions

### Issue: "User not found"
**Solution:** Verify user ID exists and is correct type (client/provider)

### Issue: "Push notification not received"
**Solution:** 
- Check FCM configuration
- Verify FCM token exists for user
- Check device notification settings
- Review Laravel logs for FCM errors

## 📞 Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check database for notification records
3. Verify user IDs and types
4. Test with the provided test scripts

---

**Last Updated:** November 9, 2025  
**Test Status:** ✅ Database Tests Passed | ⚠️ FCM Configuration Needed  
**Version:** 1.0
