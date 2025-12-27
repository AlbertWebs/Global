# Changelog

All notable changes to the School Management System will be documented in this file.

## [Unreleased] - 2025-12-24

### Changed
- **Sidebar Menu**: Changed "Payments" menu item to "Record Payment" for better clarity
- **Billing Page Student Selection**: Replaced dropdown with searchable input field
  - Real-time filtering as user types (searches by student name or number)
  - Dropdown shows matching students with full name and student number
  - Selected student displayed as removable tag
  - Much easier to find students when there are many enrolled

### Added

#### Announcements Module - Enhanced
- **File Attachments Support**
  - Teachers can now attach files (PDF, DOCX, Images) to announcements
  - Supported formats: PDF, DOC, DOCX, JPG, JPEG, PNG (max 10MB)
  - Files stored in `storage/app/public/announcement-attachments/`
  - Teachers can view (PDF/Images) and download attachments they've shared
  - Students can download attachments from their portal
  - File type icons displayed for better UX
  - View button for PDF and image files opens in new tab

- **Course & Group Targeting**
  - Teachers can target announcements to specific courses
  - Teachers can target announcements to specific students/groups
  - Searchable dropdowns for course and student selection with real-time filtering
  - Selected courses and students displayed as removable tags
  - Announcements automatically filtered for students based on their course registrations
  - If no specific targeting is set, announcement is visible to all students

- **Edit & Delete Announcements**
  - Teachers can now edit their posted announcements
  - Edit form pre-populated with existing announcement data
  - Can update title, message, target audience, priority, targeting, and attachment
  - Can replace existing attachment with new file
  - Teachers can delete announcements with confirmation dialog
  - Deleted announcements remove associated files from storage

#### Exams & Results - Full Functionality
- **Student Results Viewing**
  - Added `/student-portal/results` route for students to view their results
  - Results page displays published results grouped by academic year and term
  - Shows course, exam type, score, grade, and remarks
  - Color-coded grades (A=green, B=blue, C=yellow, D=orange, F=red)
  - Overall performance summary with statistics
  - Results filtered to show only published results for the logged-in student

- **Results Posting Enhancement**
  - Teachers can post results with automatic grade calculation
  - Results default to "published" status for immediate student visibility
  - Status can be set to pending, published, or archived
  - Posted by field tracks which teacher posted the result
  - **Searchable Student Selection**: Replaced dropdown with searchable input field
    - Real-time filtering as teacher types (searches by student name or number)
    - Dropdown shows matching students with full name and student number
    - Selected student displayed as removable tag
    - Much easier to find students when there are many enrolled
  - **Edit Results**: Teachers can now edit posted results
    - Edit form pre-populated with existing result data
    - Can update all result fields including score, grade, status, and remarks
    - Automatic grade recalculation when score is updated

#### Users & Roles - Verified
- **User Creation & Management**
  - User creation functionality verified and working
  - Role assignment during user creation works correctly
  - Password hashing implemented securely
  - Email uniqueness validation enforced

- **Role Permissions**
  - Role permissions management verified and working
  - Super Admin can assign permissions to roles
  - Permissions grouped by module for better organization
  - Permission changes sync correctly to roles

#### Attendance Management - Full Functionality
- **Attendance Tracking System**
  - Teachers can mark attendance for students in their courses
  - Select course and date to mark attendance
  - Students automatically loaded based on course selection
  - Attendance status options: Present, Absent, Late, Excused
  - Optional notes field for each attendance record
  - Prevents duplicate attendance records for same student, course, and date
  - Attendance records displayed in table format with filtering
  - Search and filter by course functionality
  - Color-coded status badges (Present=green, Absent=red, Late=yellow, Excused=blue)
  - Pagination for attendance records

### Fixed
- **Receipts Index Page Error**: Fixed "Attempt to read property 'full_name' on null" error
  - Added null checks in receipts index view to handle orphaned receipts gracefully
  - Updated controller to filter out receipts with missing payment, student, or course relationships
  - Orphaned receipts (where payment/student/course was deleted) are now excluded from the list
  - View displays "Not Found" messages as a safety net if any orphaned receipts appear

### Added

