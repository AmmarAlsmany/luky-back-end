# 📊 Reports & Analytics System - Complete Guide

## 📋 **Overview**

The Reports & Analytics System provides comprehensive business intelligence and data visualization for your platform. Track revenue, bookings, providers, clients, and commissions with interactive charts and exportable data.

---

## 🎯 **Features**

### **1. Dashboard Summary Statistics**
- ✅ Total Revenue
- ✅ Total Bookings
- ✅ Active Providers
- ✅ Total Clients
- ✅ Today's Revenue
- ✅ Today's Bookings
- ✅ This Month Revenue
- ✅ Last Month Revenue

### **2. Revenue Analysis**
- ✅ Revenue by Day
- ✅ Revenue by Payment Method
- ✅ Total Commission
- ✅ Total Discounts
- ✅ Revenue Trends
- ✅ Custom Date Range

### **3. Booking Statistics**
- ✅ Bookings by Status
- ✅ Bookings by Day
- ✅ Average Booking Value
- ✅ Completion Rate
- ✅ Cancellation Rate
- ✅ Total vs Completed vs Cancelled

### **4. Provider Performance**
- ✅ Top Providers by Revenue
- ✅ Provider Ratings
- ✅ Total Bookings per Provider
- ✅ Average Booking Value
- ✅ Commission Generated

### **5. Client Spending**
- ✅ Top Spending Clients
- ✅ New vs Returning Clients
- ✅ Average Spent per Booking
- ✅ Client Lifetime Value

### **6. Commission Reports**
- ✅ Commission by Provider
- ✅ Commission by Day
- ✅ Total Commission
- ✅ Commission Rate
- ✅ Revenue vs Commission

---

## 🗂️ **Report Types**

### **1. Revenue Analysis**
```
URL: /reports/revenue
Method: GET
Parameters:
- period: day, week, month, year
- start_date: YYYY-MM-DD
- end_date: YYYY-MM-DD

Response:
{
  "revenue_by_day": [...],
  "revenue_by_method": [...],
  "total_commission": 5000,
  "total_discounts": 500,
  "total_revenue": 50000,
  "total_transactions": 150
}
```

### **2. Booking Statistics**
```
URL: /reports/bookings
Method: GET
Parameters:
- start_date: YYYY-MM-DD
- end_date: YYYY-MM-DD

Response:
{
  "bookings_by_status": [...],
  "bookings_by_day": [...],
  "avg_booking_value": 333.33,
  "completion_rate": 85.5,
  "cancellation_rate": 10.2,
  "total_bookings": 150
}
```

### **3. Provider Performance**
```
URL: /reports/providers
Method: GET
Parameters:
- start_date: YYYY-MM-DD
- end_date: YYYY-MM-DD
- limit: 10 (default)

Response:
{
  "top_providers": [...],
  "provider_ratings": [...]
}
```

### **4. Client Spending**
```
URL: /reports/clients
Method: GET
Parameters:
- start_date: YYYY-MM-DD
- end_date: YYYY-MM-DD
- limit: 10 (default)

Response:
{
  "top_clients": [...],
  "new_clients": 25,
  "returning_clients": 75
}
```

### **5. Commission Report**
```
URL: /reports/commission
Method: GET
Parameters:
- start_date: YYYY-MM-DD
- end_date: YYYY-MM-DD

Response:
{
  "commission_by_provider": [...],
  "commission_by_day": [...],
  "total_commission": 5000,
  "total_revenue": 50000,
  "commission_rate": 10
}
```

---

## 📊 **Dashboard Metrics**

### **Summary Cards:**

#### **Total Revenue**
```
Display: SAR 150,000
Calculation: SUM(payments.amount) WHERE status = 'completed'
Trend: +15% from last month
```

#### **Total Bookings**
```
Display: 1,234
Calculation: COUNT(bookings)
Trend: +8% from last month
```

#### **Active Providers**
```
Display: 45
Calculation: COUNT(service_providers) WHERE is_active = true
Trend: +3 new this month
```

#### **Total Clients**
```
Display: 567
Calculation: COUNT(users) WHERE user_type = 'client'
Trend: +12% from last month
```

---

## 📈 **Chart Types**

### **1. Line Charts**
- Revenue over time
- Bookings over time
- Commission trends

### **2. Bar Charts**
- Revenue by payment method
- Bookings by status
- Top providers

### **3. Pie Charts**
- Payment method distribution
- Booking status breakdown
- Provider performance

### **4. Area Charts**
- Revenue trends
- Booking trends
- Growth analysis

---

## 🎨 **UI Components**

### **Filter Section:**
```html
- Report Type Dropdown
- Time Period Selector
- Date Range Picker
- Generate Button
- Export Button
```

### **Metrics Cards:**
```html
- Icon
- Value (large number)
- Label
- Trend indicator (↑ ↓ →)
- Percentage change
```

### **Chart Containers:**
```html
- Chart Title
- Chart Canvas
- Legend
- Tooltips
- Download Button
```

---

## 🚀 **How to Use**

### **Access the Reports Page:**
```
URL: http://localhost:8000/reports/reports
Permission Required: view_reports
```

### **Generate a Report:**

