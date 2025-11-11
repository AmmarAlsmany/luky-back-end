# MyFatoorah Testing Guide

## Overview
This guide explains how to test MyFatoorah payment gateway integration.

---

## 1. Configuration

### Access Payment Settings
Go to: `http://localhost:8000/payment-settings`

### MyFatoorah Configuration Fields

#### Required Fields:
1. **Country**: Saudi Arabia (SA)
2. **Currency**: SAR (﷼)
3. **API Base URL**: 
   - Test: `https://apitest.myfatoorah.com`
   - Live: `https://api.myfatoorah.com`
4. **API Key**: Your MyFatoorah API token
5. **Success URL**: `http://localhost:8000/api/v1/payments/callback/success`
6. **Failure URL**: `http://localhost:8000/api/v1/payments/callback/error`

#### Optional Fields:
- **Merchant Code/ID**: Your merchant identifier
- **Webhook URL**: `http://localhost:8000/api/v1/payments/webhook`
- **Payment Methods**: MADA, Visa/Mastercard, Apple Pay, STC Pay

---

## 2. Test Connection

### Method 1: From Payment Settings Page
1. Go to `/payment-settings`
2. Scroll to "MyFatoorah — Configuration"
3. Click "Test" button
4. Wait for response

**Expected Results:**
- ✅ Success: "MyFatoorah Connection Successful! API is working correctly."
- ❌ Failure: Error message with details

### Method 2: Direct API Call
```bash
curl http://localhost:8000/payments/test-connection
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Connection successful!",
  "data": {
    "success": true,
    "data": [
      {
        "PaymentMethodId": 1,
        "PaymentMethodEn": "KNET",
        "PaymentMethodAr": "كي نت",
        ...
      }
    ]
  }
}
```

---

## 3. Test Payment Flow

### Step 1: Create a Booking
1. Use the mobile app or API to create a booking
2. Select services and provider
3. Proceed to payment

### Step 2: Initiate Payment
**API Endpoint:** `POST /api/v1/payments/initiate`

**Request Body:**
```json
{
  "booking_id": 1,
  "payment_method": "myfatoorah"
}
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "payment_url": "https://apitest.myfatoorah.com/v2/SendPayment/...",
    "invoice_id": "12345",
    "payment_id": "PAY-xxx"
  }
}
```

### Step 3: Complete Payment
1. Open the `payment_url` in browser
2. Select payment method (MADA, Visa, etc.)
3. Enter test card details
4. Complete payment

### Step 4: Verify Callback
- Success: Redirects to success URL
- Failure: Redirects to failure URL
- Check payment status in database

---

## 4. Test Card Details

### For Test Environment

#### MADA Test Cards:
```
Card Number: 5297410000000000
Expiry: 05/21
CVV: 123
```

#### Visa Test Cards:
```
Card Number: 4242424242424242
Expiry: Any future date
CVV: Any 3 digits
```

#### Mastercard Test Cards:
```
Card Number: 5200000000000007
Expiry: Any future date
CVV: Any 3 digits
```

**Note:** Test cards only work with test API keys and test environment.

---

## 5. Verify Payment in Database

### Check Payment Record
```sql
SELECT * FROM payments 
WHERE booking_id = 1 
ORDER BY created_at DESC 
LIMIT 1;
```

**Expected Fields:**
- `payment_id`: MyFatoorah invoice ID
- `amount`: Total payment amount
- `platform_commission`: Commission amount
- `provider_amount`: Provider payout
- `tax_amount`: Tax amount
- `currency`: SAR
- `gateway`: myfatoorah
- `status`: completed/pending/failed
- `gateway_transaction_id`: Transaction reference
- `paid_at`: Payment timestamp

---

## 6. Test Scenarios

### Scenario 1: Successful Payment
1. Create booking
2. Initiate payment
3. Complete with valid card
4. ✅ Payment status = completed
5. ✅ Booking status = confirmed
6. ✅ Receipt generated

### Scenario 2: Failed Payment
1. Create booking
2. Initiate payment
3. Use invalid card or cancel
4. ✅ Payment status = failed
5. ✅ Booking status = pending
6. ✅ Error message shown

### Scenario 3: Pending Payment
1. Create booking
2. Initiate payment
3. Close browser before completing
4. ✅ Payment status = pending
5. ✅ Booking status = pending
6. ✅ Can retry payment

