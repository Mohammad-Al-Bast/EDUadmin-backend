<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use App\Models\ChangeGradeForm;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ChangeGradeFormReport extends Mailable
{
    use Queueable, SerializesModels;

    public $form;
    public $reportData;
    public $attachPdf;
    public $customMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(ChangeGradeForm $form, array $reportData, bool $attachPdf = false, string $customMessage = '')
    {
        $this->form = $form;
        $this->reportData = $reportData;
        $this->attachPdf = $attachPdf;
        $this->customMessage = $customMessage;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Change of Grade Submission Report - ' . $this->reportData['report_id'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'reports.change-grade-form',
            with: $this->getTemplateData()
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $attachments = [];

        if ($this->attachPdf) {
            $pdf = PDF::loadView(
                'reports.change-grade-form',
                array_merge($this->getTemplateData(), ['isPdf' => true])
            );

            $attachments[] = Attachment::fromData(
                fn() => $pdf->output(),
                'change-grade-form-' . $this->form->id . '.pdf'
            )->withMime('application/pdf');
        }

        return $attachments;
    }

    /**
     * Get template data for the report
     */
    private function getTemplateData(): array
    {
        // Get authenticated user info if available
        $user = Auth::user();

        return [
            // Header & Organization
            'logo_url' => env('APP_LOGO_URL', 'https://via.placeholder.com/72x72?text=LOGO'),
            'organization_name' => env('APP_ORGANIZATION_NAME', 'University Name'),
            'department_or_faculty' => env('APP_DEPARTMENT_NAME', 'Academic Affairs'),
            'report_id' => $this->reportData['report_id'],
            'report_generated_at' => $this->reportData['generated_at'],

            // Submission Details
            'submitted_at' => $this->form->created_at->format('M j, Y \a\t g:i A'),
            'submitted_by_name' => $user ? $user->name : 'System User',
            'submitted_by_role' => $user && $user->is_admin ? 'Administrator' : 'User',
            'submitted_by_email' => $user ? $user->email : 'system@university.edu',
            'submitted_by_ip' => request()->ip() ?? 'N/A',

            // Student & Course Information
            'student_name' => $this->form->student_full_name,
            'student_id' => $this->form->student ? $this->form->student->university_id : 'N/A',
            'student_major' => $this->form->major,
            'course_code' => $this->form->course_code,
            'course_name' => $this->form->course_name,
            'course_section' => $this->form->section,
            'semester' => $this->form->semester_year,
            'campus' => $this->form->campus,

            // Grade Change Request
            'original_grade' => $this->form->courseGrade->current_grade ?? 'N/A',
            'requested_grade' => $this->form->courseGrade->requested_grade ?? 'N/A',
            'reason_for_change' => $this->form->reason_for_change,

            // Grade Breakdown (if available from courseGrade relationship)
            'quizzes_score' => $this->form->courseGrade->quizzes_score ?? 'N/A',
            'tests_score' => $this->form->courseGrade->tests_score ?? 'N/A',
            'midterm_score' => $this->form->courseGrade->midterm_score ?? 'N/A',
            'final_score' => $this->form->courseGrade->final_exam_score ?? 'N/A',
            'curve_value' => $this->form->courseGrade->curve_adjustment ?? '0',
            'final_numeric_grade' => $this->form->courseGrade->final_numeric_grade ?? 'N/A',
            'final_letter_grade' => $this->form->courseGrade->final_letter_grade ?? 'N/A',

            // Signatures (placeholder data - you'll need to implement approval workflow)
            'instructor_name' => $this->form->instructor_name,
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

            // Documents
            'documents_available' => $this->hasDocuments(),
            'documents' => $this->getDocumentsList(),

            // Additional Notes
            'additional_notes' => $this->customMessage ?: 'No additional notes provided.',

            // Form object for any additional data
            'form' => $this->form,
        ];
    }

    /**
     * Check if form has any documents
     */
    private function hasDocuments(): bool
    {
        return $this->form->copy_of_original_grading_report ||
            $this->form->copy_of_graded_final_exam ||
            $this->form->tuition_report ||
            $this->form->copy_of_first_ten_pages_final_report;
    }

    /**
     * Get list of available documents
     */
    private function getDocumentsList(): array
    {
        $documents = [];

        if ($this->form->copy_of_original_grading_report) {
            $documents[] = [
                'name' => 'Copy of Original Grading Report',
                'type' => 'PDF',
                'status' => 'Provided'
            ];
        }

        if ($this->form->copy_of_graded_final_exam) {
            $documents[] = [
                'name' => 'Copy of Graded Final Exam',
                'type' => 'PDF',
                'status' => 'Provided'
            ];
        }

        if ($this->form->tuition_report) {
            $documents[] = [
                'name' => 'Tuition Report',
                'type' => 'PDF',
                'status' => 'Provided'
            ];
        }

        if ($this->form->copy_of_first_ten_pages_final_report) {
            $documents[] = [
                'name' => 'Final Report (First 10 Pages)',
                'type' => 'PDF',
                'status' => 'Provided'
            ];
        }

        return $documents;
    }
}
