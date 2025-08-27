<?php

namespace App\Http\Controllers;

use App\Models\ChangeGradeForm;
use App\Models\Student;
use App\Mail\ChangeGradeFormReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ChangeGradeFormController extends Controller
{
    public function index()
    {
        $forms = ChangeGradeForm::with('student')->get();
        return response()->json($forms);
    }

    public function show($id)
    {
        $form = ChangeGradeForm::with('student')->findOrFail($id);
        return response()->json($form);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'university_id' => 'required|numeric|digits:8|exists:students,university_id',
            'student_full_name' => 'required|string|max:255',
            'semester_year' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'campus' => 'required|string|max:255',
            'instructor_name' => 'required|string|max:255',
            'course_code' => 'required|string|max:255',
            'course_name' => 'required|string|max:255',
            'section' => 'required|string|max:255',
            'reason_for_change' => 'required|string|max:500',
            'copy_of_original_grading_report' => 'nullable|string',
            'copy_of_graded_final_exam' => 'nullable|string',
            'tuition_report' => 'nullable|string',
            'copy_of_first_ten_pages_final_report' => 'nullable|string',
            'course_grade_id' => 'nullable|integer',
        ]);

        // Find the student by university_id and get the internal student_id
        $student = Student::where('university_id', $validated['university_id'])->firstOrFail();

        // Replace university_id with student_id for database storage
        $validated['student_id'] = $student->student_id;
        unset($validated['university_id']);

        $item = ChangeGradeForm::create($validated);

        // Load the student relationship and return
        $item->load('student');
        return response()->json($item, 201);
    }

    public function update(Request $request, $id)
    {
        $item = ChangeGradeForm::findOrFail($id);

        $validated = $request->validate([
            'university_id' => 'sometimes|numeric|digits:8|exists:students,university_id',
            'student_full_name' => 'sometimes|string|max:255',
            'semester_year' => 'sometimes|string|max:255',
            'major' => 'sometimes|string|max:255',
            'campus' => 'sometimes|string|max:255',
            'instructor_name' => 'sometimes|string|max:255',
            'course_code' => 'sometimes|string|max:255',
            'course_name' => 'sometimes|string|max:255',
            'section' => 'sometimes|string|max:255',
            'reason_for_change' => 'sometimes|string|max:500',
            'copy_of_original_grading_report' => 'sometimes|nullable|string',
            'copy_of_graded_final_exam' => 'sometimes|nullable|string',
            'tuition_report' => 'sometimes|nullable|string',
            'copy_of_first_ten_pages_final_report' => 'sometimes|nullable|string',
            'course_grade_id' => 'sometimes|nullable|integer',
        ]);

        // If university_id is provided, convert it to student_id
        if (isset($validated['university_id'])) {
            $student = Student::where('university_id', $validated['university_id'])->firstOrFail();
            $validated['student_id'] = $student->student_id;
            unset($validated['university_id']);
        }

        $item->update($validated);
        $item->load('student');
        return response()->json($item);
    }

    public function destroy($id)
    {
        ChangeGradeForm::destroy($id);
        return response()->json(null, 204);
    }

    /**
     * Generate HTML report for a specific form
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateReport($id)
    {
        try {
            $form = ChangeGradeForm::with(['student', 'courseGrade'])->findOrFail($id);

            $reportData = [
                'form' => $form,
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'report_id' => 'CGF-' . str_pad($id, 6, '0', STR_PAD_LEFT)
            ];

            $html = view(
                'reports.change-grade-form',
                $this->getTemplateData($form, $reportData)
            )->render();

            return response()->json([
                'success' => true,
                'data' => [
                    'html' => $html,
                    'report_data' => $reportData
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate and return the printable HTML report
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function generatePrintableReport($id)
    {
        try {
            $form = ChangeGradeForm::with(['student', 'courseGrade'])->findOrFail($id);

            $reportData = [
                'form' => $form,
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'report_id' => 'CGF-' . str_pad($id, 6, '0', STR_PAD_LEFT)
            ];

            $html = view(
                'reports.change-grade-form',
                $this->getTemplateData($form, $reportData)
            )->render();

            return response($html)->header('Content-Type', 'text/html');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all forms with report data
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFormsWithReports()
    {
        try {
            $forms = ChangeGradeForm::with(['student', 'courseGrade'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($form) {
                    return [
                        'id' => $form->id,
                        'student_name' => $form->student_full_name,
                        'student_id' => $form->student ? $form->student->university_id : 'N/A',
                        'course_code' => $form->course_code,
                        'course_name' => $form->course_name,
                        'created_at' => $form->created_at->format('Y-m-d H:i:s'),
                        'report_url' => url('/api/change-grade-forms/' . $form->id . '/report'),
                        'printable_url' => url('/api/change-grade-forms/' . $form->id . '/printable'),
                        'email_url' => url('/api/change-grade-forms/' . $form->id . '/send-email')
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $forms
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve forms',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send report via email using the printable HTML template
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendReportByEmail(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'attach_pdf' => 'boolean',
            'message' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $form = ChangeGradeForm::with(['student', 'courseGrade'])->findOrFail($id);

            $reportData = [
                'form' => $form,
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'report_id' => 'CGF-' . str_pad($id, 6, '0', STR_PAD_LEFT)
            ];

            $attachPdf = $request->get('attach_pdf', false);
            $customMessage = $request->get('message', '');

            Mail::to($request->email)->send(new ChangeGradeFormReport($form, $reportData, $attachPdf, $customMessage));

            return response()->json([
                'success' => true,
                'message' => 'Report sent successfully to ' . $request->email,
                'data' => [
                    'email' => $request->email,
                    'report_id' => $reportData['report_id'],
                    'sent_at' => now()->format('Y-m-d H:i:s'),
                    'pdf_attached' => $attachPdf
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send report to multiple recipients
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendReportToMultiple(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'emails' => 'required|array|min:1|max:10',
            'emails.*' => 'email',
            'attach_pdf' => 'boolean',
            'message' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $form = ChangeGradeForm::with(['student', 'courseGrade'])->findOrFail($id);

            $reportData = [
                'form' => $form,
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'report_id' => 'CGF-' . str_pad($id, 6, '0', STR_PAD_LEFT)
            ];

            $attachPdf = $request->get('attach_pdf', false);
            $customMessage = $request->get('message', '');
            $emails = $request->emails;
            $successfulSends = [];
            $failedSends = [];

            foreach ($emails as $email) {
                try {
                    Mail::to($email)->send(new ChangeGradeFormReport($form, $reportData, $attachPdf, $customMessage));
                    $successfulSends[] = $email;
                } catch (\Exception $e) {
                    $failedSends[] = [
                        'email' => $email,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return response()->json([
                'success' => count($failedSends) === 0,
                'message' => count($successfulSends) . ' emails sent successfully',
                'data' => [
                    'successful_sends' => $successfulSends,
                    'failed_sends' => $failedSends,
                    'report_id' => $reportData['report_id'],
                    'sent_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process email sending',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get authenticated user data safely
     * 
     * @return array
     */
    private function getAuthUserData(): array
    {
        $user = Auth::user();

        return [
            'name' => $user ? $user->name : 'System User',
            'role' => $user && isset($user->is_admin) && $user->is_admin ? 'Administrator' : 'User',
            'email' => $user ? $user->email : 'system@university.edu'
        ];
    }

    /**
     * Get template data array for views
     * 
     * @param ChangeGradeForm $form
     * @param array $reportData
     * @param string $customMessage
     * @return array
     */
    private function getTemplateData(ChangeGradeForm $form, array $reportData, string $customMessage = ''): array
    {
        $userData = $this->getAuthUserData();

        return [
            'logo_url' => 'https://via.placeholder.com/72x72?text=LOGO',
            'organization_name' => 'University Name',
            'department_or_faculty' => 'Academic Affairs',
            'report_id' => $reportData['report_id'],
            'report_generated_at' => $reportData['generated_at'],
            'submitted_at' => $form->created_at->format('M j, Y \a\t g:i A'),
            'submitted_by_name' => $userData['name'],
            'submitted_by_role' => $userData['role'],
            'submitted_by_email' => $userData['email'],
            'submitted_by_ip' => request()->ip() ?? 'N/A',
            'student_name' => $form->student_full_name,
            'student_id' => $form->student ? $form->student->university_id : 'N/A',
            'student_major' => $form->major,
            'course_code' => $form->course_code,
            'course_name' => $form->course_name,
            'course_section' => $form->section,
            'semester' => $form->semester_year,
            'campus' => $form->campus,
            'original_grade' => $form->courseGrade->current_grade ?? 'N/A',
            'requested_grade' => $form->courseGrade->requested_grade ?? 'N/A',
            'reason_for_change' => $form->reason_for_change,
            'quizzes_score' => $form->courseGrade->quizzes_score ?? 'N/A',
            'tests_score' => $form->courseGrade->tests_score ?? 'N/A',
            'midterm_score' => $form->courseGrade->midterm_score ?? 'N/A',
            'final_score' => $form->courseGrade->final_exam_score ?? 'N/A',
            'curve_value' => $form->courseGrade->curve_adjustment ?? '0',
            'final_numeric_grade' => $form->courseGrade->final_numeric_grade ?? 'N/A',
            'final_letter_grade' => $form->courseGrade->final_letter_grade ?? 'N/A',
            'instructor_name' => $form->instructor_name,
            'instructor_signed_at' => 'Pending',
            'chair_name' => 'Pending',
            'chair_signed_at' => 'Pending',
            'dean_name' => 'Pending',
            'dean_signed_at' => 'Pending',
            'academic_director_name' => 'Pending',
            'academic_director_signed_at' => 'Pending',
            'vpa_admin_name' => 'Pending',
            'vpa_admin_signed_at' => 'Pending',
            'registrar_name' => 'Pending',
            'registrar_signed_at' => 'Pending',
            'documents_available' => $this->hasDocuments($form),
            'documents' => $this->getDocumentsList($form),
            'additional_notes' => $customMessage ?: 'No additional notes provided.',
            'form' => $form,
        ];
    }
    private function hasDocuments(ChangeGradeForm $form): bool
    {
        return $form->copy_of_original_grading_report ||
            $form->copy_of_graded_final_exam ||
            $form->tuition_report ||
            $form->copy_of_first_ten_pages_final_report;
    }

    /**
     * Get list of available documents
     * 
     * @param ChangeGradeForm $form
     * @return array
     */
    private function getDocumentsList(ChangeGradeForm $form): array
    {
        $documents = [];

        if ($form->copy_of_original_grading_report) {
            $documents[] = [
                'name' => 'Copy of Original Grading Report',
                'type' => 'PDF',
                'status' => 'Provided'
            ];
        }

        if ($form->copy_of_graded_final_exam) {
            $documents[] = [
                'name' => 'Copy of Graded Final Exam',
                'type' => 'PDF',
                'status' => 'Provided'
            ];
        }

        if ($form->tuition_report) {
            $documents[] = [
                'name' => 'Tuition Report',
                'type' => 'PDF',
                'status' => 'Provided'
            ];
        }

        if ($form->copy_of_first_ten_pages_final_report) {
            $documents[] = [
                'name' => 'Final Report (First 10 Pages)',
                'type' => 'PDF',
                'status' => 'Provided'
            ];
        }

        return $documents;
    }
}