#### Student Management
- **Education Level Options Updated**
  - Removed "Secondary" option from Level of Education dropdown
  - Kept only: Primary, High School, Diploma, Bachelor, Master, PhD
  - Updated validation to enforce allowed education levels

- **Guardian Information (Previously Next of Kin)**
  - Renamed "Next of Kin" to "Guardian" across all forms and displays
  - Made Guardian Name and Guardian Mobile Number optional fields
  - Updated form validation to allow nullable guardian fields
  - Updated student profile display to show "Guardian" instead of "Next of Kin"

- **Student Photo Upload**
  - Admins can now upload student photos during student creation and editing
  - Photo upload field added to student create and edit forms with preview functionality
  - Photos stored in `storage/app/public/student-photos/` directory
  - Supported formats: JPG, JPEG, PNG (maximum file size: 2MB)
  - Student profile page displays uploaded photo or initials avatar if no photo is uploaded
  - Old photos are automatically deleted when replaced during editing
  - Real-time photo preview before form submission
- **Education Level Options Updated**
  - Removed "Secondary" option from Level of Education dropdown
  - Kept only: Primary, High School, Diploma, Bachelor, Master, PhD
  - Updated validation to enforce allowed education levels

- **Guardian Information (Previously Next of Kin)**
  - Renamed "Next of Kin" to "Guardian" across all forms and displays
  - Made Guardian Name and Guardian Mobile Number optional fields
  - Updated form validation to allow nullable guardian fields
  - Updated student profile display to show "Guardian" instead of "Next of Kin"

#### Data Formatting & Capitalization
- **Automatic Text Capitalization**
  - Implemented automatic Title Case capitalization for names, addresses, and text fields
  - Applied on frontend (JavaScript) - capitalizes text on blur event
  - Applied on backend (PHP) - capitalizes data before saving to database
  - Fields affected: First Name, Middle Name, Last Name, Nationality, Guardian Name, Address
  - Excluded fields: Email, Phone, Mobile, ID/Passport Number, Student Number, Admission Number

#### Receipt Number Serialization
- **Serialized Receipt Numbers**
  - Implemented new receipt number format: GTC-001, GTC-002, GTC-003, etc.
  - Auto-incrementing receipt numbers with zero-padding (3 digits)
  - Ensures no duplicate receipt numbers
  - Persists across system restarts using database storage
  - Applied to all payment receipts automatically

#### Printing & Receipts
- **Thermal Printer Support**
  - Optimized thermal receipt layout for standard 80mm thermal printers
  - Removed unnecessary margins and padding
  - Adjusted font sizes for better readability on thermal paper
  - Set proper page size (80mm width) for thermal printing
  - Improved logo sizing for thermal receipts

- **A5 Print Version**
  - Set receipt print size to A5 format
  - Proper layout for print preview and physical printing
  - Optimized margins and spacing for A5 paper size
  - Screen preview matches A5 dimensions

#### Reports Module
- **New Reports Page**
  - Created dedicated Reports Module page with button-only interface
  - No tables displayed on the page - only action buttons
  - Clean, modern UI with gradient buttons

- **Date Range Selection**
  - Added date range picker to Reports Module page
  - Users can select "From Date" and "To Date" before generating reports
  - Date filtering is optional - leave empty to include all records
  - Date parameters are automatically appended to report URLs
  - "Clear Dates" button to reset date selection
  - All reports support date range filtering where applicable
  - Date filtering applied to: Fee Payments, Expenses, Full Financial, Course Registrations, Bank Deposits, Receipts
  - Students Registered and Balances reports filter by student registration date and payment dates respectively

- **Students Registered Report**
  - New Excel export for all registered students
  - Includes: Admission Number, Student Number, Full Name, Email, Phone, Gender, Date of Birth, Level of Education, Nationality, ID/Passport Number, Guardian Name, Guardian Mobile, Address, Status, Registration Date
  - Automatically downloads as Excel (.xlsx) format
  - Formatted with headers and styling

- **Balances Report**
  - New Excel export for student balances
  - Includes: Student Number, Admission Number, Full Name, Email, Phone, Total Agreed Amount, Total Paid, Outstanding Balance, Number of Payments, Status
  - Automatically downloads as Excel (.xlsx) format
  - Formatted with headers and styling

