<?php

namespace App\Http\Controllers;

use App\Models\AccountRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountRequestController extends Controller
{
    public function create()
    {
        return view('account-request.create');
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->filled('association_id') && !ctype_digit((string) $request->input('association_id'))) {
            $request->merge(['association_id' => null]);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'organization_name' => 'nullable|string|max:255',
            'organization_type' => 'nullable|string|in:'.implode(',', array_keys(AccountRequest::ORGANIZATION_TYPES)),
            'football_type' => 'nullable|string|in:'.implode(',', array_keys(AccountRequest::FOOTBALL_TYPES)),
            'fifa_connect_type' => 'nullable|string|in:'.implode(',', array_keys(AccountRequest::FIFA_CONNECT_TYPES)),
            'association_id' => 'nullable|integer|exists:associations,id',
            'city' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
        ]);

        $accountRequest = AccountRequest::query()
            ->where('email', $validated['email'])
            ->whereIn('status', [AccountRequest::STATUS_PENDING, AccountRequest::STATUS_CONTACTED])
            ->where('created_at', '>=', now()->subDay())
            ->first();

        if (!$accountRequest) {
            $accountRequest = AccountRequest::create([
                ...$validated,
                'status' => AccountRequest::STATUS_PENDING,
            ]);
        }

        $message = 'Votre demande de compte a été enregistrée. Nous vous contacterons bientôt.';

        if ($request->expectsJson() || $request->isJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'request_id' => $accountRequest->id,
            ], 201);
        }

        return redirect()->back()->with('success', $message);
    }
}