1. **Select Report Type:**
   - Revenue Analysis
   - Orders Report
   - Users Report
   - Providers Report
   - Activities Report

2. **Choose Time Period:**
   - Daily
   - Weekly
   - Monthly
   - Yearly
   - Custom Range

3. **Set Date Range:**
   - Start Date
   - End Date

4. **Click "Generate Report"**

5. **View Results:**
   - Charts
   - Tables
   - Statistics

6. **Export (Optional):**
   - CSV
   - Excel
   - PDF

---

## 💡 **Use Cases**

### **1. Monthly Revenue Review**
```
Report Type: Revenue Analysis
Period: Monthly
Date: Last Month
Purpose: Review monthly performance
```

### **2. Provider Performance Evaluation**
```
Report Type: Providers Report
Period: Quarterly
Date: Last 3 Months
Purpose: Identify top performers
```

### **3. Client Retention Analysis**
```
Report Type: Users Report
Period: Yearly
Date: This Year
Purpose: Track new vs returning clients
```

### **4. Commission Reconciliation**
```
Report Type: Commission Report
Period: Monthly
Date: Last Month
Purpose: Calculate provider payouts
```

### **5. Business Growth Tracking**
```
Report Type: Revenue Analysis
Period: Yearly
Date: Year to Date
Purpose: Track annual growth
```

---

## 📊 **Key Metrics Explained**

### **Completion Rate:**
```
Formula: (Completed Bookings / Total Bookings) × 100
Example: (85 / 100) × 100 = 85%
Meaning: 85% of bookings are successfully completed
```

### **Cancellation Rate:**
```
Formula: (Cancelled Bookings / Total Bookings) × 100
Example: (10 / 100) × 100 = 10%
Meaning: 10% of bookings are cancelled
```

### **Average Booking Value:**
```
Formula: Total Revenue / Total Bookings
Example: 50,000 / 150 = 333.33 SAR
Meaning: Average booking is worth 333.33 SAR
```

### **Commission Rate:**
```
Formula: (Total Commission / Total Revenue) × 100
Example: (5,000 / 50,000) × 100 = 10%
Meaning: Platform takes 10% commission
```

### **Client Lifetime Value (CLV):**
```
Formula: Total Spent by Client / Number of Bookings
Example: 5,000 / 10 = 500 SAR
Meaning: Client spends 500 SAR per booking on average
```

---

## 🔧 **Technical Implementation**

### **Controller Methods:**

```php
ReportController:
- index() - Display reports page
- revenueReport() - Get revenue data
- bookingStats() - Get booking statistics
- providerPerformance() - Get provider metrics
- clientSpending() - Get client data
- commissionReport() - Get commission data
- export() - Export reports
```

### **Database Queries:**

```sql
-- Revenue by Day
SELECT DATE(created_at) as date, 
       SUM(amount) as revenue, 
       COUNT(*) as transactions
FROM payments
WHERE status = 'completed'
  AND created_at BETWEEN ? AND ?
GROUP BY date
ORDER BY date;

-- Top Providers
SELECT sp.id, sp.business_name,
       COUNT(b.id) as total_bookings,
       SUM(b.total_amount) as total_revenue,
       SUM(b.commission_amount) as total_commission
FROM bookings b
JOIN service_providers sp ON b.provider_id = sp.id
WHERE b.status = 'completed'
  AND b.created_at BETWEEN ? AND ?
GROUP BY sp.id, sp.business_name
ORDER BY total_revenue DESC
LIMIT 10;
```

---

## 📥 **Export Functionality**

### **Supported Formats:**
- ✅ CSV (Comma Separated Values)
- ✅ Excel (XLSX)
- ✅ PDF (Portable Document Format)

### **Export Options:**
```javascript
// Export current report
exportReport('csv'); // Downloads CSV file
exportReport('excel'); // Downloads Excel file
exportReport('pdf'); // Downloads PDF file
```

---

## 🎯 **Best Practices**

1. **Regular Monitoring:**
   - Check daily revenue
   - Monitor booking trends
   - Track provider performance

2. **Monthly Reviews:**
   - Generate monthly reports
   - Compare with previous months
   - Identify growth opportunities

3. **Quarterly Analysis:**
   - Deep dive into metrics
   - Provider performance reviews
   - Client retention analysis

4. **Annual Planning:**
   - Year-end reports
   - Growth projections
   - Budget planning

---

## 🐛 **Troubleshooting**

### **No Data Showing:**
1. Check date range
2. Verify database has data
3. Check permissions
4. Clear cache

### **Charts Not Loading:**
1. Check JavaScript console
2. Verify Chart.js loaded
3. Check API responses
4. Refresh page

### **Export Not Working:**
1. Check export permissions
2. Verify file path writable
3. Check disk space
4. Try different format

---

## 🔐 **Permissions**

Required permissions:
- `view_reports` - View reports page
- `export_reports` - Export reports to files

---

## 📝 **Summary**

The Reports & Analytics System provides:
- ✅ Real-time business metrics
- ✅ Interactive charts and graphs
- ✅ Customizable date ranges
- ✅ Multiple report types
- ✅ Export capabilities
- ✅ Provider performance tracking
- ✅ Client spending analysis
- ✅ Commission reconciliation

**Your complete business intelligence solution! 📊**
