# MasterKey Package Testing Guide

This guide helps you test all the polymorphic functionality of the MasterKey package locally.

## 🚀 Quick Test Run

### Option 1: Automated Tests
```bash
# Run all automated tests
./src/repo/masterkey/run-tests.sh

# Or run specific test files
docker exec newmag_app vendor/bin/phpunit repo/masterkey/tests/Feature/AppAuthTest.php
docker exec newmag_app vendor/bin/phpunit repo/masterkey/tests/Feature/WebLoginTest.php
docker exec newmag_app vendor/bin/phpunit repo/masterkey/tests/Feature/PolymorphicRelationshipTest.php
```

### Option 2: Manual API Testing

## 📱 Testing the API Endpoints

### 1. Request Verification Code
```bash
curl -X POST http://localhost:8000/api/app/request-code \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com"}'
```
**Expected Response:**
```json
{
  "nonce": "random-40-char-string",
  "message": "Verification code sent to your email if it exists."
}
```

### 2. Verify Code and Get Token
```bash
curl -X POST http://localhost:8000/api/app/verify \
  -H "Content-Type: application/json" \
  -d '{"nonce": "YOUR_NONCE_FROM_STEP_1", "code": "123456"}'
```
**Expected Response:**
```json
{
  "token": "60-char-token-string",
  "user": {
    "id": 1,
    "email": "test@example.com"
  }
}
```

### 3. Test Web Login Status
```bash
curl "http://localhost:8000/api/web/status?session_id=test-session-123"
```
**Expected Response:**
```json
{"status": "missing"}
```

### 4. Approve Web Login (Requires Token)
```bash
curl -X POST http://localhost:8000/api/web/approve \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_FROM_STEP_2" \
  -d '{"session_id": "test-session-123"}'
```

## 🔧 Testing Polymorphic Functionality

### Database Inspection
```bash
# Enter the app container
docker exec -it newmag_app bash

# Check the database structure
php artisan tinker
```

In Tinker:
```php
// Check polymorphic token creation
$user = App\Models\User::first();
$token = \Mariojgt\MasterKey\Models\MasterKeyToken::create([
    'tokenable_type' => get_class($user),
    'tokenable_id' => $user->id,
    'token' => 'test-poly-token',
    'name' => 'manual-test'
]);

// Verify polymorphic relationship
$token->tokenable; // Should return the User model
$token->tokenable->email; // Should return user email

// Test different model types (if you have other models)
// $admin = App\Models\Admin::first();
// $adminToken = \Mariojgt\MasterKey\Models\MasterKeyToken::create([
//     'tokenable_type' => get_class($admin),
//     'tokenable_id' => $admin->id,
//     'token' => 'admin-token',
//     'name' => 'admin-test'
// ]);
```

## 🧪 Testing Error Scenarios

### 1. Invalid Email Format
```bash
curl -X POST http://localhost:8000/api/app/request-code \
  -H "Content-Type: application/json" \
  -d '{"email": "invalid-email"}'
# Should return 422 validation error
```

### 2. Invalid Verification Code
```bash
curl -X POST http://localhost:8000/api/app/verify \
  -H "Content-Type: application/json" \
  -d '{"nonce": "valid-nonce", "code": "wrong-code"}'
# Should return 422 "Invalid code"
```

### 3. Expired Token
```bash
# First create an expired token in Tinker:
# $expiredToken = \Mariojgt\MasterKey\Models\MasterKeyToken::create([
#     'tokenable_type' => 'App\Models\User',
#     'tokenable_id' => 1,
#     'token' => 'expired-token',
#     'name' => 'test',
#     'expires_at' => now()->subDay()
# ]);

curl -X POST http://localhost:8000/api/web/approve \
  -H "Authorization: Bearer expired-token" \
  -d '{"session_id": "test"}'
# Should return 401 "Invalid token"
```

### 4. Missing Authorization
```bash
curl -X POST http://localhost:8000/api/web/approve \
  -H "Content-Type: application/json" \
  -d '{"session_id": "test-session"}'
# Should return 401 "Unauthorized"
```

## 📊 Verifying Database Changes

### Check Token Table Structure
```sql
-- In your database client or tinker
DESCRIBE masterkey_tokens;
-- Should show: tokenable_type, tokenable_id (instead of user_id)

DESCRIBE masterkey_sessions;
-- Should show: tokenable_type, tokenable_id (instead of user_id)
```

### Check Migrated Data
```php
// In Tinker - verify existing data was migrated correctly
\Mariojgt\MasterKey\Models\MasterKeyToken::all()->each(function($token) {
    echo "Token: {$token->token} - Type: {$token->tokenable_type} - ID: {$token->tokenable_id}\n";
    echo "Model: " . get_class($token->tokenable) . "\n";
    echo "---\n";
});
```

## 🎯 Test Checklist

- [ ] ✅ Request verification code endpoint works
- [ ] ✅ Email validation works correctly
- [ ] ✅ Code verification creates polymorphic token
- [ ] ✅ Invalid codes are rejected
- [ ] ✅ Tokens authenticate correctly in middleware
- [ ] ✅ Expired tokens are rejected
- [ ] ✅ Web login approval works with polymorphic tokens
- [ ] ✅ Session status checking works
- [ ] ✅ Polymorphic relationships return correct models
- [ ] ✅ Legacy user() method still works
- [ ] ✅ Database migrations completed successfully
- [ ] ✅ Multiple tokens per model work correctly

## 🐛 Common Issues & Solutions

### Issue: Tests fail with "Class not found"
**Solution:** Make sure you're running tests from the project root and migrations are up to date.

### Issue: Token authentication fails
**Solution:** Verify the token exists in the database and check the tokenable relationship.

### Issue: Migration errors
**Solution:** Check if the old columns exist before trying to migrate data.

### Issue: Polymorphic relationship returns null
**Solution:** Verify the tokenable_type and tokenable_id are correctly set and the referenced model exists.

## 🔍 Debugging Tips

1. **Check logs:** `docker exec newmag_app tail -f storage/logs/laravel.log`
2. **Database queries:** Enable query logging in your test environment
3. **Dump variables:** Use `dd()` or `dump()` in your code for debugging
4. **Tinker testing:** Use `php artisan tinker` for quick model testing

## 📝 Creating Additional Tests

To test with different models (like Admin), create them like this:

```php
// In your MasterKeyHandler
private function createOrFindAdmin(string $email)
{
    // Assuming you have an Admin model
    return \App\Models\Admin::firstOrCreate(['email' => $email], [
        'name' => explode('@', $email)[0],
        'password' => bcrypt(\Illuminate\Support\Str::random(16)),
        'role' => 'admin',
    ]);
}

// Then in AFTER_VERIFY hook:
if (str_ends_with($context->email, '@admin.com')) {
    return $this->createOrFindAdmin($context->email);
}
```

This allows testing the polymorphic system with different model types!
