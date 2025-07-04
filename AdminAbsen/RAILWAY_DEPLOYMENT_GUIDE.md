# RAILWAY DEPLOYMENT GUIDE - ATTENDANCE IMAGES FIX

## MASALAH YANG DIATASI

✅ **Data attendance sudah ada di database Railway**  
❌ **Gambar attendance tidak muncul di website**  
❌ **Error: File not found meskipun ada di storage**

## ROOT CAUSE ANALYSIS

1. **Storage symlink tidak dibuat otomatis di Railway**
2. **Permissions tidak sesuai untuk file access**  
3. **URL generation menggunakan localhost bukan Railway domain**
4. **Directory structure tidak konsisten**

## SOLUSI YANG DIIMPLEMENTASI

### 1. Railway Configuration (`railway.toml`)
```toml
[build]
builder = "nixpacks"
buildCommand = "bash build.sh"

[deploy]
startCommand = "bash start.sh"
```

### 2. Build Script Enhancement (`build.sh`)
- ✅ Create attendance directory
- ✅ Set proper permissions  
- ✅ Run railway-attendance-migration.sh di Railway environment

### 3. Migration Script (`railway-attendance-migration.sh`)
- ✅ Multiple symlink creation methods
- ✅ Directory structure validation
- ✅ Permissions fixing
- ✅ File accessibility testing

### 4. Model Accessors (`app/Models/Attendance.php`)
- ✅ Fallback file checking
- ✅ Railway-compatible URL generation
- ✅ Enhanced error logging

### 5. Environment Configuration (`.env.production`)
```bash
FILESYSTEM_DISK=public
FILAMENT_FILESYSTEM_DISK=public  
ASSET_URL=https://your-app.up.railway.app
```

## DEPLOYMENT STEPS

### Step 1: Push ke GitHub
```bash
git add .
git commit -m "Fix Railway attendance images storage"
git push origin main
```

### Step 2: Railway Auto-Deploy
Railway akan otomatis:
1. Detect push ke GitHub
2. Run `bash build.sh` 
3. Run `bash start.sh`
4. Execute attendance migration

### Step 3: Verify Deployment
Setelah deploy selesai, test endpoints:

**1. Debug Railway configuration:**
```
GET https://your-app.up.railway.app/debug-railway-attendance
```

**2. Test storage system:**
```
GET https://your-app.up.railway.app/test-attendance-images
```

**3. Test direct image access:**
```
GET https://your-app.up.railway.app/storage/attendance/filename.jpg
```

### Step 4: Fix Issues (Jika Ada)

**A. Jika symlink masih broken:**
```bash
# Manual fix via Railway console
rm -f public/storage
ln -sf ../storage/app/public public/storage
```

**B. Jika permissions salah:**
```bash
chmod -R 775 storage/app/public
chmod 644 storage/app/public/attendance/*
```

**C. Jika URL masih salah:**
- Cek ASSET_URL di Railway environment variables
- Pastikan sama dengan domain Railway app

## TESTING CHECKLIST

### ✅ Pre-Deploy Local Test
- [x] Build script runs without errors
- [x] Migration script creates symlinks
- [x] Model accessors return correct URLs
- [x] Routes accessible

### ✅ Post-Deploy Railway Test  
- [ ] `/debug-railway-attendance` returns success
- [ ] `/test-attendance-images` shows files detected
- [ ] Direct image URLs load (tidak 404)
- [ ] Filament attendance pages show images
- [ ] Upload new images works

## MONITORING & TROUBLESHOOTING

### 1. Railway Logs
```bash
railway logs --follow
```
Look for:
- Storage link creation messages
- File permission errors
- 404 errors on /storage/* paths

### 2. Debug Endpoints
Access these URLs setelah deploy:
- `/debug-railway-attendance` - Comprehensive debug info
- `/test-attendance-images` - Attendance-specific tests
- `/test-storage` - General storage tests

### 3. Common Issues & Solutions

**Issue: "Storage symlink not found"**
```bash
# Solution: Manual symlink creation
ln -sf ../storage/app/public public/storage
```

**Issue: "File exists but 404 in browser"**
```bash
# Solution: Check ASSET_URL
# Must be: https://your-app.up.railway.app
```

**Issue: "Permission denied"**
```bash
# Solution: Fix permissions
chmod -R 775 storage/app/public
```

**Issue: "Storage disk not found"**
```bash
# Solution: Check .env Railway variables
FILESYSTEM_DISK=public
FILAMENT_FILESYSTEM_DISK=public
```

## ROLLBACK PLAN

Jika ada masalah serius:

1. **Revert model changes:**
   - Kembalikan accessor ke versi lama
   - Set FILESYSTEM_DISK=local

2. **Disable migration:**
   - Comment out railway-attendance-migration.sh di build.sh
   - Deploy ulang

3. **Manual intervention:**
   - Access Railway console
   - Manual symlink creation
   - Manual file copying jika perlu

## SUCCESS CRITERIA

✅ **Gambar attendance lama (yang sudah ada di DB) bisa dilihat**  
✅ **URL gambar tidak 404**  
✅ **Upload gambar baru works**  
✅ **Filament dashboard shows images properly**  
✅ **Performance tidak menurun**

## NEXT STEPS AFTER SUCCESS

1. **Monitor performance** - Check load times
2. **Test file uploads** - Ensure new uploads work  
3. **Backup verification** - Ensure files persisted
4. **Documentation update** - Update team docs
5. **Remove debug routes** - Clean up debug endpoints jika tidak diperlukan

---

**STATUS**: 🚀 Ready for Railway deployment  
**DEPLOYMENT METHOD**: Git push to GitHub (Railway auto-deploy)  
**ESTIMATED DOWNTIME**: ~2-3 minutes (Railway redeploy)  
**ROLLBACK TIME**: ~1-2 minutes  

**DEPLOYMENT COMMAND**:
```bash
git push origin main
```

Railway akan otomatis detect dan deploy!
