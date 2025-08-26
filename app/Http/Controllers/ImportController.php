<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Http\Requests\ImportStudentsRequest;
use App\Http\Requests\ImportCoursesRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLS;

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
            // Debug logging
            Log::info('Course import request received', [
                'user_id' => auth()->id(),
                'has_file' => $request->hasFile('file'),
                'semester' => $request->input('semester'),
                'file_info' => $request->hasFile('file') ? [
                    'name' => $request->file('file')->getClientOriginalName(),
                    'size' => $request->file('file')->getSize(),
                    'mime' => $request->file('file')->getMimeType(),
                    'extension' => $request->file('file')->getClientOriginalExtension(),
                ] : null,
                'headers' => $request->headers->all()
            ]);

            $file = $request->file('file');
            $semester = $request->input('semester');
            $fileExtension = $file->getClientOriginalExtension();

            // Parse the file based on extension
            $data = $this->parseFile($file, $fileExtension);

            Log::info('File parsing completed', [
                'data_rows' => count($data),
                'first_row' => $data[0] ?? null
            ]);

            if (!$data || count($data) < 2) {
                Log::warning('File validation failed - empty or insufficient data', [
                    'data_count' => count($data ?? []),
                    'data_sample' => $data ?? null
                ]);
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
     * Parse Excel file using SimpleXLSX for .xlsx and SimpleXLS for .xls
     */
    private function parseExcel(string $filePath): array
    {
        try {
            // Log file details for debugging
            Log::info('Parsing Excel file', [
                'file_path' => $filePath,
                'file_exists' => file_exists($filePath),
                'file_size' => file_exists($filePath) ? filesize($filePath) : 0,
                'mime_type' => file_exists($filePath) ? mime_content_type($filePath) : 'unknown'
            ]);

            // Check if file exists and is readable
            if (!file_exists($filePath) || !is_readable($filePath)) {
                throw new \Exception('File does not exist or is not readable');
            }

            // Check file size
            if (filesize($filePath) === 0) {
                throw new \Exception('File is empty');
            }

            $mimeType = mime_content_type($filePath);
            $rows = [];

            // Determine file type and use appropriate parser
            if (strpos($mimeType, 'application/vnd.ms-excel') !== false) {
                // This is an older .xls file - use SimpleXLS
                Log::info('Detected XLS file, using SimpleXLS parser');

                $xls = SimpleXLS::parse($filePath);
                if ($xls === false) {
                    $error = SimpleXLS::parseError();
                    Log::error('SimpleXLS parsing failed', ['error' => $error]);
                    throw new \Exception('Error parsing XLS file: ' . $error);
                }

                $rows = $xls->rows();
            } else {
                // This is likely a newer .xlsx file - use SimpleXLSX
                Log::info('Detected XLSX file, using SimpleXLSX parser');

                $xlsx = SimpleXLSX::parse($filePath);
                if ($xlsx === false) {
                    $error = SimpleXLSX::parseError();
                    Log::error('SimpleXLSX parsing failed', ['error' => $error]);
                    throw new \Exception('Error parsing XLSX file: ' . $error);
                }

                $rows = $xlsx->rows();
            }

            if (empty($rows)) {
                Log::warning('Excel file parsed but contains no rows', [
                    'mime_type' => $mimeType,
                    'file_size' => filesize($filePath)
                ]);
                throw new \Exception('Excel file contains no data');
            }

            Log::info('Excel parsing successful', [
                'total_rows' => count($rows),
                'first_row' => $rows[0] ?? null,
                'parser_used' => strpos($mimeType, 'application/vnd.ms-excel') !== false ? 'SimpleXLS' : 'SimpleXLSX'
            ]);

            return $rows;
        } catch (\Exception $e) {
            Log::error('Excel parsing exception', [
                'error' => $e->getMessage(),
                'file_path' => $filePath
            ]);
            throw $e;
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
                        $existingStudent->update(array_filter($studentData, function ($value) {
                            return $value !== null && $value !== '';
                        }));
                    } else {
                        // Create new student
                        Student::create(array_filter($studentData, function ($value) {
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

        // Define column mapping from Excel headers to database fields
        $columnMapping = [
            'Code' => 'course_code',
            'Course' => 'course_name',
            'Instructor' => 'instructor',
            'Section' => 'section',
            'Credits' => 'credits',
            'Room' => 'room',
            'Schedule' => 'schedule',
            'Days' => 'days',
            'Time' => 'time',
            'School' => 'school',
            // Also support direct database field names for backward compatibility
            'course_code' => 'course_code',
            'course_name' => 'course_name',
            'instructor' => 'instructor',
            'section' => 'section',
            'credits' => 'credits',
            'room' => 'room',
            'schedule' => 'schedule',
            'days' => 'days',
            'time' => 'time',
            'school' => 'school'
        ];

        // Create header mapping
        $headerMap = [];
        foreach ($header as $index => $column) {
            $trimmedColumn = trim($column);
            if (isset($columnMapping[$trimmedColumn])) {
                $headerMap[$index] = $columnMapping[$trimmedColumn];
            }
        }

        // Validate that ALL required fields are present
        $requiredFields = ['course_code', 'course_name', 'instructor', 'section', 'credits', 'room', 'schedule', 'days', 'time', 'school'];
        $presentFields = array_values($headerMap);
        $missingFields = array_diff($requiredFields, $presentFields);

        if (!empty($missingFields)) {
            $expectedExcelHeaders = ['Code', 'Course', 'Instructor', 'Section', 'Credits', 'Room', 'Schedule', 'Days', 'Time', 'School']; // Corresponding Excel headers
            throw new \Exception('Missing required columns. All fields are required. Expected: ' . implode(', ', $expectedExcelHeaders) . ' (or database field names: ' . implode(', ', $missingFields) . ')');
        }

        DB::beginTransaction();

        try {
            foreach ($data as $index => $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Initialize course data array
                $courseData = [
                    'course_code' => '',
                    'course_name' => '',
                    'instructor' => '',
                    'section' => '',
                    'credits' => null,
                    'room' => '',
                    'schedule' => '',
                    'days' => '',
                    'time' => '',
                    'school' => '',
                ];

                // Map Excel columns to database fields using header mapping
                foreach ($headerMap as $excelIndex => $dbField) {
                    if (isset($row[$excelIndex])) {
                        $value = trim($row[$excelIndex] ?? '');

                        // Handle credits field - convert to integer
                        if ($dbField === 'credits' && is_numeric($value)) {
                            $courseData[$dbField] = intval($value);
                        } else {
                            $courseData[$dbField] = $value;
                        }
                    }
                }

                // Validate ALL required fields - no field can be empty
                $requiredFields = ['course_code', 'course_name', 'instructor', 'section', 'credits', 'room', 'schedule', 'days', 'time', 'school'];
                $missingFieldsInRow = [];

                foreach ($requiredFields as $field) {
                    if (empty($courseData[$field]) || ($field === 'credits' && $courseData[$field] === null)) {
                        $missingFieldsInRow[] = $field;
                    }
                }

                if (!empty($missingFieldsInRow)) {
                    $failed++;
                    $errors[] = "Row " . ($index + 2) . ": Missing required fields: " . implode(', ', $missingFieldsInRow);
                    continue;
                }

                // Validate credits is a positive number
                if (!is_numeric($courseData['credits']) || $courseData['credits'] <= 0) {
                    $failed++;
                    $errors[] = "Row " . ($index + 2) . ": Credits must be a positive number";
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
                        // Update existing course with all fields (no filtering since all are required)
                        $existingCourse->update($courseData);
                    } else {
                        // Create new course with all fields (no filtering since all are required)
                        Course::create($courseData);
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
                    'required_fields' => ['Code', 'Course', 'Instructor', 'Section', 'Credits', 'Room', 'Schedule', 'Days', 'Time', 'School'], // All Excel column names are required
                    'optional_fields' => [], // No optional fields - all are required
                    'field_order' => [
                        'Code' => 'Course Code (unique) - REQUIRED',
                        'Course' => 'Course Name - REQUIRED',
                        'Instructor' => 'Instructor - REQUIRED',
                        'Section' => 'Section - REQUIRED',
                        'Credits' => 'Credits - REQUIRED',
                        'Room' => 'Room - REQUIRED',
                        'Schedule' => 'Schedule - REQUIRED',
                        'Days' => 'Days - REQUIRED',
                        'Time' => 'Time - REQUIRED',
                        'School' => 'School - REQUIRED'
                    ],
                    'example_csv' => "Code,Course,Instructor,Section,Credits,Room,Schedule,Days,Time,School\nCS101,Introduction to Programming,Dr. Smith,A,3,Room 101,MWF,Monday Wednesday Friday,9:00-10:00,Engineering",
                    'database_mapping' => [
                        'Code' => 'course_code',
                        'Course' => 'course_name',
                        'Instructor' => 'instructor',
                        'Section' => 'section',
                        'Credits' => 'credits',
                        'Room' => 'room',
                        'Schedule' => 'schedule',
                        'Days' => 'days',
                        'Time' => 'time',
                        'School' => 'school'
                    ]
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
