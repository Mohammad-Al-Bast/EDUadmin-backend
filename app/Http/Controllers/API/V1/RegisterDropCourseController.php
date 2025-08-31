<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\RegisterDropCourse;
use App\Http\Requests\StoreRegisterDropCourseRequest;
use App\Http\Requests\UpdateRegisterDropCourseRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RegisterDropCourseController extends Controller
{
    /**
     * Display a listing of register/drop courses forms.
     * GET api/v1/register-drop-courses
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = RegisterDropCourse::with(['student', 'processedBy', 'registeredCourses', 'droppedCourses']);

            // Apply filters
            if ($request->has('status')) {
                $query->byStatus($request->status);
            }
            if ($request->has('semester')) {
                $query->bySemester($request->semester);
            }
            if ($request->has('academic_year')) {
                $query->byAcademicYear($request->academic_year);
            }
            if ($request->has('university_id')) {
                $query->byStudent($request->university_id);
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginate results
            $perPage = $request->get('per_page', 15);
            $forms = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $forms,
                'message' => 'Register/Drop courses forms retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving forms: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created register/drop courses form.
     * POST api/v1/register-drop-courses
     */
    public function store(StoreRegisterDropCourseRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $form = RegisterDropCourse::create([
                'university_id' => $validated['university_id'],
                'semester' => $validated['semester'],
                'academic_year' => $validated['academic_year'],
                'reason' => $validated['reason'],
                'status' => 'pending',
                'submitted_at' => now(),
            ]);

            // Create registered courses
            if (!empty($validated['registered_courses'])) {
                foreach ($validated['registered_courses'] as $courseData) {
                    $form->registeredCourses()->create([
                        'course_id' => $courseData['course_id'],
                        'course_code' => $courseData['course_code'],
                        'course_name' => $courseData['course_name'],
                        'section' => $courseData['section'],
                        'instructor' => $courseData['instructor'],
                        'credits' => $courseData['credits'],
                        'room' => $courseData['room'] ?? null,
                        'schedule' => $courseData['schedule'] ?? null,
                        'days' => $courseData['days'] ?? null,
                        'time' => $courseData['time'] ?? null,
                        'school' => $courseData['school'] ?? null,
                    ]);
                }
            }

            // Create dropped courses
            if (!empty($validated['dropped_courses'])) {
                foreach ($validated['dropped_courses'] as $courseData) {
                    $form->droppedCourses()->create([
                        'course_id' => $courseData['course_id'],
                        'course_code' => $courseData['course_code'],
                        'course_name' => $courseData['course_name'],
                        'section' => $courseData['section'],
                        'instructor' => $courseData['instructor'],
                        'credits' => $courseData['credits'],
                        'room' => $courseData['room'] ?? null,
                        'schedule' => $courseData['schedule'] ?? null,
                        'days' => $courseData['days'] ?? null,
                        'time' => $courseData['time'] ?? null,
                        'school' => $courseData['school'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Form submitted successfully',
                'data' => $form->load(['student', 'processedBy', 'registeredCourses', 'droppedCourses'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating form: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a specific register/drop courses form.
     * GET api/v1/register-drop-courses/{form}
     */
    public function show(RegisterDropCourse $form): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $form->load(['student', 'processedBy', 'registeredCourses', 'droppedCourses']),
                'message' => 'Form retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving form: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a register/drop courses form.
     * PUT api/v1/register-drop-courses/{form}
     */
    public function update(UpdateRegisterDropCourseRequest $request, RegisterDropCourse $form): JsonResponse
    {
        try {
            if (!$form->canBeEdited()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending forms can be updated.'
                ], 403);
            }

            DB::beginTransaction();

            $form->update($request->validated());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Form updated successfully',
                'data' => $form->fresh()->load(['student', 'processedBy'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating form: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a register/drop courses form.
     * DELETE api/v1/register-drop-courses/{form}
     */
    public function destroy(RegisterDropCourse $form): JsonResponse
    {
        try {
            if (!$form->canBeDeleted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending forms can be deleted.'
                ], 403);
            }

            $form->delete();

            return response()->json([
                'success' => true,
                'message' => 'Form deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting form: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all forms for a specific student.
     * GET api/v1/register-drop-courses/student/{universityId}
     */
    public function byStudent(Request $request, $universityId): JsonResponse
    {
        try {
            $query = RegisterDropCourse::with(['student', 'processedBy', 'registeredCourses', 'droppedCourses'])
                ->byStudent($universityId);

            // Apply filters
            if ($request->has('status')) {
                $query->byStatus($request->status);
            }
            if ($request->has('semester')) {
                $query->bySemester($request->semester);
            }
            if ($request->has('academic_year')) {
                $query->byAcademicYear($request->academic_year);
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginate results
            $perPage = $request->get('per_page', 15);
            $forms = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $forms,
                'message' => 'Student forms retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving student forms: ' . $e->getMessage()
            ], 500);
        }
    }
}