- **Fee Payment Report**
  - Excel export for all fee payments
  - Includes: Date, Student Name, Student Number, Course, Payment Method, Amount Paid, Agreed Amount, Balance, Receipt Number, Processed By
  - Supports date range filtering via query parameters
  - Automatically downloads as Excel (.xlsx) format

- **Expenses Report**
  - Excel export for all expenses
  - Includes: Expense Date, Description, Category, Amount, Payment Method, Recorded By, Notes
  - Supports date range filtering via query parameters
  - Automatically downloads as Excel (.xlsx) format

- **Full Financial Report**
  - Comprehensive financial report with multiple sheets
  - Includes: Payments, Expenses, Summary statistics, Payment method breakdown
  - Supports date range filtering via query parameters
  - Automatically downloads as Excel (.xlsx) format

- **Course Registrations Report**
  - Excel export for course registrations
  - Includes: Registration Date, Student Number, Student Name, Course Name, Course Code, Academic Year, Month, Year, Status, Notes
  - Supports date range filtering via query parameters
  - Automatically downloads as Excel (.xlsx) format

- **Bank Deposits Report**
  - Excel export for bank deposits
  - Includes: Deposit Date, Source Account, Amount, Reference Number, Recorded By, Notes
  - Supports date range filtering via query parameters
  - Automatically downloads as Excel (.xlsx) format

- **Receipts Report**
  - Excel export for all issued receipts
  - Includes: Receipt Number, Receipt Date, Student Number, Student Name, Course Name, Amount Paid, Agreed Amount, Payment Method, Processed By, Payment Date
  - Supports date range filtering via query parameters
  - Automatically downloads as Excel (.xlsx) format

#### System Settings
- **Dynamic School Name**
  - Sidebar header now displays school name from system settings
  - School name is loaded dynamically from database
  - Changeable without code modifications through Settings page
  - Falls back to "Global College" if not set

#### Notifications System
- **Email Notifications**
  - Created EmailService for handling email notifications
  - Automatic email sent on student registration with welcome message and login credentials
  - Automatic email sent on payment confirmation with receipt details
  - HTML email templates with professional styling
  - Email errors are logged but don't fail the main operation
  - Uses Laravel Mail facade with configurable mail drivers

