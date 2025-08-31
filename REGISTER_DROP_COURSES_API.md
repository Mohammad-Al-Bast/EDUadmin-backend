# Register/Drop Courses Form API Documentation

This documentation describes the new API endpoints for managing register/drop courses forms in the EDU Admin system.

## Database Structure

The system now includes three new tables:

### 1. register_drop_courses_form

Main form table containing student information and general form data.

**Fields:**

-   `form_id` (Primary Key)
-   `student_id` (Foreign Key to students table)
-   `student_name`
-   `campus`
-   `school`
-   `major`
-   `semester`
-   `year`
-   `comments` (nullable)
-   `created_at`, `updated_at`

### 2. register_courses

Table for courses to be registered, linked to the main form.

**Fields:**

-   `register_course_id` (Primary Key)
-   `form_id` (Foreign Key to register_drop_courses_form)
-   `course_code`
-   `course_name`
-   `section`
-   `instructor`
-   `credits`
-   `room`
-   `schedule`
-   `days`
-   `time`
-   `school`
-   `created_at`, `updated_at`

### 3. drop_courses

Table for courses to be dropped, linked to the main form.

**Fields:**

-   `drop_course_id` (Primary Key)
-   `form_id` (Foreign Key to register_drop_courses_form)
-   `course_code`
-   `course_name`
-   `section`
-   `instructor`
-   `credits`
-   `room`
-   `schedule`
-   `days`
-   `time`
-   `school`
-   `created_at`, `updated_at`

## API Endpoints

All endpoints require authentication via Sanctum token.

### GET /api/v1/register-drop-courses

**Description:** Get all register/drop courses forms
**Method:** GET
**Authentication:** Required
**Returns:** List of all forms with related data

### POST /api/v1/register-drop-courses

**Description:** Create a new register/drop courses form
**Method:** POST
**Authentication:** Required

**Request Body Example:**

```json
{
    "student_id": 123,
    "student_name": "John Doe",
    "campus": "Main Campus",
    "school": "Engineering",
    "major": "Computer Science",
    "semester": "Fall",
    "year": 2025,
    "comments": "Optional comments about the form",
    "register_courses": [
        {
            "course_code": "CSC101",
            "course_name": "Introduction to Computer Science",
            "section": "01",
            "instructor": "Dr. Smith",
            "credits": 3,
            "room": "EN101",
            "schedule": "MW",
            "days": "Monday/Wednesday",
            "time": "09:00-10:30",
            "school": "Engineering"
        }
    ],
    "drop_courses": [
        {
            "course_code": "MAT200",
            "course_name": "Calculus II",
            "section": "02",
            "instructor": "Dr. Johnson",
            "credits": 4,
            "room": "MA201",
            "schedule": "TTh",
            "days": "Tuesday/Thursday",
            "time": "11:00-12:30",
            "school": "Arts & Sciences"
        }
    ]
}
```

### GET /api/v1/register-drop-courses/{form}

**Description:** Get a specific form by ID
**Method:** GET
**Authentication:** Required
**Parameters:** `form` - Form ID

### PUT /api/v1/register-drop-courses/{form}

**Description:** Update form information (not courses)
**Method:** PUT
**Authentication:** Required
**Parameters:** `form` - Form ID

**Request Body Example:**

```json
{
    "student_name": "Updated Name",
    "campus": "Updated Campus",
    "comments": "Updated comments"
}
```

### DELETE /api/v1/register-drop-courses/{form}

**Description:** Delete a form and all associated courses
**Method:** DELETE
**Authentication:** Required
**Parameters:** `form` - Form ID

### GET /api/v1/register-drop-courses/student/{studentId}

**Description:** Get all forms for a specific student
**Method:** GET
**Authentication:** Required
**Parameters:** `studentId` - Student ID

## Response Format

All endpoints return JSON responses in the following format:

**Success Response:**

```json
{
    "success": true,
    "data": {
        // Form data with relationships
    },
    "message": "Operation completed successfully"
}
```

**Error Response:**

```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        // Validation errors if applicable
    }
}
```

## Models and Relationships

### RegisterDropCoursesForm

-   `belongsTo` Student
-   `hasMany` RegisterCourse
-   `hasMany` DropCourse

### RegisterCourse

-   `belongsTo` RegisterDropCoursesForm

### DropCourse

-   `belongsTo` RegisterDropCoursesForm

### Student (Updated)

-   `hasMany` RegisterDropCoursesForm

## Usage Examples

### Creating a Form with Courses

```javascript
// Frontend example
const formData = {
    student_id: 12345,
    student_name: "Jane Smith",
    campus: "North Campus",
    school: "Business",
    major: "Business Administration",
    semester: "Spring",
    year: 2025,
    register_courses: [
        {
            course_code: "BUS301",
            course_name: "Marketing Fundamentals",
            section: "01",
            instructor: "Prof. Wilson",
            credits: 3,
            room: "BU205",
            schedule: "MW",
            days: "Monday/Wednesday",
            time: "14:00-15:30",
            school: "Business",
        },
    ],
    drop_courses: [
        {
            course_code: "ACC200",
            course_name: "Accounting Principles",
            section: "03",
            instructor: "Dr. Brown",
            credits: 3,
            room: "BU101",
            schedule: "TTh",
            days: "Tuesday/Thursday",
            time: "10:00-11:30",
            school: "Business",
        },
    ],
    comments: "Need to drop accounting due to schedule conflict",
};

fetch("/api/v1/register-drop-courses", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
        Authorization: "Bearer " + token,
    },
    body: JSON.stringify(formData),
});
```

### Getting Student Forms

```javascript
fetch("/api/v1/register-drop-courses/student/12345", {
    headers: {
        Authorization: "Bearer " + token,
    },
})
    .then((response) => response.json())
    .then((data) => {
        console.log("Student forms:", data.data);
    });
```

## Notes

-   Forms cascade delete - deleting a form will automatically delete all associated register and drop courses
-   Student ID must exist in the students table before creating a form
-   Both register_courses and drop_courses arrays are optional
-   Comments field is optional
-   All course fields are required when adding courses to the arrays
