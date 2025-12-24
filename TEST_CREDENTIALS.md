# Test Credentials - Quick Reference

## 🔐 Admin Account

**Login URL:** `http://localhost:8000/login`

- **Email:** `admin@test.com`
- **Password:** `admin123`
- **Access:** Full system administration

---

## 👨‍🏫 Teacher Account

**Login URL:** `http://localhost:8000/teacher/login`

- **Employee Number:** `EMP-2025-00001`
- **Password:** `EMP-2025-00001`
- **Access:** Teacher portal

---

## 👨‍🎓 Student Account

**Login URL:** `http://localhost:8000/updte`

- **Student Number:** `STU-TEST001`
- **Password:** `STU-TEST001`
- **Access:** Student portal

---

## 📝 Notes

- All passwords are case-sensitive
- Default passwords should be changed after first login
- Test accounts are created automatically when running the seeder
- To recreate test accounts: `php artisan db:seed --class=TestAccountsSeeder`

---

**For detailed user manuals, see:** `USER_MANUAL.md`

