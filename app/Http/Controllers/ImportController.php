<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Http\Requests\ImportStudentsRequest;
use App\Http\Requests\ImportCoursesRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Shuchkin\SimpleXLSX;

class ImportController extends Controller
{
    /**
     * Import students from CSV/Excel file
     */
    public function importStudents(ImportStudentsRequest $request): JsonResponse
    {
        try {
            $file = $request->file('file');
            $fileExtension = $file->getClientOriginalExtension();
            
            // Parse the file based on extension
            $data = $this->parseFile($file, $fileExtension);
            
            if (!$data || count($data) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'File is empty or invalid'
                ], 400);
            }

            // Process students data
            $result = $this->processStudentsData($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Students imported successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import courses from CSV/Excel file
     */
    public function importCourses(ImportCoursesRequest $request): JsonResponse
    {
        try {
            $file = $request->file('file');
            $semester = $request->input('semester');
            $fileExtension = $file->getClientOriginalExtension();
            
            // Parse the file based on extension
            $data = $this->parseFile($file, $fileExtension);
            
            if (!$data || count($data) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'File is empty or invalid'
                ], 400);
            }

            // Process courses data
            $result = $this->processCoursesData($data, $semester);
            
            return response()->json([
                'success' => true,
                'message' => 'Courses imported successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parse file based on extension
     */
    private function parseFile($file, string $fileExtension): array
    {
        $filePath = $file->getRealPath();
        
        switch (strtolower($fileExtension)) {
            case 'csv':
                return $this->parseCsv($filePath);
            case 'xlsx':
            case 'xls':
                return $this->parseExcel($filePath);
            default:
                throw new \Exception('Unsupported file format');
        }
    }

    /**
     * Parse CSV file
     */
    private function parseCsv(string $filePath): array
    {
        $data = [];
        $handle = fopen($filePath, 'r');
        
        if ($handle !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $data[] = $row;
            }
            fclose($handle);
        }
        
        return $data;
    }

    /**
     * Parse Excel file using SimpleXLSX
     */
    private function parseExcel(string $filePath): array
    {
        if ($xlsx = SimpleXLSX::parse($filePath)) {
            return $xlsx->rows();
        } else {
            throw new \Exception('Error parsing Excel file: ' . SimpleXLSX::parseError());
        }
    }

    /**
     * Process students data from parsed file
     */
    private function processStudentsData(array $data): array
    {
        $header = array_shift($data); // Remove header row
        $successful = 0;
        $failed = 0;
        $errors = [];

        // Validate header
        $expectedFields = ['student_name', 'university_id', 'campus', 'school', 'major', 'semester', 'year'];
        $missingFields = array_diff($expectedFields, array_slice($header, 0, count($expectedFields)));
        
        if (!empty($missingFields)) {
            throw new \Exception('Missing required columns in CSV: ' . implode(', ', $missingFields));
        }

        DB::beginTransaction();
        
        try {
            foreach ($data as $index => $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Map CSV columns to model attributes with proper sanitization
                $studentData = [
                    'student_name' => trim($row[0] ?? ''),
                    'university_id' => trim($row[1] ?? ''),
                    'campus' => trim($row[2] ?? ''),
                    'school' => trim($row[3] ?? ''),
                    'major' => trim($row[4] ?? ''),
                    'semester' => trim($row[5] ?? ''),
                    'year' => is_numeric($row[6] ?? '') ? intval($row[6]) : null,
                ];

                // Validate required fields
                if (empty($studentData['student_name']) || empty($studentData['university_id'])) {
                    $failed++;
                    $errors[] = "Row " . ($index + 2) . ": Missing required fields (student_name, university_id)";
                    continue;
                }

                // Validate university_id is numeric
                if (!is_numeric($studentData['university_id'])) {
                    $failed++;
                    $errors[] = "Row " . ($index + 2) . ": University ID must be numeric";
                    continue;
                }

                $studentData['university_id'] = intval($studentData['university_id']);

                // Check if student already exists
                $existingStudent = Student::where('university_id', $studentData['university_id'])->first();
                
                try {
                    if ($existingStudent) {
                        // Update existing student
                        $existingStudent->update(array_filter($studentData, function($value) {
                            return $value !== null && $value !== '';
                        }));
                    } else {
                        // Create new student
                        Student::create(array_filter($studentData, function($value) {
                            return $value !== null && $value !== '';
                        }));
                    }
                    
                    $successful++;
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Row " . ($index + 2) . ": Database error - " . $e->getMessage();
                }
            }

            DB::commit();
            
            return [
                'successful' => $successful,
                'failed' => $failed,
                'errors' => $errors,
                'total_processed' => $successful + $failed
            ];

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Process courses data from parsed file
     */
    private function processCoursesData(array $data, string $semester): array
    {
        $header = array_shift($data); // Remove header row
        $successful = 0;
        $failed = 0;
        $errors = [];

        // Validate header
        $expectedFields = ['course_code', 'course_name', 'instructor', 'section', 'credits', 'room', 'schedule', 'days', 'time', 'school'];
        $missingFields = array_diff(array_slice($expectedFields, 0, 2), array_slice($header, 0, 2)); // Only check required fields
        
        if (!empty($missingFields)) {
            throw new \Exception('Missing required columns in CSV: ' . implode(', ', $missingFields));
        }

        DB::beginTransaction();
        
        try {
            foreach ($data as $index => $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Map CSV columns to model attributes with proper sanitization
                $courseData = [
                    'course_code' => trim($row[0] ?? ''),
                    'course_name' => trim($row[1] ?? ''),
                    'instructor' => trim($row[2] ?? ''),
                    'section' => trim($row[3] ?? ''),
                    'credits' => is_numeric($row[4] ?? '') ? intval($row[4]) : null,
                    'room' => trim($row[5] ?? ''),
                    'schedule' => trim($row[6] ?? ''),
                    'days' => trim($row[7] ?? ''),
                    'time' => trim($row[8] ?? ''),
                    'school' => trim($row[9] ?? ''),
                ];

                // Validate required fields
                if (empty($courseData['course_code']) || empty($courseData['course_name'])) {
                    $failed++;
                    $errors[] = "Row " . ($index + 2) . ": Missing required fields (course_code, course_name)";
                    continue;
                }

                // Validate course_code format (basic validation)
                if (!preg_match('/^[A-Z]{2,4}\d{3,4}[A-Z]?$/i', $courseData['course_code'])) {
                    $errors[] = "Row " . ($index + 2) . ": Warning - Course code format may be invalid: " . $courseData['course_code'];
                }

                // Check if course already exists (course_code + section combination should be unique)
                $existingCourse = Course::where('course_code', $courseData['course_code'])
                                      ->where('section', $courseData['section'] ?: '')
                                      ->first();
                
                try {
                    if ($existingCourse) {
                        // Update existing course
                        $existingCourse->update(array_filter($courseData, function($value) {
                            return $value !== null && $value !== '';
                        }));
                    } else {
                        // Create new course
                        Course::create(array_filter($courseData, function($value) {
                            return $value !== null && $value !== '';
                        }));
                    }
                    
                    $successful++;
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Row " . ($index + 2) . ": Database error - " . $e->getMessage();
                }
            }

            DB::commit();
            
            return [
                'successful' => $successful,
                'failed' => $failed,
                'errors' => $errors,
                'semester' => $semester,
                'total_processed' => $successful + $failed
            ];

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Get import templates and field information
     */
    public function getTemplateInfo(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'students' => [
                    'required_fields' => ['student_name', 'university_id'],
                    'optional_fields' => ['campus', 'school', 'major', 'semester', 'year'],
                    'field_order' => [
                        'student_name' => 'Student Name',
                        'university_id' => 'University ID (unique)',
                        'campus' => 'Campus',
                        'school' => 'School',
                        'major' => 'Major',
                        'semester' => 'Semester',
                        'year' => 'Year'
                    ],
                    'example_csv' => "student_name,university_id,campus,school,major,semester,year\nJohn Doe,12345,Main Campus,Engineering,Computer Science,Fall 2025,3"
                ],
                'courses' => [
                    'required_fields' => ['course_code', 'course_name'],
                    'optional_fields' => ['instructor', 'section', 'credits', 'room', 'schedule', 'days', 'time', 'school'],
                    'field_order' => [
                        'course_code' => 'Course Code (unique)',
                        'course_name' => 'Course Name',
                        'instructor' => 'Instructor',
                        'section' => 'Section',
                        'credits' => 'Credits',
                        'room' => 'Room',
                        'schedule' => 'Schedule',
                        'days' => 'Days',
                        'time' => 'Time',
                        'school' => 'School'
                    ],
                    'example_csv' => "course_code,course_name,instructor,section,credits,room,schedule,days,time,school\nCS101,Introduction to Programming,Dr. Smith,A,3,Room 101,MWF,Monday Wednesday Friday,9:00-10:00,Engineering"
                ]
            ]
        ]);
    }

    /**
     * Download student import template
     */
    public function downloadStudentTemplate()
    {
        $templatePath = public_path('templates/students_template.csv');
        
        if (!file_exists($templatePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Template file not found'
            ], 404);
        }

        return response()->download($templatePath, 'students_import_template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Download course import template
     */
    public function downloadCourseTemplate()
    {
        $templatePath = public_path('templates/courses_template.csv');
        
        if (!file_exists($templatePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Template file not found'
            ], 404);
        }

        return response()->download($templatePath, 'courses_import_template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
