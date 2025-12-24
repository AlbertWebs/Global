# User Manual - Global College Management System

## Table of Contents
1. [Admin User Manual](#admin-user-manual)
2. [Teacher User Manual](#teacher-user-manual)
3. [Student User Manual](#student-user-manual)
4. [Test Credentials](#test-credentials)

---

## Admin User Manual

### Getting Started

**Login URL:** `http://localhost:8000/login`

**First Time Login:**
1. Open your web browser
2. Go to the login URL
3. Enter your email and password
4. Click "Login"

### Main Features

#### Dashboard
- View today's payments
- See monthly revenue
- View student enrollment statistics
- Access financial reports

#### Student Management
**Location:** Sidebar → Students

**Add New Student:**
1. Click "Add Student" button
2. Fill in all required fields (marked with *)
3. Click "Submit"
4. Student will receive welcome SMS with login credentials

**View Student Details:**
1. Go to Students list
2. Click on any student name
3. View payments, courses, and statistics
4. Send welcome SMS manually if needed

**Edit Student:**
1. Go to student detail page
2. Click "Edit" button
3. Update information
4. Click "Save"

#### Teacher Management
**Location:** Sidebar → Admin → Teachers

**Add New Teacher:**
1. Click "Add Teacher" button
2. Fill in personal information
3. Select courses to assign (optional)
4. Click "Save"
5. Teacher will receive welcome SMS automatically

**Assign Courses to Teacher:**
1. Go to teacher detail page
2. Click "Edit"
3. Select courses from dropdown
4. Click "Save"

#### Course Management
**Location:** Sidebar → Courses

**Add New Course:**
1. Click "Add Course"
2. Enter course name and description
3. Set course price
4. Set status (Active/Inactive)
5. Click "Save"

#### Payment Processing
**Location:** Sidebar → Billing

**Process Payment:**
1. Click "Process Payment"
2. Select student
3. Select course
4. Enter amount paid
5. Select payment method (Cash/M-Pesa)
6. Click "Process Payment"
7. Receipt will be generated automatically

#### Financial Reports
**Location:** Sidebar → Reports

**View Reports:**
1. Select date range
2. View summary statistics
3. Export to Excel (click "Export Full Report")
4. Print report (click "Print Report")

#### Bank Deposits
**Location:** Sidebar → Bank Deposits

**Record Bank Deposit:**
1. Click "Add Bank Deposit"
2. Select source account (Cash on Hand or M-Pesa Wallet)
3. Amount will auto-fill with available balance
4. Enter bank account details
5. Click "Save"

#### Expenses
**Location:** Sidebar → Expenses

**Add Expense:**
1. Click "Add Expense"
2. Fill in expense details
3. Select payment method (default: Cash)
4. Click "Save"

#### Settings
**Location:** Sidebar → Settings

**Update School Information:**
1. Enter school name
2. Upload school logo (normal and receipt logo)
3. Enter contact information
4. Click "Save"

**Manage Permissions:**
1. Go to Role Permissions
2. Select a role
3. Check/uncheck permissions
4. Click "Save"

---

## Teacher User Manual

### Getting Started

**Login URL:** `http://localhost:8000/teacher/login`

**First Time Login:**
1. Open your web browser
2. Go to the teacher login URL
3. Enter your Employee Number and Password
4. Click "Login"
5. **Important:** Change your password after first login

### Main Features

#### Dashboard
- View assigned courses
- See total students
- View recent announcements
- Check pending results to post

#### Personal Information
**Location:** Top Menu → Personal Info

- View your profile details
- See assigned courses
- Check employment information

#### My Courses
**Location:** Top Menu → Courses

- View all courses assigned to you
- See course details and schedules

#### Post Results
**Location:** Top Menu → Post Results

**Post Student Results:**
1. Select student
2. Select course
3. Enter academic year and term
4. Enter exam type
5. Enter score (0-100)
6. Grade will be calculated automatically
7. Add remarks (optional)
8. Click "Post Result"

**View Posted Results:**
- See all results you've posted
- Filter by course or student
- Edit or update results

#### Communicate with Students
**Location:** Top Menu → Communicate

**Post Announcement:**
1. Click "New Announcement"
2. Enter title and message
3. Select target audience (All/Students/Parents)
4. Set priority level
5. Click "Post Announcement"

#### Attendance
**Location:** Top Menu → Attendance

- View attendance records
- Mark student attendance
- Generate attendance reports

#### Settings
**Location:** Top Menu → Settings

**Change Password:**
1. Enter current password
2. Enter new password
3. Confirm new password
4. Click "Change Password"

---

## Student User Manual

### Getting Started

**Login URL:** `http://localhost:8000/student/login`

**First Time Login:**
1. Open your web browser
2. Go to the student login URL
3. Enter your Student Number and Password
4. Click "Login"
5. **Important:** Change your password after first login

**Default Password:** Your Student Number (you'll receive this via SMS)

### Main Features

#### Dashboard
- View payment statistics
- See total paid amount
- Check outstanding balance
- View registered courses count

#### Financial Info
**Location:** Top Menu → Financial Info

**View Payments:**
- See all your payment history
- View payment dates and amounts
- Download receipts (click "View Receipt")

**View Receipts:**
1. Click "View Receipt" next to any payment
2. Choose receipt format:
   - Normal Print
   - Black & White Print
   - Thermal Print
3. Print or download receipt

#### My Courses
**Location:** Top Menu → Courses

- View all registered courses
- See course details
- Check registration dates
- View course status

#### Results
**Location:** Top Menu → Results

- View all published results
- See scores and grades
- Filter by academic year and term
- View teacher remarks

#### Settings
**Location:** Top Menu → Settings

**View Profile:**
- See personal information
- Check student number
- View admission number
- See account status

**Change Password:**
1. Enter current password (default: your Student Number)
2. Enter new password
3. Confirm new password
4. Click "Change Password"

---

## Test Credentials

### Admin Account
- **Login URL:** `http://localhost:8000/login`
- **Email:** `admin@test.com`
- **Password:** `admin123`
- **Access Level:** Full system access

### Teacher Account
- **Login URL:** `http://localhost:8000/teacher/login`
- **Employee Number:** `EMP-2025-00001`
- **Password:** `EMP-2025-00001`
- **Access Level:** Teacher portal access

### Student Account
- **Login URL:** `http://localhost:8000/student/login`
- **Student Number:** `STU-TEST001`
- **Password:** `STU-TEST001`
- **Access Level:** Student portal access

---

## Quick Tips

### For Admins
- Always verify student information before processing payments
- Use the search function to quickly find students or teachers
- Export reports regularly for record keeping
- Keep school settings updated

### For Teachers
- Post results promptly after exams
- Use announcements to communicate important information
- Keep your password secure
- Contact admin if you need course assignments changed

### For Students
- Check your financial info regularly
- Download receipts for your records
- Change your password immediately after first login
- Contact admin if you have payment issues

---

## Support

If you encounter any issues:
1. Check this manual first
2. Contact your system administrator
3. Ensure you're using the correct login URL
4. Verify your credentials are correct

---

## Security Notes

- **Never share your password** with anyone
- Change your password regularly
- Log out when finished using the system
- Use strong passwords (mix of letters, numbers, and symbols)
- Report any suspicious activity immediately

---

**Last Updated:** December 2025
**System Version:** 1.0

