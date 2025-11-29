<?php

return [
    // Page Title
    'notifications' => 'Notifications',
    
    // Stats
    'sent' => 'Sent',
    'failed' => 'Failed',
    'today' => 'Today',
    
    // Send Notification Card
    'send_notification' => 'Send Notification',
    'send_to' => 'Send To',
    'clients' => 'Clients',
    'providers' => 'Providers',
    'select_recipient' => 'Select Recipient',
    'required' => 'Required',
    'search_dropdown_hint' => 'Use the search box at the top of the dropdown.',
    'title_optional' => 'Title (Optional)',
    'message_placeholder' => 'Message from Luky Admin',
    'message' => 'Message',
    'write_your_message' => 'Write your message...',
    'message_tip' => 'Tip: keep it short and actionable. Max 500 characters.',
    'send' => 'Send',
    'sending' => 'Sending...',
    
    // Sent Notifications List
    'sent_notifications' => 'Sent Notifications',
    'search' => 'Search...',
    'all_status' => 'All Status',
    'status_sent' => 'Sent',
    'status_failed' => 'Failed',
    'filter' => 'Filter',
    
    // Table Headers
    'id' => '#',
    'message_col' => 'Message',
    'date_time' => 'Date & Time',
    'name' => 'Name',
    'type' => 'Type',
    'status' => 'Status',
    
    // Recipient Types
    'client' => 'Client',
    'provider' => 'Provider',
    
    // Empty State
    'no_notifications_yet' => 'No notifications sent yet',
    
    // Pagination
    'showing_notifications' => 'Showing :count notification(s)',
    
    // JavaScript Messages
    'error' => 'Error',
    'fill_required_fields' => 'Please fill in all required fields',
    'success' => 'Success!',
    'notification_sent' => 'Notification sent successfully',
    'failed_to_send' => 'Failed to send notification',
    'error_occurred' => 'An error occurred while sending the notification',
    'search_name' => 'Search name...',
    'select' => 'Select...',

    // Notification Types & Messages
    'types' => [
        'booking_request' => 'New Booking Request',
        'booking_accepted' => 'Booking Accepted',
        'booking_rejected' => 'Booking Rejected',
        'booking_cancelled' => 'Booking Cancelled',
        'booking_completed' => 'Booking Completed',
        'payment_reminder' => 'Payment Reminder',
        'payment_success' => 'Payment Successful',
        'payment_failed' => 'Payment Failed',
        'payment_timeout' => 'Payment Timeout',
        'payment_completed' => 'Payment Completed',
        'review_reminder' => 'Rate Your Experience',
        'new_review' => 'New Review Received',
        'provider_approved' => 'Congratulations!',
        'provider_rejected' => 'Application Status',
        'chat_message' => 'New message from :sender',
        'admin_announcement' => 'Announcement',
    ],

    'messages' => [
        // Booking Request
        'booking_request_body' => ':client has requested a booking for :date at :time',

        // Booking Accepted
        'booking_accepted_title' => 'Booking Accepted! | تم قبول الحجز!',
        'booking_accepted_body' => ':provider has accepted your booking. Please complete payment within :timeout minutes.',
        'booking_accepted_body_ar' => 'قبل :provider حجزك. يرجى إكمال الدفع خلال :timeout دقيقة.',

        // Booking Rejected
        'booking_rejected_title' => 'Booking Rejected | تم رفض الحجز',
        'booking_rejected_body' => ':provider has rejected your booking request',
        'booking_rejected_body_ar' => 'رفض :provider طلب حجزك',
        'booking_rejected_with_reason' => ':provider has rejected your booking request. Reason: :reason',
        'booking_rejected_with_reason_ar' => 'رفض :provider طلب حجزك. السبب: :reason',

        // Booking Cancelled
        'booking_cancelled_title' => 'Booking Cancelled | تم إلغاء الحجز',
        'booking_cancelled_client_body' => 'Your booking with :provider has been cancelled',
        'booking_cancelled_client_body_ar' => 'تم إلغاء حجزك مع :provider',
        'booking_cancelled_provider_body' => 'Booking from :client has been cancelled',
        'booking_cancelled_provider_body_ar' => 'تم إلغاء الحجز من :client',
        'booking_cancelled_reason' => 'Reason: :reason | السبب: :reason',

        // Booking Completed
        'booking_completed_client_title' => 'Booking Completed | تم إكمال الحجز',
        'booking_completed_client_body' => 'Your booking with :provider has been completed. Thank you!',
        'booking_completed_client_body_ar' => 'تم إكمال حجزك مع :provider. شكراً لك!',
        'booking_completed_provider_title' => 'Booking Completed | تم إكمال الحجز',
        'booking_completed_provider_body' => 'Booking with :client has been marked as completed.',
        'booking_completed_provider_body_ar' => 'تم تحديد الحجز مع :client كمكتمل.',

        // Payment Reminder
        'payment_reminder_title' => 'Payment Reminder | تذكير بالدفع',
        'payment_reminder_body' => 'Only :minutes minutes left to complete payment for your booking with :provider',
        'payment_reminder_body_ar' => 'باقي :minutes دقيقة فقط لإتمام الدفع لحجزك مع :provider',

        // Payment Success
        'payment_success_client_title' => 'Payment Successful | تم الدفع بنجاح',
        'payment_success_client_body' => 'Your payment of :amount SAR has been processed successfully for booking with :provider',
        'payment_success_client_body_ar' => 'تم معالجة دفعتك بقيمة :amount ريال بنجاح لحجزك مع :provider',
        'payment_success_provider_title' => 'Payment Received | تم استلام الدفع',
        'payment_success_provider_body' => 'Payment received for booking from :client. Amount: :amount SAR',
        'payment_success_provider_body_ar' => 'تم استلام الدفع لحجز من :client. المبلغ: :amount ريال',

        // Payment Failed
        'payment_failed_title' => 'Payment Failed | فشل الدفع',
        'payment_failed_body' => 'Payment for your booking failed. Please try again.',
        'payment_failed_body_ar' => 'فشل الدفع لحجزك. يرجى المحاولة مرة أخرى.',
        'payment_failed_with_reason' => 'Payment for your booking failed. Reason: :reason',
        'payment_failed_with_reason_ar' => 'فشل الدفع لحجزك. السبب: :reason',

        // Payment Timeout
        'payment_timeout_title' => 'Payment Timeout',
        'payment_timeout_body' => 'Payment time expired for booking #:booking_id. Please request a new payment link.',

        // Review Reminder
        'review_reminder_body' => 'How was your experience with :provider? Please leave a review.',

        // New Review
        'new_review_body' => ':client rated you :stars (:rating/5)',

        // Provider Approved
        'provider_approved_body' => 'Your business \':business\' has been approved. You can now start receiving bookings!',

        // Provider Rejected
        'provider_rejected_body' => 'Your business application \':business\' has been rejected',
        'provider_rejected_with_reason' => 'Your business application \':business\' has been rejected. Reason: :reason',

        // Chat Message
        'chat_message_text' => ':sender sent you a message',
        'chat_message_text_ar' => 'أرسل :sender رسالة',
        'chat_message_image' => ':sender sent you an image',
        'chat_message_image_ar' => 'أرسل :sender صورة',

        // Admin Announcements
        'booking_auto_cancelled' => 'Booking #:number was cancelled - :reason',
        'booking_auto_completed' => 'Booking #:number was auto-completed after service time ended. Amount: :amount SAR',
        'payment_timeout_admin' => 'Payment timeout for booking #:number. Amount: :amount SAR',
    ],
];
