<?php

namespace App\Http\Controllers;

use App\Models\RegistrationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RegistrationRequestController extends Controller
{
    /**
     * Store a new registration request.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'association' => 'required|string|max:255',
            'profile_type' => 'required|string|in:club,association,player,referee,medical_staff',
            'organization' => 'nullable|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'reason' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if email already has a pending request
        $existingRequest = RegistrationRequest::where('email', $request->email)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending registration request. Please wait for review.'
            ], 409);
        }

        // Check if user already exists
        $existingUser = \App\Models\User::where('email', $request->email)->first();
        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'A user with this email already exists. Please use the login page.'
            ], 409);
        }

        try {
            $registrationRequest = RegistrationRequest::create([
                'association' => $request->association,
                'profile_type' => $request->profile_type,
                'organization' => $request->organization,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'reason' => $request->reason,
                'status' => 'pending',
            ]);

            // TODO: Send notification to association admins
            // $this->notifyAssociationAdmins($registrationRequest);

            return response()->json([
                'success' => true,
                'message' => 'Registration request submitted successfully. The association will review your request and contact you.',
                'request_id' => $registrationRequest->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting request. Please try again.'
            ], 500);
        }
    }

    /**
     * Display a listing of registration requests (for admins).
     */
    public function index(Request $request)
    {
        // Check if user has admin role
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'system_admin', 'association_admin'])) {
            abort(403, 'Insufficient permissions to view registration requests.');
        }

        $query = RegistrationRequest::with('reviewer');

        // Filter by association if specified
        if ($request->has('association')) {
            $query->forAssociation($request->association);
        }

        // Filter by status if specified
        if ($request->has('status')) {
            $query->withStatus($request->status);
        }

        // Filter by profile type if specified
        if ($request->has('profile_type')) {
            $query->where('profile_type', $request->profile_type);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.registration-requests.index', compact('requests'));
    }

    /**
     * Display the specified registration request.
     */
    public function show(RegistrationRequest $registrationRequest)
    {
        // Check if user has admin role
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'system_admin', 'association_admin'])) {
            abort(403, 'Insufficient permissions to view registration requests.');
        }

        return view('admin.registration-requests.show', compact('registrationRequest'));
    }

    /**
     * Update the status of a registration request (approve/reject).
     */
    public function update(Request $request, RegistrationRequest $registrationRequest): JsonResponse
    {
        // Check if user has admin role
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'system_admin', 'association_admin'])) {
            abort(403, 'Insufficient permissions to update registration requests.');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $registrationRequest->update([
                'status' => $request->status,
                'admin_notes' => $request->admin_notes,
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
            ]);

            // If approved, create user account
            if ($request->status === 'approved') {
                $this->createUserFromRequest($registrationRequest);
            }

            // TODO: Send notification to requester
            // $this->notifyRequester($registrationRequest);

            return response()->json([
                'success' => true,
                'message' => 'Registration request ' . $request->status . ' successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating request. Please try again.'
            ], 500);
        }
    }

    /**
     * Create a user account from an approved registration request.
     */
    private function createUserFromRequest(RegistrationRequest $request): void
    {
        // Generate a random password
        $password = \Str::random(12);

        $user = \App\Models\User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($password),
            'role' => $this->mapProfileTypeToRole($request->profile_type),
            'association' => $request->association,
        ]);

        // TODO: Send welcome email with credentials
        // Mail::to($user->email)->send(new WelcomeEmail($user, $password));
    }

    /**
     * Map profile type to user role.
     */
    private function mapProfileTypeToRole(string $profileType): string
    {
        $roleMap = [
            'club' => 'club_admin',
            'association' => 'association_admin',
            'player' => 'player',
            'referee' => 'referee',
            'medical_staff' => 'medical_staff',
        ];

        return $roleMap[$profileType] ?? 'user';
    }

    /**
     * Get statistics for registration requests.
     */
    public function statistics(): JsonResponse
    {
        // Check if user has admin role
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'system_admin', 'association_admin'])) {
            abort(403, 'Insufficient permissions to view registration request statistics.');
        }

        $stats = [
            'total' => RegistrationRequest::count(),
            'pending' => RegistrationRequest::pending()->count(),
            'approved' => RegistrationRequest::approved()->count(),
            'rejected' => RegistrationRequest::rejected()->count(),
            'by_association' => RegistrationRequest::selectRaw('association, COUNT(*) as count')
                ->groupBy('association')
                ->get(),
            'by_profile_type' => RegistrationRequest::selectRaw('profile_type, COUNT(*) as count')
                ->groupBy('profile_type')
                ->get(),
        ];

        return response()->json($stats);
    }
}
