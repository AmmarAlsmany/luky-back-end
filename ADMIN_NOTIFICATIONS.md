# 🔔 Admin Notification System

## Overview
The system automatically sends notifications to all admin users when important events occur. Notifications appear in the topbar bell icon and can also be sent as push notifications via FCM.

---

## ✅ Events That Trigger Admin Notifications

### 📅 **Booking Events**

#### 1. New Booking Created
- **Trigger:** When a client creates a new booking
- **Type:** `booking_request`
- **Title:** "New Booking Request"
- **Message:** "New booking #{booking_number} from {client_name} for {provider_name}"
- **Data Included:**
  - booking_id
  - booking_number
  - client_name
  - provider_name
  - total_amount

#### 2. Booking Cancelled
- **Trigger:** When a booking status changes to 'cancelled'
- **Type:** `booking_cancelled`
- **Title:** "Booking Cancelled"
- **Message:** "Booking #{booking_number} has been cancelled"
- **Data Included:**
  - booking_id
  - booking_number
  - cancellation_reason

---

### 👔 **Service Provider Events**

#### 3. New Provider Registration
- **Trigger:** When a new service provider registers
- **Type:** `provider_registration`
- **Title:** "New Provider Registration"
- **Message:** "New provider '{business_name}' has registered and is pending approval"
- **Data Included:**
  - provider_id
  - business_name
  - user_id

#### 4. Provider Approved
- **Trigger:** When admin approves a provider
- **Type:** `provider_approved`
- **Title:** "Provider Approved"
- **Message:** "Provider '{business_name}' has been approved"
- **Data Included:**
  - provider_id
  - business_name

#### 5. Provider Rejected
- **Trigger:** When admin rejects a provider
- **Type:** `provider_rejected`
- **Title:** "Provider Rejected"
- **Message:** "Provider '{business_name}' has been rejected"
- **Data Included:**
  - provider_id
  - business_name

---

### ⭐ **Review Events**

#### 6. New Review Submitted
- **Trigger:** When a client submits a review
- **Type:** `new_review`
- **Title:** "New Review Received"
- **Message:** "{client_name} rated {provider_name} ⭐⭐⭐⭐⭐ (5/5)"
- **Data Included:**
  - review_id
  - provider_id
  - client_id
  - rating

---

### 👥 **User Events**

#### 7. New Client Registration
- **Trigger:** When a new client registers
- **Type:** `new_client`
- **Title:** "New Client Registration"
- **Message:** "New client '{name}' has registered on the platform"
- **Data Included:**
  - user_id
  - name
  - email
  - phone
  - city_id

#### 8. New Admin User Created
- **Trigger:** When a new admin user is created
- **Type:** `new_admin_user`
- **Title:** "New Admin User Created"
- **Message:** "New admin user '{name}' ({role}) has been created"
- **Data Included:**
  - user_id
  - name
  - email
  - role

---

### 💳 **Payment Events**

#### 9. New Payment Received
- **Trigger:** When a new payment is created
- **Type:** `new_payment`
- **Title:** "New Payment Received"
- **Message:** "Payment of {amount} {currency} received for booking #{booking_number}"
- **Data Included:**
  - payment_id
  - booking_id
  - booking_number
  - amount
  - currency
  - payment_gateway
  - status

#### 10. Payment Completed
- **Trigger:** When payment status changes to 'completed'
- **Type:** `payment_completed`
- **Title:** "Payment Completed"
- **Message:** "Payment of {amount} {currency} completed for booking #{booking_number}"
- **Data Included:**
  - payment_id
  - booking_id
  - booking_number
  - amount
  - currency
  - paid_at

#### 11. Payment Failed
- **Trigger:** When payment status changes to 'failed'
- **Type:** `payment_failed`
- **Title:** "Payment Failed"
- **Message:** "Payment of {amount} {currency} failed for booking #{booking_number} - Reason: {reason}"
- **Data Included:**
  - payment_id
  - booking_id
  - booking_number
  - amount
  - currency
  - failure_reason

#### 12. Payment Refunded
- **Trigger:** When payment status changes to 'refunded'
- **Type:** `payment_refunded`
- **Title:** "Payment Refunded"
- **Message:** "Payment of {amount} {currency} refunded for booking #{booking_number}"
- **Data Included:**
  - payment_id
  - booking_id
  - booking_number
  - amount
  - currency

---

## 📊 How It Works

### 1. **Event Observers**
Located in `app/Observers/`:
- `BookingObserver.php` - Monitors booking events
- `ServiceProviderObserver.php` - Monitors provider events
- `ReviewObserver.php` - Monitors review events
- `UserObserver.php` - Monitors user events
- `PaymentObserver.php` - Monitors payment events

