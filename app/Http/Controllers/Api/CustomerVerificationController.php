<?php

namespace App\Http\Controllers\Api;

use App\Events\CustomerVerified;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerVerificationController extends Controller
{
    public function verify(Request $request, int $id, string $hash): JsonResponse
    {
        $customer = Customer::findOrFail($id);

        if (! hash_equals($hash, sha1($customer->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link.'], 403);
        }

        if ($customer->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        $customer->forceFill(['email_verified_at' => now()])->save();

        event(new Verified($customer));
        event(new CustomerVerified($customer));

        return response()->json(['message' => 'Email verified successfully.']);
    }
}
