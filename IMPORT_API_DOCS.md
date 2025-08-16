# Import API Documentation

## Overview
This API provides endpoints for importing students and courses data from CSV and Excel files.

## Authentication
All import endpoints require:
- Authentication via Laravel Sanctum (Bearer token)
- Admin privileges
- Email verification

## Base URL
```
{APP_URL}/api/v1/import
```

## Endpoints

### 1. Import Students
**POST** `/import/students`

Import students from CSV/Excel file.

**Headers:**
```
Content-Type: multipart/form-data
Authorization: Bearer {token}
```

**Body:**
- `file` (required): CSV, XLS, or XLSX file (max 10MB)

**CSV Format:**
```csv
student_name,university_id,campus,school,major,semester,year
John Doe,12345,Main Campus,Engineering,Computer Science,Fall 2025,3
```

**Response:**
```json
{
  "success": true,
  "message": "Students imported successfully",
  "data": {
    "successful": 5,
    "failed": 1,
    "errors": ["Row 3: Missing required fields (student_name, university_id)"],
    "total_processed": 6
  }
}
```

### 2. Import Courses
**POST** `/import/courses`

Import courses from CSV/Excel file.

**Headers:**
```
Content-Type: multipart/form-data
Authorization: Bearer {token}
```

**Body:**
- `file` (required): CSV, XLS, or XLSX file (max 10MB)
- `semester` (required): Semester information (string, max 50 chars)

**CSV Format:**
```csv
course_code,course_name,instructor,section,credits,room,schedule,days,time,school
CS101,Introduction to Programming,Dr. Smith,A,3,Room 101,MWF,Monday Wednesday Friday,9:00-10:00,Engineering
```

**Response:**
```json
{
  "success": true,
  "message": "Courses imported successfully",
  "data": {
    "successful": 3,
    "failed": 0,
    "errors": [],
    "semester": "Fall 2025",
    "total_processed": 3
  }
}
```

### 3. Get Template Information
**GET** `/import/template-info`

Get information about required fields and CSV format.

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "students": {
      "required_fields": ["student_name", "university_id"],
      "optional_fields": ["campus", "school", "major", "semester", "year"],
      "field_order": {
        "student_name": "Student Name",
        "university_id": "University ID (unique)",
        "campus": "Campus",
        "school": "School",
        "major": "Major",
        "semester": "Semester",
        "year": "Year"
      },
      "example_csv": "student_name,university_id,campus,school,major,semester,year\nJohn Doe,12345,Main Campus,Engineering,Computer Science,Fall 2025,3"
    },
    "courses": {
      "required_fields": ["course_code", "course_name"],
      "optional_fields": ["instructor", "section", "credits", "room", "schedule", "days", "time", "school"],
      "field_order": {
        "course_code": "Course Code (unique)",
        "course_name": "Course Name",
        "instructor": "Instructor",
        "section": "Section",
        "credits": "Credits",
        "room": "Room",
        "schedule": "Schedule",
        "days": "Days",
        "time": "Time",
        "school": "School"
      },
      "example_csv": "course_code,course_name,instructor,section,credits,room,schedule,days,time,school\nCS101,Introduction to Programming,Dr. Smith,A,3,Room 101,MWF,Monday Wednesday Friday,9:00-10:00,Engineering"
    }
  }
}
```

### 4. Download Student Template
**GET** `/import/template/students`

Download a CSV template for students import.

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
Downloads `students_import_template.csv` file.

### 5. Download Course Template
**GET** `/import/template/courses`

Download a CSV template for courses import.

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
Downloads `courses_import_template.csv` file.

## Error Responses

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "file": ["The file must be a CSV, XLS, or XLSX file."],
    "semester": ["Semester is required."]
  }
}
```

### Authentication Error (401)
```json
{
  "message": "Unauthenticated.",
  "error": "Authentication required"
}
```

### Permission Error (403)
```json
{
  "message": "Access denied",
  "error": "Admin privileges required"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Import failed: Database connection error"
}
```

## Data Validation Rules

### Students
- `student_name`: Required, string
- `university_id`: Required, unique, numeric
- `campus`: Optional, string
- `school`: Optional, string  
- `major`: Optional, string
- `semester`: Optional, string
- `year`: Optional, integer

### Courses
- `course_code`: Required, string (recommended format: 2-4 letters + 3-4 digits)
- `course_name`: Required, string
- `instructor`: Optional, string
- `section`: Optional, string
- `credits`: Optional, integer
- `room`: Optional, string
- `schedule`: Optional, string
- `days`: Optional, string
- `time`: Optional, string
- `school`: Optional, string

## Notes

1. **File Size Limit**: Maximum file size is 10MB
2. **Supported Formats**: CSV, XLS, XLSX
3. **Duplicate Handling**: 
   - Students: Updated based on `university_id`
   - Courses: Updated based on `course_code` + `section` combination
4. **Transaction Safety**: All imports are wrapped in database transactions
5. **CORS**: API is configured to accept requests from React frontend
6. **Rate Limiting**: Standard Laravel rate limiting applies
7. **Empty Rows**: Empty rows in files are automatically skipped
8. **Error Reporting**: Detailed error messages for each failed row

## Frontend Integration

### React Example
```javascript
const importStudents = async (file) => {
  const formData = new FormData();
  formData.append('file', file);
  
  const response = await fetch('/api/v1/import/students', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
    },
    body: formData,
  });
  
  return response.json();
};

const importCourses = async (file, semester) => {
  const formData = new FormData();
  formData.append('file', file);
  formData.append('semester', semester);
  
  const response = await fetch('/api/v1/import/courses', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
    },
    body: formData,
  });
  
  return response.json();
};
```
