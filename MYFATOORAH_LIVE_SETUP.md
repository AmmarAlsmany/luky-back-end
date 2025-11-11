# MyFatoorah Live Setup Guide

## 🌍 API URLs by Country

### Saudi Arabia (Your Target Market)
```
Live URL: https://api-sa.myfatoorah.com/
Test URL: https://apitest.myfatoorah.com/
```

### Other Countries
```
Kuwait, UAE, Bahrain, Jordan, Oman: https://api.myfatoorah.com/
Qatar: https://api-qa.myfatoorah.com/
Egypt: https://api-eg.myfatoorah.com/
```

---

## 🔑 Getting Your Live API Key

### Steps:
1. **Login** to MyFatoorah Portal
   - URL: https://portal.myfatoorah.com/
   - Use your Super Master Account

2. **Navigate to Integration Settings**
   - Click "Integration Settings" from left menu
   - Select "API Key" from dropdown

3. **Generate API Key**
   - Press "Create" button
   - Click "Copy" icon (lower right corner)
   - Save the key securely

4. **Update Your .env File**
   ```env
   MYFATOORAH_API_KEY=your_live_key_here
   MYFATOORAH_API_URL=https://api-sa.myfatoorah.com
   ```

5. **Clear Config Cache**
   ```bash
   php artisan config:clear
   ```

---

## ⚠️ Critical Warnings

### 1. Don't Disable Token Creator
- **Never** disable the user who created the API token
- Disabling the user will **disable the token**
- Keep the creator account active at all times

### 2. Token Expiration
- API tokens expire every **5 years**
- Set a reminder to regenerate before expiry
- Plan for token rotation in production

### 3. Token Regeneration
- Generating a new token **expires the old one immediately**
- Update all systems before regenerating
- Have a rollback plan ready

### 4. Multi-Country Accounts
- Each country has a **different API token**
- Use the correct token for each country
- Allows customers to pay in local currency

---

## 🔄 Migration from Test to Live

### Current Configuration (Test Mode)
```env
MYFATOORAH_API_KEY=rLtt6JWvbUHDDhsZnfpA... (Test Token)
MYFATOORAH_API_URL=https://apitest.myfatoorah.com
```

### Live Configuration (Production)
```env
MYFATOORAH_API_KEY=your_live_token_from_portal
MYFATOORAH_API_URL=https://api-sa.myfatoorah.com
MYFATOORAH_SUCCESS_URL=https://yourdomain.com/api/v1/payments/callback/success
MYFATOORAH_ERROR_URL=https://yourdomain.com/api/v1/payments/callback/error
MYFATOORAH_WEBHOOK_SECRET=your_webhook_secret
```

### Migration Checklist
- [ ] Get live API key from portal
- [ ] Update MYFATOORAH_API_KEY in .env
- [ ] Change API URL to https://api-sa.myfatoorah.com
- [ ] Update callback URLs to production domain
- [ ] Clear config cache
- [ ] Test connection
- [ ] Test small payment (1 SAR)
- [ ] Verify webhook working
- [ ] Monitor first transactions
- [ ] Document live credentials securely

---

## 🧪 Testing Before Going Live

### 1. Test Environment
```bash
# Current test setup
php test-myfatoorah-simple.php

# Should show:
✓ SUCCESS!
Connection: Working
```

### 2. Test Payment Flow
```bash
# Create test booking
# Initiate payment
# Use test cards:
MADA: 5297410000000000 | Exp: 05/21 | CVV: 123
Visa: 4242424242424242 | Exp: Any future | CVV: Any
```

### 3. Verify Database
```sql
SELECT * FROM payments 
WHERE status = 'completed' 
ORDER BY created_at DESC 
LIMIT 5;
```

### 4. Test Webhooks
```bash
# Check webhook endpoint is accessible
curl -X POST https://yourdomain.com/api/v1/payments/webhook
```

---

## 🚀 Going Live Checklist

### Pre-Launch
- [ ] MyFatoorah account verified and approved
- [ ] Business documents submitted
- [ ] Bank account linked
- [ ] Live API key obtained
- [ ] SSL certificate installed on domain
- [ ] Callback URLs configured
- [ ] Webhook endpoint secured
- [ ] Error logging enabled
- [ ] Monitoring alerts set up

### Configuration
- [ ] Update .env with live credentials
- [ ] Change API URL to live endpoint
- [ ] Update callback URLs to production
- [ ] Set webhook secret
- [ ] Clear all caches
- [ ] Restart application

### Testing
- [ ] Test connection to live API
- [ ] Process test payment (1 SAR)
- [ ] Verify payment in MyFatoorah portal
- [ ] Check database records
- [ ] Test refund process
- [ ] Verify webhook delivery
- [ ] Test error scenarios

### Monitoring
- [ ] Set up payment monitoring
- [ ] Configure failure alerts
- [ ] Monitor transaction logs
- [ ] Track commission calculations
- [ ] Review daily reconciliation

---

## 📊 Currency Handling

### Test Mode
- All transactions in **KWD** (Kuwaiti Dinar)
- Amounts converted automatically

### Live Mode
- Transactions in your portal currency
- For Saudi Arabia: **SAR** (Saudi Riyal)
- Real exchange rates applied

---

## 🔐 Security Best Practices

### API Key Management
1. **Never commit** API keys to version control
2. **Use environment variables** (.env file)
3. **Rotate keys** every 6-12 months
4. **Restrict access** to production keys
5. **Monitor usage** for suspicious activity

### Webhook Security
1. **Validate webhook signature**
2. **Use HTTPS only**
3. **Verify source IP** (MyFatoorah IPs)
4. **Log all webhook calls**
5. **Implement replay protection**

---

## 🆘 Troubleshooting

### Connection Failed
```
Error: Failed to connect to API

Solutions:
1. Check API key is correct
2. Verify API URL matches country
3. Ensure internet connection
4. Check firewall settings
5. Verify SSL certificates
```

### Invalid Token
```
Error: Invalid API token

Solutions:
1. Regenerate token in portal
2. Update .env file
3. Clear config cache
4. Restart application
```

### Payment Failed
```
Error: Payment initiation failed

Solutions:
1. Check invoice amount > 0
2. Verify currency code (SAR)
3. Ensure callback URLs accessible
4. Check MyFatoorah account status
5. Review error logs
```

---

## 📞 Support

### MyFatoorah Support
- **Email**: [email protected]
- **Portal**: https://portal.myfatoorah.com/
- **Docs**: https://docs.myfatoorah.com/

### Technical Support
- **API Issues**: Check documentation first
- **Integration Help**: Contact MyFatoorah technical team
- **Account Issues**: Contact account manager

---

## 📝 Notes

### Important Reminders
1. Test thoroughly before going live
2. Keep backup of working configuration
3. Document all changes
4. Monitor first week closely
5. Have rollback plan ready

### Token Expiry Reminder
- **Set calendar reminder** for 4.5 years from now
- Plan token rotation before expiry
- Test new token before switching
- Keep old token active during transition

---

## ✅ Current Status

**Environment**: Test/Sandbox
**API URL**: https://apitest.myfatoorah.com
**Connection**: ✓ Working
**Ready for**: Development and Testing

**Next Step**: Get live API key when ready for production
