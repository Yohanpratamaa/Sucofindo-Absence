# Password Double Hashing Fix

## Problem
When creating a new pegawai (employee), the password was being hashed twice, causing login failures even with the correct password.

## Root Cause
The password was being hashed in two places:
1. **In CreatePegawai::mutateFormDataBeforeCreate()** - The password was hashed using `Hash::make()`
2. **In Pegawai Model's setPasswordAttribute() mutator** - Any value assigned to the password attribute was automatically hashed again

This double hashing resulted in a password hash that could never be verified correctly during login.

## Solution
Fixed the password mutator in the `Pegawai` model to detect if a password is already hashed before applying bcrypt:

```php
// In app/Models/Pegawai.php
public function setPasswordAttribute($value)
{
    if ($value) {
        // Check if password is already hashed (starts with $2y$ for bcrypt)
        if (preg_match('/^\$2[ayb]\$.{56}$/', $value)) {
            // Already hashed, use as-is
            $this->attributes['password'] = $value;
        } else {
            // Plain text, hash it
            $this->attributes['password'] = Hash::make($value);
        }
    }
}
```

Also cleaned up the `UserRoleService::createUserBasedOnRole()` method to avoid any password modification after the initial creation process.

## How to Test
1. Create a new pegawai through Filament admin panel
2. Note the password shown in the success notification
3. Try logging in with that email and password through `/login`
4. Login should now work correctly

## Files Modified
- `app/Models/Pegawai.php` - Fixed password mutator
- `app/Services/UserRoleService.php` - Removed password modification

## Verification
The CreatePegawai class now logs password verification after creation:
```
Log::info("Password verification test: " . ($passwordWorks ? 'SUCCESS' : 'FAILED') . " for user: {$pegawai->email}");
```

Check Laravel logs to confirm password verification is working after user creation.