---

## 7. Webhook Testing

### Setup Webhook
1. Configure webhook URL in MyFatoorah dashboard
2. Set URL: `https://yourdomain.com/api/v1/payments/webhook`
3. Enable webhook notifications

### Test Webhook
```bash
# Simulate webhook call
curl -X POST http://localhost:8000/api/v1/payments/webhook \
  -H "Content-Type: application/json" \
  -d '{
    "InvoiceId": "12345",
    "InvoiceStatus": "Paid",
    "InvoiceValue": 100.00,
    "CustomerName": "Test User"
  }'
```

**Expected:**
- Payment status updated
- Booking status updated
- Notifications sent

---

## 8. Common Issues & Solutions

### Issue 1: Connection Test Fails
**Error:** "Failed to fetch payment methods"

**Solutions:**
- ✅ Check API key is correct
- ✅ Verify API URL (test vs live)
- ✅ Ensure internet connection
- ✅ Check MyFatoorah account is active

### Issue 2: Payment Initiation Fails
**Error:** "Invalid API key"

**Solutions:**
- ✅ Regenerate API key from MyFatoorah dashboard
- ✅ Update API key in settings
- ✅ Clear config cache: `php artisan config:clear`

### Issue 3: Callback Not Working
**Error:** "Callback URL not reachable"

**Solutions:**
- ✅ Use public URL (not localhost)
- ✅ Use ngrok for local testing
- ✅ Verify routes are registered
- ✅ Check firewall settings

### Issue 4: Payment Status Not Updating
**Error:** "Webhook not received"

**Solutions:**
- ✅ Configure webhook URL in MyFatoorah
- ✅ Check webhook endpoint is accessible
- ✅ Verify webhook signature validation
- ✅ Check server logs for errors

---

## 9. Testing Checklist

### Pre-Testing
- [ ] MyFatoorah account created
- [ ] API credentials obtained
- [ ] Test environment configured
- [ ] Success/Failure URLs set
- [ ] Payment methods enabled

### Connection Test
- [ ] Test button works
- [ ] API connection successful
- [ ] Payment methods retrieved
- [ ] No errors in console

### Payment Flow Test
- [ ] Booking created successfully
- [ ] Payment initiated
- [ ] Payment URL generated
- [ ] Payment page loads
- [ ] Test card accepted
- [ ] Payment completed
- [ ] Callback received
- [ ] Status updated

### Database Verification
- [ ] Payment record created
- [ ] Commission calculated
- [ ] Tax amount correct
- [ ] Provider amount correct
- [ ] Booking status updated

### Receipt Test
- [ ] Receipt viewable
- [ ] Receipt downloadable
- [ ] All details correct
- [ ] Print works

---

## 10. Production Checklist

### Before Going Live
- [ ] Switch to live API key
- [ ] Update API URL to production
- [ ] Test with real card (small amount)
- [ ] Verify webhook in production
- [ ] Update success/failure URLs to production
- [ ] Enable required payment methods
- [ ] Set up monitoring/alerts
- [ ] Document API credentials securely
- [ ] Train support team
- [ ] Prepare rollback plan

### After Going Live
- [ ] Monitor first transactions
- [ ] Verify callbacks working
- [ ] Check commission calculations
- [ ] Test refund process
- [ ] Monitor error logs
- [ ] Collect user feedback

---

## 11. Support & Resources

### MyFatoorah Resources
- Dashboard: https://portal.myfatoorah.com
- Documentation: https://docs.myfatoorah.com
- Support: support@myfatoorah.com

### Internal Resources
- Payment Settings: `/payment-settings`
- Transactions: `/payments/transactions`
- Commissions: `/payments/commissions`
- Test Connection: `/payments/test-connection`

---

## Quick Test Command

```bash
# Test MyFatoorah connection
php artisan tinker
>>> app(App\Services\MyFatoorahService::class)->getPaymentMethods(100);
```

**Expected Output:**
```php
[
  "success" => true,
  "data" => [
    // Array of payment methods
  ]
]
```

---

## Summary

**To test MyFatoorah:**
1. Configure API credentials in `/payment-settings`
2. Click "Test" button to verify connection
3. Create a test booking via API
4. Complete payment with test card
5. Verify payment status and receipt
6. Check database records

**All tests passing = Ready for production! ✅**
