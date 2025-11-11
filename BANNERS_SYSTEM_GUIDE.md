# 🎨 Banners Management System - Complete Guide

## 📋 **Overview**

The Banners Management System allows you to create, manage, and track promotional banners for your application.

---

## 🗂️ **Database Structure**

### **Banners Table:**
```sql
- id (primary key)
- title (string) - Banner title
- provider_name (string, nullable) - Provider/business name
- offer_text (string) - Offer/promotion text
- banner_template (string) - Template type
- title_color, title_font, title_size - Title styling
- provider_color, provider_font, provider_size - Provider styling
- offer_text_color, offer_bg_color, offer_font, offer_size - Offer styling
- image_url (string, nullable) - Banner image path
- link_url (string, nullable) - Click destination URL
- start_date (date) - When banner becomes active
- end_date (date) - When banner expires
- status (enum) - 'active', 'scheduled', 'expired'
- display_order (integer) - Display priority (0 = highest)
- display_location (enum) - 'home', 'services', 'providers', 'all'
- click_count (integer) - Number of clicks
- impression_count (integer) - Number of views
- is_active (boolean) - Manual enable/disable
- created_by (foreign key) - User who created banner
- timestamps
```

### **Banner Clicks Table:**
```sql
- id
- banner_id (foreign key)
- user_id (nullable)
- ip_address
- user_agent
- clicked_at
```

---

## 🎯 **Features**

### **1. Banner Creation**
- ✅ Visual banner designer with live preview
- ✅ Multiple banner templates
- ✅ Customizable colors, fonts, and sizes
- ✅ Upload banner images (base64 encoded)
- ✅ Set start and end dates
- ✅ Choose display location
- ✅ Set display order

### **2. Banner Management**
- ✅ View all banners in a grid
- ✅ Status badges (Active, Upcoming, Expired)
- ✅ Edit existing banners
- ✅ Delete banners
- ✅ Track clicks and impressions

### **3. Banner Status**
- **Active** - Currently displaying (between start and end date)
- **Upcoming** - Scheduled for future (before start date)
- **Expired** - Past end date

### **4. Display Locations**
- **home** - Show on home page
- **services** - Show on services page
- **providers** - Show on providers page
- **all** - Show everywhere

---

## 🚀 **How to Use**

### **Access the Page:**
```
URL: http://localhost:8000/banners/banners
Permission Required: manage_banners
```

### **Create a Banner:**

1. **Click "Create New Banner"**
2. **Fill in Details:**
   - Title (e.g., "Summer Sale")
   - Provider Name (optional)
   - Offer Text (e.g., "50% OFF")
   
3. **Choose Template:**
   - Select from available banner templates
   
4. **Customize Styling:**
   - Title: Color, Font, Size
   - Provider: Color, Font, Size
   - Offer: Text Color, Background Color, Font, Size
   
5. **Upload Image:**
   - Click to upload or drag & drop
   - Image is converted to base64
   
6. **Set Schedule:**
   - Start Date: When banner goes live
   - End Date: When banner expires
   
7. **Display Settings:**
   - Location: Where to show banner
   - Order: Display priority (0 = first)
   - Link URL: Where banner clicks go
   
8. **Preview & Save:**
   - See live preview
   - Click "Create Banner"

---

## 📊 **API Endpoints**

### **1. Get All Banners**
```
GET /banners/banners
Response: View with all banners
```

### **2. Create Banner**
```
POST /banners/store
Headers: Content-Type: application/json
Body: {
  "title": "Summer Sale",
  "provider_name": "Glow Salon",
  "offer_text": "50% OFF",
  "banner_template": "template1",
  "title_color": "#000000",
  "title_font": "Arial",
  "title_size": "24px",
  "provider_color": "#666666",
  "provider_font": "Arial",
  "provider_size": "18px",
  "offer_text_color": "#ffffff",
  "offer_bg_color": "#ff0000",
  "offer_font": "Arial",
  "offer_size": "32px",
  "banner_image": "data:image/png;base64,...",
  "link_url": "https://example.com/sale",
  "start_date": "2025-11-09",
  "end_date": "2025-12-31",
  "display_location": "home",
  "display_order": 0
}
```

