# Student Quiz System - Installation & Usage Guide

## 🚀 New Features Added

### 1. **Quiz System**
- Students can take interactive quizzes
- Multiple choice questions with instant feedback
- Score tracking and performance analytics

### 2. **PDF Generation**
- Download detailed quiz results as PDF
- Includes all questions, answers, and explanations
- Professional formatting for academic records

### 3. **Certificate Generation**
- Automatic certificate generation for passing grades (≥60%)
- Beautiful, printable certificates with student details
- Unique certificate IDs for verification

### 4. **QR Code Integration**
- Personal student QR codes with profile information
- QR codes for quiz results and certificates
- Quick access to student achievements

## 📋 Setup Instructions

### 1. Database Setup
Run this file to create additional tables:
```
http://localhost/Mini%20Project%203ii/PhpGroupProject/setup_quiz_tables.php
```

### 2. Sample Data
The setup automatically creates:
- 2 sample quizzes (PHP Basics & Web Development)
- 10 sample questions (5 for each quiz)
- Sample quiz attempts for testing

## 🎯 How to Use

### For Students:

1. **Login** to the student dashboard
2. **Take Quizzes**: Click "Take Quiz" on available quizzes
3. **View Results**: See detailed results with correct/incorrect answers
4. **Download PDF**: Get a PDF report of your quiz performance
5. **Get Certificate**: Download certificate if you pass (≥60%)
6. **QR Code**: Use the QR code to share your profile/achievements

### Student Dashboard Features:
- **Available Quizzes**: List of quizzes you can take
- **Recent Results**: Your latest quiz performances
- **Performance Summary**: Overall statistics and progress
- **QR Code Section**: Personal QR code with your stats
- **Quick Actions**: Fast access to common tasks

## 📁 New Files Created

### Quiz System Files:
- `views/student/take_quiz.php` - Quiz taking interface
- `views/student/quiz_result.php` - Results display with detailed feedback
- `views/student/generate_pdf.php` - PDF generation for quiz results
- `views/student/generate_certificate.php` - Certificate generation
- `views/student/generate_qr.php` - QR code generation
- `setup_quiz_tables.php` - Database setup script

### Updated Files:
- `views/student/dashboard.php` - Enhanced with quiz functionality

## 🔧 Technical Features

### PDF Generation:
- Uses HTML to PDF conversion
- Fallback to HTML display if PDF tools unavailable
- Professional layout with branding

### QR Code Generation:
- Uses Google Charts API for QR code creation
- Fallback to simple image if API unavailable
- Contains student profile and achievement data

### Database Structure:
```sql
- quizzes (id, title, description, teacher_id, created_at, status)
- questions (id, quiz_id, question_text, options, correct_answer)
- quiz_attempts (id, student_id, quiz_id, score, completed_at, status)
- student_answers (id, attempt_id, question_id, selected_answer, is_correct)
```

## 🎨 User Experience

### Quiz Flow:
1. Student sees available quizzes on dashboard
2. Clicks "Take Quiz" → Goes to quiz page
3. Answers all questions → Submits quiz
4. Redirected to results page with detailed feedback
5. Can download PDF report or certificate

### Dashboard Features:
- **Real-time Statistics**: Quiz count, completion rate, average score
- **Progress Tracking**: Visual progress bars and metrics
- **Quick Actions**: One-click access to take quizzes or view results
- **QR Code Integration**: Personal QR code for easy sharing

## 🚨 Important Notes

### Requirements:
- PHP 7.4+ with PDO extension
- MySQL database
- Modern web browser with JavaScript enabled
- Internet connection for QR code generation (Google Charts API)

### Security Features:
- Session-based authentication
- SQL injection protection with prepared statements
- Role-based access control
- Input validation and sanitization

### Browser Compatibility:
- Chrome, Firefox, Safari, Edge (modern versions)
- Bootstrap 5 for responsive design
- Font Awesome icons for better UI

## 🎓 Sample Usage Scenarios

1. **Student takes PHP quiz**: Gets 4/5 questions correct (80%)
2. **System generates**: Detailed results, PDF report, and certificate
3. **Student downloads**: PDF for records, certificate for portfolio
4. **QR code contains**: Student name, quiz score, certificate status

## 🔄 Future Enhancements (Optional)

- Timer-based quizzes
- Question randomization
- Multiple quiz attempts
- Advanced analytics
- Email notifications
- Mobile app integration

---

## 🎉 Ready to Use!

Your enhanced student dashboard is now ready with:
✅ Interactive quiz system
✅ PDF report generation
✅ Certificate generation
✅ QR code functionality
✅ Real-time performance tracking

**Access the system**: `http://localhost/Mini%20Project%203ii/PhpGroupProject/`

**Test Login**:
- Admin: `admin` / `admin123`
- Teacher: `teacher` / `teacher123`  
- Student: `john_doe` / `student123`