### 2. **Notification Service**
Located in `app/Services/NotificationService.php`:
- `sendToAdmins()` - Sends notification to all admin users
- Automatically finds users with 'admin' or 'super_admin' role
- Creates in-app notification in database
- Sends push notification via FCM (if configured)

### 3. **Dashboard User Recipients**
Notifications are sent to **ALL users with dashboard access** (`user_type = 'admin'`), including:
- ✅ `super_admin` - Full system access
- ✅ `admin` - Administrative access
- ✅ `manager` - Management access
- ✅ `support_agent` - Customer support
- ✅ `content_manager` - Content management
- ✅ `analyst` - Analytics and reporting

**Note:** Only **active** users receive notifications (`is_active = true`)

### 4. **Display in Topbar**
- Bell icon shows unread notification count
- Dropdown shows latest 10 unread notifications
- Real-time updates (when page refreshes)

---

## 🔧 Configuration

### Enable/Disable Notifications
Observers are registered in `app/Providers/AppServiceProvider.php`:

```php
public function boot(): void
{
    // Register model observers
    Booking::observe(BookingObserver::class);
    Review::observe(ReviewObserver::class);
    User::observe(UserObserver::class);
    ServiceProviderModel::observe(ServiceProviderObserver::class);
}
```

### Push Notifications (FCM)
Configure in `.env`:
```env
FCM_CREDENTIALS_PATH=storage/app/firebase/luky-96cae-firebase-adminsdk-fbsvc-96f53ee261.json
```

---

## 📱 Notification Types

### In-App Notifications
- Stored in `notifications` table
- Displayed in topbar bell icon
- Can be marked as read
- Persist until deleted

### Push Notifications (FCM)
- Sent to admin's mobile device
- Requires FCM token registration
- Works even when app is closed
- Includes notification data for deep linking

---

## 🎯 Testing Notifications

### Test New Booking Notification
```php
php artisan tinker

// Create a test booking
$booking = Booking::factory()->create();
// Observer will automatically send notification to admins
```

### Test Provider Registration
```php
php artisan tinker

// Create a test provider
$provider = ServiceProvider::factory()->create();
// Observer will automatically send notification to admins
```

### Test Review Notification
```php
php artisan tinker

// Create a test review
$review = Review::factory()->create();
// Observer will automatically send notification to admins
```

---

## 📝 Adding New Admin Notifications

To add a new notification type:

1. **Add to Observer** (e.g., `BookingObserver.php`):
```php
public function updated(Booking $booking): void
{
    if ($booking->isDirty('status') && $booking->status === 'confirmed') {
        $this->notificationService->sendToAdmins(
            'booking_confirmed',
            'Booking Confirmed',
            "Booking #{$booking->booking_number} has been confirmed",
            ['booking_id' => $booking->id]
        );
    }
}
```

2. **That's it!** The notification will automatically:
   - Be sent to all admins
   - Appear in topbar
   - Be sent as push notification (if FCM configured)

---

## 🔍 Viewing Notifications

### In Dashboard
1. Click bell icon in topbar
2. See unread notification count (badge)
3. View latest 10 notifications in dropdown
4. Click to mark as read

### In Database
```sql
SELECT * FROM notifications 
WHERE user_id = {admin_id} 
ORDER BY created_at DESC;
```

### Via API
```php
GET /api/notifications
Authorization: Bearer {token}
```

---

## ✅ Current Status

- ✅ **Booking notifications** - Working
- ✅ **Provider notifications** - Working
- ✅ **Review notifications** - Working
- ✅ **Admin notification display** - Working in topbar
- ✅ **FCM push notifications** - Configured (requires mobile app)
- ✅ **Notification persistence** - Stored in database
- ✅ **Multi-admin support** - All admins receive notifications

---

## 🚀 Future Enhancements

1. **Email Notifications** - Send important notifications via email
2. **SMS Notifications** - Critical alerts via SMS
3. **Notification Preferences** - Let admins choose which notifications to receive
4. **Notification Grouping** - Group similar notifications
5. **Real-time Updates** - WebSocket for instant notifications without refresh
6. **Notification History** - View all past notifications
7. **Notification Filters** - Filter by type, date, read/unread

---

## 📞 Support

For issues or questions about the notification system:
- Check logs: `storage/logs/laravel.log`
- Review NotificationService: `app/Services/NotificationService.php`
- Check observers: `app/Observers/`
