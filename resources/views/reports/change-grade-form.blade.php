<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Change of Grade Submission Report</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <style>
        /* Print & general fallback styles (email clients that keep <style>) */
        @page {
            size: A4;
            margin: 15mm;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                font-size: 11pt;
            }

            .no-print {
                display: none !important;
            }

            .page-wrapper {
                box-shadow: none !important;
                margin: 0 !important;
                border: none !important;
            }

            a {
                color: #000 !important;
                text-decoration: none !important;
            }
        }
    </style>
</head>

<body
    style="margin:0; padding:0; background:#f2f4f7; font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif; color:#222; line-height:1.4;">
    <!-- Wrapper (kept narrow for email clients, centered for print) -->
    <div class="page-wrapper"
        style="max-width:800px; margin:24px auto; background:#ffffff; border:1px solid #d9dde3; border-radius:6px; box-shadow:0 2px 6px rgba(0,0,0,0.08); overflow:hidden;">

        <!-- Header with Logo & Title -->
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
            style="border-collapse:collapse; background:#ffffff;">
            <tr>
                <td style="padding:20px 24px; border-bottom:2px solid #0f4c81;">
                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                        style="border-collapse:collapse;">
                        <tr>
                            <td valign="middle" style="width:72px; padding:0 16px 0 0;">
                                <img src="{{ $logo_url }}" alt="Company Logo"
                                    style="display:block; max-width:72px; height:auto;" />
                            </td>
                            <td valign="middle" style="padding:0;">
                                <div
                                    style="font-size:18px; font-weight:600; letter-spacing:.5px; color:#0f4c81; text-transform:uppercase;">
                                    {{ $organization_name }}</div>
                                <div style="font-size:14px; color:#555; margin-top:2px;">{{ $department_or_faculty }}
                                </div>
                                <div style="font-size:22px; font-weight:600; margin-top:10px; color:#222;">Change of
                                    Grade Form Submission</div>
                                <div style="font-size:12px; color:#777; margin-top:4px;">Report ID: {{ $report_id }}
                                    &nbsp;|&nbsp; Generated: {{ $report_generated_at }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Notice / Intro -->
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;">
            <tr>
                <td style="padding:16px 24px 8px 24px;">
                    <p style="margin:0 0 8px 0; font-size:13px; color:#555;">
                        Below is a structured summary of the submitted Change of Grade request. Please review all fields
                        carefully.
                    </p>
                    @if ($additional_notes && $additional_notes !== 'No additional notes provided.')
                        <div
                            style="background:#e3f2fd; border-left:4px solid #2196f3; padding:12px 16px; margin:10px 0; border-radius:4px;">
                            <strong style="color:#1976d2;">Additional Message:</strong><br>
                            <span style="color:#333;">{{ $additional_notes }}</span>
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Submission Meta Section -->
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
            style="border-collapse:collapse; font-size:13px;">
            <tr>
                <td style="padding:4px 24px 16px 24px;">
                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                        style="border-collapse:collapse; background:#f7f9fc; border:1px solid #dbe2ea; border-radius:4px;">
                        <tr>
                            <td style="padding:12px 16px; vertical-align:top; width:50%;">
                                <div style="font-weight:600; font-size:14px; margin:0 0 6px 0; color:#0f4c81;">
                                    Submission Details</div>
                                <table cellpadding="0" cellspacing="0" role="presentation"
                                    style="border-collapse:collapse; width:100%; font-size:12px;">
                                    <tr>
                                        <td style="padding:3px 0; width:110px; color:#444;">Submitted At:</td>
                                        <td style="padding:3px 0; font-weight:500; color:#222;">{{ $submitted_at }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:3px 0; color:#444;">Submitted By:</td>
                                        <td style="padding:3px 0; font-weight:500; color:#222;">{{ $submitted_by_name }}
                                            ({{ $submitted_by_role }})</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:3px 0; color:#444;">User Email:</td>
                                        <td style="padding:3px 0; color:#222;">{{ $submitted_by_email }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:3px 0; color:#444;">IP Address:</td>
                                        <td style="padding:3px 0; color:#222;">{{ $submitted_by_ip }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="padding:12px 16px; vertical-align:top; width:50%;">
                                <div style="font-weight:600; font-size:14px; margin:0 0 6px 0; color:#0f4c81;">Student &
                                    Course</div>
                                <table cellpadding="0" cellspacing="0" role="presentation"
                                    style="border-collapse:collapse; width:100%; font-size:12px;">
                                    <tr>
                                        <td style="padding:3px 0; width:110px; color:#444;">Student Name:</td>
                                        <td style="padding:3px 0; font-weight:500; color:#222;">{{ $student_name }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:3px 0; color:#444;">Student ID:</td>
                                        <td style="padding:3px 0; color:#222;">{{ $student_id }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:3px 0; color:#444;">Major:</td>
                                        <td style="padding:3px 0; color:#222;">{{ $student_major }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:3px 0; color:#444;">Course Code:</td>
                                        <td style="padding:3px 0; color:#222;">{{ $course_code }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:3px 0; color:#444;">Course Name:</td>
                                        <td style="padding:3px 0; color:#222;">{{ $course_name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:3px 0; color:#444;">Section:</td>
                                        <td style="padding:3px 0; color:#222;">{{ $course_section }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:3px 0; color:#444;">Semester:</td>
                                        <td style="padding:3px 0; color:#222;">{{ $semester }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:3px 0; color:#444;">Campus:</td>
                                        <td style="padding:3px 0; color:#222;">{{ $campus }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Requested Change -->
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
            style="border-collapse:collapse; font-size:13px;">
            <tr>
                <td style="padding:0 24px 16px 24px;">
                    <div style="background:#ffffff; border:1px solid #dbe2ea; border-radius:4px;">
                        <div
                            style="padding:12px 16px; border-bottom:1px solid #e5ebf1; background:#f0f5fa; font-weight:600; font-size:14px; color:#0f4c81;">
                            Requested Grade Change
                        </div>
                        <div style="padding:14px 16px;">
                            <p style="margin:0 0 8px 0; font-size:13px; color:#333;">
                                Please change the grade from <strong
                                    style="color:#b00020;">{{ $original_grade }}</strong> to
                                <strong style="color:#0f4c81;">{{ $requested_grade }}</strong>.
                            </p>
                            <p style="margin:0; font-size:12px; color:#555;">
                                Reason(s) for change: <span
                                    style="font-weight:500; color:#222;">{{ $reason_for_change }}</span>
                            </p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Grade Breakdown Table -->
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
            style="border-collapse:collapse;">
            <tr>
                <td style="padding:0 24px 16px 24px;">
                    <div style="font-weight:600; font-size:14px; margin:0 0 8px 0; color:#0f4c81;">Breakdown of the
                        Grade</div>
                    <table width="100%" cellpadding="0" cellspacing="0" role="table"
                        aria-label="Grade Breakdown"
                        style="border-collapse:collapse; font-size:12px; table-layout:fixed; background:#fff; border:1px solid #d0d7de;">
                        <thead>
                            <tr>
                                <th
                                    style="padding:8px 6px; background:#f7f9fc; border-right:1px solid #d0d7de; border-bottom:1px solid #d0d7de; text-align:center; font-weight:600; color:#333;">
                                    Quizzes (10%)</th>
                                <th
                                    style="padding:8px 6px; background:#f7f9fc; border-right:1px solid #d0d7de; border-bottom:1px solid #d0d7de; text-align:center; font-weight:600; color:#333;">
                                    Tests (20%)</th>
                                <th
                                    style="padding:8px 6px; background:#f7f9fc; border-right:1px solid #d0d7de; border-bottom:1px solid #d0d7de; text-align:center; font-weight:600; color:#333;">
                                    Midterm (15%)</th>
                                <th
                                    style="padding:8px 6px; background:#f7f9fc; border-right:1px solid #d0d7de; border-bottom:1px solid #d0d7de; text-align:center; font-weight:600; color:#333;">
                                    Final (55%)</th>
                                <th
                                    style="padding:8px 6px; background:#f7f9fc; border-right:1px solid #d0d7de; border-bottom:1px solid #d0d7de; text-align:center; font-weight:600; color:#333;">
                                    Curve</th>
                                <th
                                    style="padding:8px 6px; background:#f7f9fc; border-right:1px solid #d0d7de; border-bottom:1px solid #d0d7de; text-align:center; font-weight:600; color:#333;">
                                    Final Grade</th>
                                <th
                                    style="padding:8px 6px; background:#f7f9fc; border-bottom:1px solid #d0d7de; text-align:center; font-weight:600; color:#333;">
                                    Letter Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td
                                    style="padding:10px 6px; text-align:center; border-right:1px solid #d0d7de; border-bottom:1px solid #d0d7de; color:#222;">
                                    {{ $quizzes_score }}</td>
                                <td
                                    style="padding:10px 6px; text-align:center; border-right:1px solid #d0d7de; border-bottom:1px solid #d0d7de; color:#222;">
                                    {{ $tests_score }}</td>
                                <td
                                    style="padding:10px 6px; text-align:center; border-right:1px solid #d0d7de; border-bottom:1px solid #d0d7de; color:#222;">
                                    {{ $midterm_score }}</td>
                                <td
                                    style="padding:10px 6px; text-align:center; border-right:1px solid #d0d7de; border-bottom:1px solid #d0d7de; color:#222;">
                                    {{ $final_score }}</td>
                                <td
                                    style="padding:10px 6px; text-align:center; border-right:1px solid #d0d7de; border-bottom:1px solid #d0d7de; color:#222;">
                                    {{ $curve_value }}</td>
                                <td
                                    style="padding:10px 6px; text-align:center; border-right:1px solid #d0d7de; border-bottom:1px solid #d0d7de; font-weight:600; color:#0f4c81;">
                                    {{ $final_numeric_grade }}</td>
                                <td
                                    style="padding:10px 6px; text-align:center; border-bottom:1px solid #d0d7de; font-weight:600; color:#0f4c81;">
                                    {{ $final_letter_grade }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Signatures Section -->
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
            style="border-collapse:collapse;">
            <tr>
                <td style="padding:0 24px 16px 24px;">
                    <div style="font-weight:600; font-size:14px; margin:0 0 8px 0; color:#0f4c81;">Authorization &
                        Signatures</div>
                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                        style="border-collapse:collapse; font-size:12px; border:1px solid #d0d7de; background:#ffffff;">
                        <tbody>
                            <tr>
                                <td
                                    style="padding:10px 12px; width:33%; border-right:1px solid #d0d7de; border-bottom:1px solid #d0d7de;">
                                    <div style="font-weight:600; color:#333;">Instructor</div>
                                    <div
                                        style="margin-top:28px; border-top:1px solid #ccc; padding-top:4px; font-size:11px; color:#444;">
                                        {{ $instructor_name }}</div>
                                    <div style="font-size:11px; color:#777;">Signed: {{ $instructor_signed_at }}</div>
                                </td>
                                <td
                                    style="padding:10px 12px; width:33%; border-right:1px solid #d0d7de; border-bottom:1px solid #d0d7de;">
                                    <div style="font-weight:600; color:#333;">Chair / Associate Chair</div>
                                    <div
                                        style="margin-top:28px; border-top:1px solid #ccc; padding-top:4px; font-size:11px; color:#444;">
                                        {{ $chair_name }}</div>
                                    <div style="font-size:11px; color:#777;">Signed: {{ $chair_signed_at }}</div>
                                </td>
                                <td style="padding:10px 12px; width:34%; border-bottom:1px solid #d0d7de;">
                                    <div style="font-weight:600; color:#333;">Dean</div>
                                    <div
                                        style="margin-top:28px; border-top:1px solid #ccc; padding-top:4px; font-size:11px; color:#444;">
                                        {{ $dean_name }}</div>
                                    <div style="font-size:11px; color:#777;">Signed: {{ $dean_signed_at }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:10px 12px; width:33%; border-right:1px solid #d0d7de;">
                                    <div style="font-weight:600; color:#333;">Academic Director</div>
                                    <div
                                        style="margin-top:28px; border-top:1px solid #ccc; padding-top:4px; font-size:11px; color:#444;">
                                        {{ $academic_director_name }}</div>
                                    <div style="font-size:11px; color:#777;">Signed:
                                        {{ $academic_director_signed_at }}</div>
                                </td>
                                <td style="padding:10px 12px; width:33%; border-right:1px solid #d0d7de;">
                                    <div style="font-weight:600; color:#333;">VPA / Admin</div>
                                    <div
                                        style="margin-top:28px; border-top:1px solid #ccc; padding-top:4px; font-size:11px; color:#444;">
                                        {{ $vpa_admin_name }}</div>
                                    <div style="font-size:11px; color:#777;">Signed: {{ $vpa_admin_signed_at }}</div>
                                </td>
                                <td style="padding:10px 12px; width:34%;">
                                    <div style="font-weight:600; color:#333;">Registrar</div>
                                    <div
                                        style="margin-top:28px; border-top:1px solid #ccc; padding-top:4px; font-size:11px; color:#444;">
                                        {{ $registrar_name }}</div>
                                    <div style="font-size:11px; color:#777;">Signed: {{ $registrar_signed_at }}</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="font-size:11px; color:#666; margin-top:6px;">(Digital timestamps represent electronic
                        approval where applicable.)</div>
                </td>
            </tr>
        </table>

        <!-- Required Documents -->
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
            style="border-collapse:collapse;">
            <tr>
                <td style="padding:0 24px 16px 24px;">
                    <div style="font-weight:600; font-size:14px; margin:0 0 6px 0; color:#0f4c81;">Required Documents
                    </div>
                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                        style="border-collapse:collapse; font-size:12px; border:1px solid #d0d7de; background:#fff;">
                        <tbody>
                            @if ($documents_available && count($documents) > 0)
                                @foreach ($documents as $document)
                                    <tr>
                                        <td
                                            style="padding:8px 12px; border-bottom:1px solid #e5ebf1; width:65%; color:#222;">
                                            {{ $document['name'] }}</td>
                                        <td
                                            style="padding:8px 12px; border-bottom:1px solid #e5ebf1; width:20%; color:#555;">
                                            {{ $document['type'] }}</td>
                                        <td
                                            style="padding:8px 12px; border-bottom:1px solid #e5ebf1; width:15%; color:#0f4c81;">
                                            {{ $document['status'] }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td style="padding:12px; color:#777; font-style:italic; text-align:center;">No
                                        documents were provided with this submission.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
            style="border-collapse:collapse; background:#0f4c81;">
            <tr>
                <td style="padding:12px 24px; text-align:center; font-size:11px; color:#e0ecf5;">
                    Confidential academic record. Generated automatically on {{ $report_generated_at }}. Reference:
                    {{ $report_id }}.
                </td>
            </tr>
        </table>
    </div>

    @if (!isset($isPdf))
        <!-- Optional action hints (hidden in print) -->
        <div class="no-print" style="text-align:center; font-size:11px; color:#8a8f98; margin:12px 0 24px 0;">
            <span style="display:inline-block; margin:0 6px;">📧 This report was sent via email</span>
            <span style="display:inline-block; margin:0 6px;">🖨️ To print: Ctrl/Cmd + P</span>
        </div>
    @endif

</body>

</html>
