<?php

namespace App\Http\Controllers;

use App\Models\AccountRequest;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AccountRequestController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Store a new account request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'organization_name' => 'required|string|max:255',
            'organization_type' => 'required|string|in:' . implode(',', array_keys(AccountRequest::ORGANIZATION_TYPES)),
            'football_type' => 'required|string|in:' . implode(',', array_keys(AccountRequest::FOOTBALL_TYPES)),
            'fifa_connect_type' => 'required|string|in:' . implode(',', array_keys(AccountRequest::FIFA_CONNECT_TYPES)),
            'association_id' => 'required|exists:associations,id',
            'city' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'fr' ? 'Erreur de validation' : 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if email already exists in users table
            if (User::where('email', $request->email)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => app()->getLocale() === 'fr' 
                        ? 'Un compte avec cette adresse email existe déjà.' 
                        : 'An account with this email address already exists.'
                ], 422);
            }

            // Check if email already has a pending request
            if (AccountRequest::where('email', $request->email)->whereIn('status', ['pending', 'contacted'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => app()->getLocale() === 'fr' 
                        ? 'Une demande en attente existe déjà pour cette adresse email.' 
                        : 'A pending request already exists for this email address.'
                ], 422);
            }

            // Create the account request
            $accountRequest = AccountRequest::create($request->all());

            // Log the action
            Log::info("Account request submitted", [
                'id' => $accountRequest->id,
                'email' => $accountRequest->email,
                'organization' => $accountRequest->organization_name,
                'football_type' => $accountRequest->football_type
            ]);

            // Notify association agents using the service
            $this->notificationService->sendAccountRequestSubmitted($accountRequest);

            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'fr' 
                    ? 'Votre demande a été soumise avec succès. Nous vous contacterons bientôt.' 
                    : 'Your request has been submitted successfully. We will contact you soon.',
                'data' => [
                    'id' => $accountRequest->id,
                    'status' => $accountRequest->status
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error("Error creating account request", [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'fr' 
                    ? 'Une erreur est survenue lors de la soumission de votre demande.' 
                    : 'An error occurred while submitting your request.'
            ], 500);
        }
    }

    /**
     * Get football types
     */
    public function getFootballTypes()
    {
        return response()->json([
            'success' => true,
            'data' => AccountRequest::FOOTBALL_TYPES
        ]);
    }

    /**
     * Get organization types
     */
    public function getOrganizationTypes()
    {
        return response()->json([
            'success' => true,
            'data' => AccountRequest::ORGANIZATION_TYPES
        ]);
    }

    /**
     * Get FIFA Connect types
     */
    public function getFifaConnectTypes()
    {
        return response()->json([
            'success' => true,
            'data' => AccountRequest::FIFA_CONNECT_TYPES
        ]);
    }

    /**
     * Get FIFA associations grouped by confederation
     */
    public function getFifaAssociations()
    {
        try {
            $associations = \App\Models\Association::orderBy('confederation')
                ->orderBy('name')
                ->get()
                ->groupBy('confederation');

            $groupedAssociations = [];
            foreach ($associations as $confederation => $associationList) {
                $groupedAssociations[$confederation] = $associationList->map(function ($association) {
                    return [
                        'id' => $association->id,
                        'name' => $association->name,
                        'country' => $association->country,
                        'short_name' => $association->short_name,
                        'confederation' => $association->confederation,
                        'fifa_ranking' => $association->fifa_ranking,
                        'full_name' => $association->getFullNameAttribute()
                    ];
                });
            }

            return response()->json([
                'success' => true,
                'data' => $groupedAssociations
            ]);

        } catch (\Exception $e) {
            Log::error("Error fetching FIFA associations", [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching associations'
            ], 500);
        }
    }

    /**
     * Approve an account request
     */
    public function approve(Request $request, AccountRequest $accountRequest)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255|unique:users,email',
                'password' => 'required|string|min:8',
                'role' => 'required|string|in:club_admin,club_manager,club_medical,association_admin,association_registrar,association_medical',
                'entity_type' => 'required|string|in:club,association',
                'entity_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create the user
            $user = User::create([
                'name' => $accountRequest->first_name . ' ' . $accountRequest->last_name,
                'email' => $request->username,
                'password' => bcrypt($request->password),
                'role' => $request->role,
                'entity_type' => $request->entity_type,
                'entity_id' => $request->entity_id,
                'club_id' => $request->entity_type === 'club' ? $request->entity_id : null,
                'association_id' => $request->entity_type === 'association' ? $request->entity_id : null,
                'status' => 'active',
            ]);

            // Update account request status
            $accountRequest->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'user_id' => $user->id,
            ]);

            // Send approval notification
            $this->notificationService->sendAccountRequestApproved($accountRequest, auth()->user());

            Log::info("Account request approved", [
                'request_id' => $accountRequest->id,
                'user_id' => $user->id,
                'approved_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Account request approved successfully.',
                'data' => [
                    'user_id' => $user->id,
                    'account_request' => $accountRequest
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Error approving account request", [
                'request_id' => $accountRequest->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while approving the request.'
            ], 500);
        }
    }

    /**
     * Reject an account request
     */
    public function reject(Request $request, AccountRequest $accountRequest)
    {
        try {
            $validator = Validator::make($request->all(), [
                'reason' => 'required|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update account request status
            $accountRequest->update([
                'status' => 'rejected',
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
                'rejection_reason' => $request->reason,
            ]);

            // Send rejection notification
            $this->notificationService->sendAccountRequestRejected($accountRequest, auth()->user(), $request->reason);

            Log::info("Account request rejected", [
                'request_id' => $accountRequest->id,
                'rejected_by' => auth()->id(),
                'reason' => $request->reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Account request rejected successfully.',
                'data' => $accountRequest
            ]);

        } catch (\Exception $e) {
            Log::error("Error rejecting account request", [
                'request_id' => $accountRequest->id,
                'error' => $e->getMessage()
            ], 500);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while rejecting the request.'
            ], 500);
        }
    }

    /**
     * Mark account request as contacted
     */
    public function markAsContacted(AccountRequest $accountRequest)
    {
        try {
            $accountRequest->update([
                'status' => 'contacted',
                'contacted_by' => auth()->id(),
                'contacted_at' => now(),
            ]);

            Log::info("Account request marked as contacted", [
                'request_id' => $accountRequest->id,
                'contacted_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Account request marked as contacted successfully.',
                'data' => $accountRequest
            ]);

        } catch (\Exception $e) {
            Log::error("Error marking request as contacted", [
                'request_id' => $accountRequest->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the request.'
            ], 500);
        }
    }

    /**
     * Get account requests for admin review
     */
    public function index(Request $request)
    {
        $query = AccountRequest::with(['approver', 'rejector', 'association'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by organization type
        if ($request->has('organization_type')) {
            $query->where('organization_type', $request->organization_type);
        }

        // Filter by football type
        if ($request->has('football_type')) {
            $query->where('football_type', $request->football_type);
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('organization_name', 'like', "%{$search}%");
            });
        }

        $accountRequests = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $accountRequests
        ]);
    }

    /**
     * Show a specific account request
     */
    public function show(AccountRequest $accountRequest)
    {
        $accountRequest->load(['approver', 'rejector', 'association']);

        return response()->json([
            'success' => true,
            'data' => $accountRequest
        ]);
    }
}
