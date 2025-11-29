<?php

return [
    // Page Title
    'notifications' => 'الإشعارات',
    
    // Stats
    'sent' => 'مرسل',
    'failed' => 'فشل',
    'today' => 'اليوم',
    
    // Send Notification Card
    'send_notification' => 'إرسال إشعار',
    'send_to' => 'إرسال إلى',
    'clients' => 'العملاء',
    'providers' => 'مقدمي الخدمة',
    'select_recipient' => 'اختر المستلم',
    'required' => 'مطلوب',
    'search_dropdown_hint' => 'استخدم مربع البحث في أعلى القائمة المنسدلة.',
    'title_optional' => 'العنوان (اختياري)',
    'message_placeholder' => 'رسالة من إدارة لوكي',
    'message' => 'الرسالة',
    'write_your_message' => 'اكتب رسالتك...',
    'message_tip' => 'نصيحة: اجعلها قصيرة وواضحة. الحد الأقصى 500 حرف.',
    'send' => 'إرسال',
    'sending' => 'جاري الإرسال...',
    
    // Sent Notifications List
    'sent_notifications' => 'الإشعارات المرسلة',
    'search' => 'بحث...',
    'all_status' => 'جميع الحالات',
    'status_sent' => 'مرسل',
    'status_failed' => 'فشل',
    'filter' => 'تصفية',
    
    // Table Headers
    'id' => '#',
    'message_col' => 'الرسالة',
    'date_time' => 'التاريخ والوقت',
    'name' => 'الاسم',
    'type' => 'النوع',
    'status' => 'الحالة',
    
    // Recipient Types
    'client' => 'عميل',
    'provider' => 'مقدم خدمة',
    
    // Empty State
    'no_notifications_yet' => 'لم يتم إرسال إشعارات بعد',
    
    // Pagination
    'showing_notifications' => 'عرض :count إشعار',
    
    // JavaScript Messages
    'error' => 'خطأ',
    'fill_required_fields' => 'يرجى ملء جميع الحقول المطلوبة',
    'success' => 'نجح!',
    'notification_sent' => 'تم إرسال الإشعار بنجاح',
    'failed_to_send' => 'فشل إرسال الإشعار',
    'error_occurred' => 'حدث خطأ أثناء إرسال الإشعار',
    'search_name' => 'البحث عن اسم...',
    'select' => 'اختر...',

    // Notification Types & Messages
    'types' => [
        'booking_request' => 'طلب حجز جديد',
        'booking_accepted' => 'تم قبول الحجز',
        'booking_rejected' => 'تم رفض الحجز',
        'booking_cancelled' => 'تم إلغاء الحجز',
        'booking_completed' => 'تم إكمال الحجز',
        'payment_reminder' => 'تذكير بالدفع',
        'payment_success' => 'تم الدفع بنجاح',
        'payment_failed' => 'فشل الدفع',
        'payment_timeout' => 'انتهت مهلة الدفع',
        'payment_completed' => 'اكتمل الدفع',
        'review_reminder' => 'قيّم تجربتك',
        'new_review' => 'تقييم جديد',
        'provider_approved' => 'مبروك!',
        'provider_rejected' => 'حالة الطلب',
        'chat_message' => 'رسالة جديدة من :sender',
        'admin_announcement' => 'إعلان',
    ],

    'messages' => [
        // Booking Request
        'booking_request_body' => 'طلب :client حجزاً في :date الساعة :time',

        // Booking Accepted
        'booking_accepted_title' => 'Booking Accepted! | تم قبول الحجز!',
        'booking_accepted_body' => 'قبل :provider حجزك. يرجى إكمال الدفع خلال :timeout دقيقة.',
        'booking_accepted_body_ar' => 'قبل :provider حجزك. يرجى إكمال الدفع خلال :timeout دقيقة.',

        // Booking Rejected
        'booking_rejected_title' => 'Booking Rejected | تم رفض الحجز',
        'booking_rejected_body' => 'رفض :provider طلب حجزك',
        'booking_rejected_body_ar' => 'رفض :provider طلب حجزك',
        'booking_rejected_with_reason' => 'رفض :provider طلب حجزك. السبب: :reason',
        'booking_rejected_with_reason_ar' => 'رفض :provider طلب حجزك. السبب: :reason',

        // Booking Cancelled
        'booking_cancelled_title' => 'Booking Cancelled | تم إلغاء الحجز',
        'booking_cancelled_client_body' => 'تم إلغاء حجزك مع :provider',
        'booking_cancelled_client_body_ar' => 'تم إلغاء حجزك مع :provider',
        'booking_cancelled_provider_body' => 'تم إلغاء الحجز من :client',
        'booking_cancelled_provider_body_ar' => 'تم إلغاء الحجز من :client',
        'booking_cancelled_reason' => 'السبب: :reason | Reason: :reason',

        // Booking Completed
        'booking_completed_client_title' => 'Booking Completed | تم إكمال الحجز',
        'booking_completed_client_body' => 'تم إكمال حجزك مع :provider. شكراً لك!',
        'booking_completed_client_body_ar' => 'تم إكمال حجزك مع :provider. شكراً لك!',
        'booking_completed_provider_title' => 'Booking Completed | تم إكمال الحجز',
        'booking_completed_provider_body' => 'تم تحديد الحجز مع :client كمكتمل.',
        'booking_completed_provider_body_ar' => 'تم تحديد الحجز مع :client كمكتمل.',

        // Payment Reminder
        'payment_reminder_title' => 'Payment Reminder | تذكير بالدفع',
        'payment_reminder_body' => 'باقي :minutes دقيقة فقط لإتمام الدفع لحجزك مع :provider',
        'payment_reminder_body_ar' => 'باقي :minutes دقيقة فقط لإتمام الدفع لحجزك مع :provider',

        // Payment Success
        'payment_success_client_title' => 'Payment Successful | تم الدفع بنجاح',
        'payment_success_client_body' => 'تم معالجة دفعتك بقيمة :amount ريال بنجاح لحجزك مع :provider',
        'payment_success_client_body_ar' => 'تم معالجة دفعتك بقيمة :amount ريال بنجاح لحجزك مع :provider',
        'payment_success_provider_title' => 'Payment Received | تم استلام الدفع',
        'payment_success_provider_body' => 'تم استلام الدفع لحجز من :client. المبلغ: :amount ريال',
        'payment_success_provider_body_ar' => 'تم استلام الدفع لحجز من :client. المبلغ: :amount ريال',

        // Payment Failed
        'payment_failed_title' => 'Payment Failed | فشل الدفع',
        'payment_failed_body' => 'فشل الدفع لحجزك. يرجى المحاولة مرة أخرى.',
        'payment_failed_body_ar' => 'فشل الدفع لحجزك. يرجى المحاولة مرة أخرى.',
        'payment_failed_with_reason' => 'فشل الدفع لحجزك. السبب: :reason',
        'payment_failed_with_reason_ar' => 'فشل الدفع لحجزك. السبب: :reason',

        // Payment Timeout
        'payment_timeout_title' => 'انتهت مهلة الدفع',
        'payment_timeout_body' => 'انتهت مهلة الدفع للحجز #:booking_id. يرجى طلب رابط دفع جديد.',

        // Review Reminder
        'review_reminder_body' => 'كيف كانت تجربتك مع :provider؟ يرجى ترك تقييم.',

        // New Review
        'new_review_body' => 'قيّمك :client بـ :stars (:rating/5)',

        // Provider Approved
        'provider_approved_body' => 'تمت الموافقة على نشاطك التجاري \':business\'. يمكنك الآن البدء في استقبال الحجوزات!',

        // Provider Rejected
        'provider_rejected_body' => 'تم رفض طلب نشاطك التجاري \':business\'',
        'provider_rejected_with_reason' => 'تم رفض طلب نشاطك التجاري \':business\'. السبب: :reason',

        // Chat Message
        'chat_message_text' => 'أرسل :sender رسالة',
        'chat_message_text_ar' => 'أرسل :sender رسالة',
        'chat_message_image' => 'أرسل :sender صورة',
        'chat_message_image_ar' => 'أرسل :sender صورة',

        // Admin Announcements
        'booking_auto_cancelled' => 'تم إلغاء الحجز #:number - :reason',
        'booking_auto_completed' => 'تم إكمال الحجز #:number تلقائياً بعد انتهاء وقت الخدمة. المبلغ: :amount ريال',
        'payment_timeout_admin' => 'انتهت مهلة الدفع للحجز #:number. المبلغ: :amount ريال',
    ],
];