### **3. Get Banner Details**
```
GET /banners/{id}
Response: {
  "success": true,
  "data": {
    "banner": {...}
  }
}
```

### **4. Delete Banner**
```
DELETE /banners/{id}
Response: {
  "success": true,
  "message": "Banner deleted successfully"
}
```

---

## 🎨 **Banner Templates**

The system supports multiple banner templates with different layouts:

1. **Template 1** - Classic layout
2. **Template 2** - Modern design
3. **Template 3** - Minimal style
4. **Template 4** - Bold design

Each template can be customized with:
- Custom colors
- Custom fonts
- Custom sizes
- Custom images

---

## 📈 **Analytics & Tracking**

### **Metrics Tracked:**
- **Impressions** - How many times banner was shown
- **Clicks** - How many times banner was clicked
- **CTR** - Click-through rate (clicks / impressions)

### **Click Tracking:**
```sql
banner_clicks table stores:
- banner_id
- user_id (if logged in)
- ip_address
- user_agent
- clicked_at timestamp
```

---

## 🔧 **Technical Details**

### **Image Handling:**
- Images uploaded as base64
- Decoded and saved to `storage/app/public/banners/`
- Filename format: `banner_{timestamp}_{uniqid}.{ext}`
- Accessible via: `storage/banners/{filename}`

### **Status Logic:**
```php
$now = now();
if ($now >= $start_date && $now <= $end_date) {
    $status = 'active';
} elseif ($now < $start_date) {
    $status = 'scheduled';
} else {
    $status = 'expired';
}
```

### **Display Order:**
- Lower number = higher priority
- 0 = displayed first
- Banners sorted by display_order ASC

---

## 🎯 **Use Cases**

### **1. Seasonal Promotions**
```
Title: "Winter Sale"
Offer: "40% OFF"
Start: Dec 1, 2025
End: Dec 31, 2025
Location: home
```

### **2. Provider Spotlight**
```
Title: "Featured Provider"
Provider: "Elite Spa"
Offer: "Book Now"
Location: providers
```

### **3. Service Promotion**
```
Title: "New Service Alert"
Offer: "Try Our New Massage"
Location: services
```

### **4. App-Wide Announcement**
```
Title: "Download Our App"
Offer: "Get 10% OFF"
Location: all
```

---

## ✅ **Testing Checklist**

- [ ] Create a banner with all fields
- [ ] Upload an image
- [ ] Set start date (today) and end date (future)
- [ ] Verify status shows "Active"
- [ ] Create a scheduled banner (future start date)
- [ ] Verify status shows "Upcoming"
- [ ] Create an expired banner (past end date)
- [ ] Verify status shows "Expired"
- [ ] Edit a banner
- [ ] Delete a banner
- [ ] Check image is deleted from storage
- [ ] Test different display locations
- [ ] Test display order
- [ ] Track clicks and impressions

---

## 🐛 **Troubleshooting**

### **Banner Not Showing:**
1. Check `is_active` = true
2. Check status = 'active'
3. Check start_date <= today <= end_date
4. Check display_location matches page

### **Image Not Uploading:**
1. Check storage link: `php artisan storage:link`
2. Check permissions on `storage/app/public/banners/`
3. Verify base64 encoding is correct
4. Check file size limits

### **Status Not Updating:**
1. Status is calculated on page load
2. Refresh page to see updated status
3. Check date format (YYYY-MM-DD)

---

## 🔐 **Permissions**

Required permission: `manage_banners`

Users with this permission can:
- View all banners
- Create new banners
- Edit existing banners
- Delete banners
- View analytics

---

## 📝 **Best Practices**

1. **Image Size:** Use 1200x400px for best results
2. **File Format:** PNG or JPG recommended
3. **Start Date:** Set to today or future
4. **End Date:** Must be after start date
5. **Display Order:** Use increments of 10 (0, 10, 20, 30)
6. **Link URL:** Always use full URL with https://
7. **Testing:** Test on different screen sizes
8. **Analytics:** Review click rates regularly

---

## 🚀 **Next Steps**

1. Access: http://localhost:8000/banners/banners
2. Create your first banner
3. Test on different pages
4. Monitor analytics
5. Optimize based on performance

**Your Banners Management System is ready to use!** 🎉
