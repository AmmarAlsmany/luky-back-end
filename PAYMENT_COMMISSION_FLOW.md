# Payment & Commission Flow

## Overview
This document explains how service pricing, payments, and commission calculations work in the Luky platform.

---

## 1. Provider Sets Service Price

### When Creating/Editing a Service:
The provider enters:
- **Base Price** (e.g., 100 SAR) - Price at provider location
- **Home Service Price** (e.g., 150 SAR) - Optional, if service available at client's home
- **Duration** (e.g., 60 minutes)

**Example:**
```
Service: Haircut
Base Price: 100 SAR (at salon)
Home Service Price: 150 SAR (at client's home)
```

---

## 2. Client Books Service

### Booking Calculation Flow:

#### Step 1: Calculate Subtotal
```
Subtotal = Sum of all service prices
```

**Example:**
```
Service 1: Haircut = 100 SAR
Service 2: Beard Trim = 50 SAR
Subtotal = 150 SAR
```

#### Step 2: Apply Promo Code (if any)
```
Discount = Calculate based on promo code type
Amount After Discount = Subtotal - Discount
```

**Example:**
```
Promo Code: SUMMER20 (20% off)
Discount = 150 × 20% = 30 SAR
Amount After Discount = 150 - 30 = 120 SAR
```

#### Step 3: Calculate Tax (VAT)
```
Tax Rate = 15% (configurable)
Tax Amount = Amount After Discount × 15%
```

**Example:**
```
Tax Amount = 120 × 15% = 18 SAR
```

#### Step 4: Calculate Total Amount (What Client Pays)
```
Total Amount = Amount After Discount + Tax Amount
```

**Example:**
```
Total Amount = 120 + 18 = 138 SAR
```

#### Step 5: Calculate Platform Commission
```
Commission Rate = Provider's commission rate (e.g., 10%)
Commission Amount = Amount After Discount × Commission Rate
```

**Example:**
```
Commission Rate = 10%
Commission Amount = 120 × 10% = 12 SAR
```

#### Step 6: Calculate Provider Payout
```
Provider Amount = Amount After Discount - Commission Amount
```

**Example:**
```
Provider Amount = 120 - 12 = 108 SAR
```

---

## 3. Complete Booking Example

### Scenario:
- **Service**: Haircut (100 SAR) + Beard Trim (50 SAR)
- **Location**: At client's home (home service price applies)
- **Promo Code**: SUMMER20 (20% discount)
- **Provider Commission Rate**: 10%
- **Tax Rate**: 15%

### Calculation:

| Step | Description | Amount |
|------|-------------|--------|
| 1 | Haircut (home service) | 150 SAR |
| 2 | Beard Trim (home service) | 75 SAR |
| **Subtotal** | | **225 SAR** |
| 3 | Promo Discount (20%) | -45 SAR |
| **After Discount** | | **180 SAR** |
| 4 | Tax (15%) | +27 SAR |
| **Total (Client Pays)** | | **207 SAR** |
| | | |
| **Platform Commission (10%)** | | **18 SAR** |
| **Provider Receives** | | **162 SAR** |

---

## 4. Payment Record in Database

When payment is completed, the system stores:

```php
Payment {
    booking_id: 123
    amount: 207.00              // Total amount client paid
    platform_commission: 18.00  // Platform's commission
    provider_amount: 162.00     // Provider's payout
    tax_amount: 27.00           // Tax amount
    currency: 'SAR'
    status: 'completed'
    gateway: 'myfatoorah'
}
```

---

## 5. Commission Rates

### Setting Commission Rates:

#### Per Provider:
Admins can set different commission rates for each provider:
- Go to: Provider Details → Payment Settings
- Set commission rate (e.g., 10%, 15%, 20%)
- Default: 15%

#### Global Default:
Set in: `/payment-settings` → Commission Settings

---

## 6. Commission Tracking

### Admin Dashboard Shows:

#### Transaction View (`/payments/transactions`):
- All payments with amounts
- Status (pending, completed, failed)
- Gateway information
- Export to CSV

#### Commission View (`/payments/commissions`):
- **Per Provider Breakdown:**
  - Gross Revenue (total bookings)
  - Platform Commission (total earned)
  - Net Payout (amount to provider)
  - Transaction Count

- **Summary Statistics:**
  - Total Commission Earned
  - This Month Commission
  - Pending Payouts
  - Average Commission Rate

---

## 7. Key Points

### For Providers:
✅ **You set the service price** - This is what you want to earn
✅ **Client pays more** - Due to tax (15%)
✅ **Platform takes commission** - From your base price (before tax)
✅ **You receive** - Your price minus commission

### For Platform:
✅ **Commission is calculated** - On the discounted amount (after promo)
✅ **Tax is separate** - Goes to government, not included in commission
✅ **Flexible rates** - Different providers can have different commission rates

### For Clients:
✅ **See total price** - Including tax
✅ **Promo codes reduce** - The base amount (before tax)
✅ **Pay once** - Through MyFatoorah gateway

---

## 8. Example Code

### In Booking Controller:
```php
// Calculate commission
$amountAfterDiscount = $subtotal - $discountAmount;
$taxAmount = $amountAfterDiscount * 0.15; // 15% VAT
$totalAmount = $amountAfterDiscount + $taxAmount;
$commissionAmount = $amountAfterDiscount * ($provider->commission_rate / 100);
$providerAmount = $amountAfterDiscount - $commissionAmount;

// Store in booking
$booking->commission_amount = $commissionAmount;
$booking->total_amount = $totalAmount;
$booking->tax_amount = $taxAmount;
```

### In Payment Record:
```php
Payment::create([
    'booking_id' => $booking->id,
    'amount' => $totalAmount,
    'platform_commission' => $commissionAmount,
    'provider_amount' => $providerAmount,
    'tax_amount' => $taxAmount,
    'status' => 'completed',
]);
```

---

## 9. Commission Reports

### Available Reports:
1. **Transaction History** - All payments with details
2. **Commission Summary** - Total earnings by period
3. **Provider Breakdown** - Commission per provider
4. **Export to CSV** - For accounting

### Access:
- `/payments/transactions` - Transaction tracking
- `/payments/commissions` - Commission reports
- `/payments/export` - Export data

---

## Summary

**Flow:**
1. Provider sets service price (e.g., 100 SAR)
2. Client books and pays total (price + tax)
3. Platform calculates commission (e.g., 10% = 10 SAR)
4. Provider receives (100 - 10 = 90 SAR)
5. Platform keeps commission (10 SAR)
6. Tax goes to government (15 SAR)

**Formula:**
```
Client Pays = (Service Price - Discount) + Tax
Platform Gets = (Service Price - Discount) × Commission Rate
Provider Gets = (Service Price - Discount) - Platform Commission
```