- **SMS & Email Integration**
  - Both SMS and Email notifications sent automatically on:
    - Student registration/enrollment
    - Payment confirmation
  - Notifications include relevant details (credentials, amounts, receipt numbers)
  - Errors are logged if delivery fails, but don't interrupt the main process
  - Supports multiple SMS providers (Africa's Talking, Twilio, Zettatel, Log)

#### Teacher Management
- **Qualifications Dropdown**
  - Changed Qualifications field from text input to dropdown
  - Dropdown options: Certificate, Diploma, Degree, Masters, PhD
  - Applied to both Teacher creation and edit forms
  - Validation updated to accept only allowed qualification values
  - Created Teacher edit form (was missing)

### Changed

#### Student Forms
- Updated `resources/views/students/create.blade.php`
  - Removed "Secondary" from education level options
  - Changed "Next of Kin" labels to "Guardian"
  - Removed required asterisks from Guardian fields
  - Updated placeholder text to indicate optional fields

- Updated `resources/views/students/edit.blade.php`
  - Removed "Secondary" from education level options
  - Changed "Next of Kin" labels to "Guardian"
  - Removed required validation from Guardian fields

- Updated `resources/views/students/show.blade.php`
  - Changed "Next of Kin" section header to "Guardian"

#### Controllers
- Updated `app/Http/Controllers/StudentController.php`
  - Changed validation rules: `next_of_kin_name` and `next_of_kin_mobile` now nullable
  - Added education level validation with allowed values
  - Added automatic capitalization for text fields before saving
  - Applied to both `store()` and `update()` methods

- Updated `app/Http/Controllers/BillingController.php`
  - Changed receipt number generation to use new serialized format (GTC-001)
  - Uses `Receipt::generateReceiptNumber()` method
  - Added email notification sending on payment confirmation
  - Both SMS and Email sent automatically after payment processing

- Updated `app/Http/Controllers/StudentController.php`
  - Added email notification sending on student enrollment
  - Both SMS and Email sent automatically after student creation

- Updated `app/Http/Controllers/Admin/TeacherController.php`
  - Updated validation for qualification field to accept dropdown values only
  - Validation accepts: Certificate, Diploma, Degree, Masters, PhD
  - Applied to both `store()` and `update()` methods

- Updated `app/Http/Controllers/ReportController.php`
  - Added `module()` method for new Reports Module page
  - Updated `exportStudentsRegistered()` method to accept and use date range parameters
  - Updated `exportBalances()` method to accept and use date range parameters for payment filtering
  - Added `exportCourseRegistrations()` method with date filtering
  - Added `exportBankDeposits()` method with date filtering
  - Added `exportReceipts()` method with date filtering
  - Updated `index()` method route to `financial` for existing financial reports
  - All export methods support optional date range filtering via query parameters
  - Date filtering applied at database query level for optimal performance

#### Models
- Updated `app/Models/Receipt.php`
  - Added `generateReceiptNumber()` static method
  - Implements serialized receipt number generation (GTC-001 format)
  - Handles race conditions and ensures uniqueness

#### Views
- Updated `resources/views/layouts/app.blade.php`
  - Sidebar header now uses dynamic school name: `{{ \App\Models\Setting::get('school_name', 'Global College') }}`
  - Added JavaScript for automatic text capitalization on form inputs
  - Capitalization applied on blur event for text inputs and textareas

- Updated `resources/views/receipts/thermal.blade.php`
  - Optimized for thermal printer width (80mm)
  - Reduced margins and padding
  - Adjusted font sizes for better readability
  - Improved logo sizing constraints

- Updated `resources/views/receipts/print.blade.php`
  - Set page size to A5 format
  - Added A5-specific CSS for print media
  - Optimized layout for A5 paper dimensions

#### Routes
- Updated `routes/web.php`
  - Changed `/reports` route to point to new module page
  - Added `/reports/financial` route for existing financial reports
  - Added `/reports/export-students-registered` route
  - Added `/reports/export-balances` route
  - Added `/reports/export-course-registrations` route
  - Added `/reports/export-bank-deposits` route
  - Added `/reports/export-receipts` route

### New Files Created

- `app/Exports/StudentsRegisteredExport.php`
  - Excel export class for Students Registered Report
  - Includes all student information fields
  - Formatted with headers and styling

- `app/Exports/BalancesExport.php`
  - Excel export class for Balances Report
  - Calculates balances and payment statistics
  - Formatted with headers and styling

- `app/Exports/CourseRegistrationsExport.php`
  - Excel export class for Course Registrations Report
  - Includes student and course information
  - Formatted with headers and styling

- `app/Exports/BankDepositsExport.php`
  - Excel export class for Bank Deposits Report
  - Includes deposit details and source account information
  - Formatted with headers and styling

- `app/Exports/ReceiptsExport.php`
  - Excel export class for Receipts Report
  - Includes receipt and payment details
  - Formatted with headers and styling

- `resources/views/reports/module.blade.php`
  - New Reports Module page with button-only interface
  - Eight report buttons: Students Registered, Balances, Fee Payment, Expenses, Full Financial, Course Registrations, Bank Deposits, Receipts
  - Modern, clean design with gradient buttons
  - Responsive grid layout (1 column mobile, 2 columns tablet, 3 columns desktop)
  - Date range selection section with "From Date" and "To Date" inputs
  - Alpine.js integration for dynamic URL building with date parameters
  - "Clear Dates" functionality to reset date selection
  - All report links dynamically include date parameters when selected

- `app/Services/EmailService.php`
  - New service class for handling email notifications
  - Methods: sendEnrollmentEmail(), sendPaymentEmail(), sendToGuardian()
  - Uses Laravel Mail facade
  - Error handling and logging

- `resources/views/emails/student-enrollment.blade.php`
  - HTML email template for student enrollment confirmation
  - Includes login credentials and portal access link
  - Professional styling with gradient header

- `resources/views/emails/payment-confirmation.blade.php`
  - HTML email template for payment confirmation
  - Includes payment details and receipt number
  - Professional styling with green gradient header

- `resources/views/admin/teachers/edit.blade.php`
  - Created Teacher edit form (was missing)
  - Includes Qualifications dropdown
  - Matches create form structure and styling

### Technical Details

#### Receipt Number Generation
- Format: `GTC-XXX` where XXX is a zero-padded 3-digit number
- Starts from GTC-001
- Auto-increments based on last receipt number in database
- Handles race conditions with uniqueness check loop
- Database-driven, persists across restarts

#### Capitalization Logic
- Frontend: JavaScript function `capitalizeText()` converts text to Title Case
- Backend: PHP `ucwords(strtolower(trim()))` converts text to Title Case
- Applied to: names, addresses, nationality, guardian name
- Excluded from: email, phone numbers, ID numbers, system-generated numbers

#### Email Notifications
- Uses Laravel Mail facade
- HTML email templates with professional styling
- Automatic sending on student registration and payment confirmation
- Error handling: logs errors but doesn't fail main operations
- Configurable via Laravel mail configuration (SMTP, Mailgun, etc.)
- Email templates use Blade templating engine

#### Reports Export
- Uses Maatwebsite Excel package
- Exports as .xlsx format
- Includes formatted headers with styling
- Automatic file download on button click
- Date range filtering via URL query parameters
- Dynamic URL building with Alpine.js
- Date filtering applied at database query level for performance

### Database Changes

- No schema changes required
- Existing `receipts` table supports new receipt number format
- Existing `settings` table used for dynamic school name
- Existing `students` table supports nullable guardian fields (already nullable in migration)

### Migration Notes

- No new migrations required
- Existing migrations already support nullable guardian fields
- Receipt number format change is backward compatible (existing receipts keep their numbers)

### Known Issues / Pending

- Announcements Module - File attachments and group/course targeting needs implementation
- Exams & Results - Full functionality needs to be verified and fixed
- Users & Roles - User creation and role permissions need to be fixed

### Testing Recommendations

1. **Student Management**
   - Test creating student without guardian information
   - Verify education level validation (should reject "Secondary")
   - Verify capitalization works on form submission

2. **Receipt Numbers**
   - Create multiple payments and verify sequential receipt numbers
   - Verify format: GTC-001, GTC-002, etc.
   - Test after system restart to ensure persistence

3. **Reports**
   - Test all 8 report exports:
     - Students Registered Report
     - Balances Report
     - Fee Payment Report
     - Expenses Report
     - Full Financial Report
     - Course Registrations Report
     - Bank Deposits Report
     - Receipts Report
   - Verify Excel files download correctly
   - Verify all data is included in exports
   - Test date range filtering:
     - Select date range and verify reports are filtered correctly
     - Test with only "From Date" selected
     - Test with only "To Date" selected
     - Test with both dates selected
     - Test with no dates (should show all records)
     - Verify "Clear Dates" button works correctly
   - Verify Reports Module page displays all buttons correctly
   - Verify date parameters are correctly appended to report URLs

4. **Printing**
   - Test thermal receipt printing on 80mm thermal printer
   - Test A5 print version on standard printer
   - Verify layout and readability

5. **Settings**
   - Change school name in settings
   - Verify sidebar updates immediately
   - Verify receipt headers use new school name

6. **Notifications**
   - Test email notifications on student registration
   - Test email notifications on payment confirmation
   - Verify emails are sent automatically
   - Check email logs for any delivery failures
   - Verify SMS notifications still work correctly
   - Test with valid and invalid email addresses

7. **Teacher Management**
   - Test creating teacher with Qualifications dropdown
   - Verify all qualification options work (Certificate, Diploma, Degree, Masters, PhD)
   - Test editing teacher and changing qualification
   - Verify validation rejects invalid qualification values

---

## Version History

- **v1.1.0** (2025-12-24) - Current release with all above changes
- **v1.0.0** - Initial release

---

## Notes

- All changes maintain backward compatibility with existing data
- No data migration required for existing records
- Existing receipt numbers remain unchanged (only new receipts use GTC-XXX format)
- Guardian fields can be left empty for new students

