<?php

namespace App\Http\Controllers\Api\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;
use App\Traits\ApiResponse;

class WebHookController extends Controller
{
    use ApiResponse;

    public function handleWebhook(Request $request)
    {
        Log::info('this is from revenue cat: ');
        Log::info($request);

        $authorizationHeader = $request->header('Authorization');

        $expectedToken = config('services.revenuecat.webhook_secret');

        Log::info('authorization header: '. $authorizationHeader);
        Log::info('webhook secret: '. $expectedToken);


        if (!$authorizationHeader || !hash_equals($expectedToken, $authorizationHeader)) {
            Log::error('Unauthorized RevenueCat webhook request.');
            return $this->error('Unauthorized', [], 401);
        }

        $event = $request->input('event');
        $userId = $event['app_user_id'] ?? null;
        $eventType = $event['type'] ?? null;

        if (!$userId || !$eventType) {
            return $this->error('Invalid payload', [], 400);
        }

        $userId = preg_replace('/\D/', '', $userId);
        $user = User::find($userId);

        if (!$user) {
            Log::warning("User not found for webhook: $userId");
            return $this->error('User not found', [], 404);
        }

        $productId = $event['product_id'] ?? null;
        $newProductId = $event['new_product_id'] ?? null;

        $getPackage = function ($id) {
            if (!$id) return null;

            if (Str::contains($id, '0001_1m')) {
                return 'monthly';
            } elseif (Str::contains($id, '0001_1y')) {
                return 'yearly';
            }

            return null;
        };

        switch ($eventType) {
            case 'INITIAL_PURCHASE':
            case 'RENEWAL':
                $user->product_id = $productId;
                $user->package = $getPackage($productId);
                $user->updated_at = now();
                break;

            case 'PRODUCT_CHANGE':
                $user->product_id = $newProductId;
                $user->package = $getPackage($newProductId);
                $user->updated_at = now();
                break;

            case 'CANCELLATION':
            case 'EXPIRATION':
                $user->product_id = null;
                $user->package = null;
                $user->updated_at = now();
                break;

            default:
                Log::info("Unhandled RevenueCat event type: $eventType");
                return $this->error("Event type '$eventType' ignored");
        }

        $user->save();

        return $this->success([], 'Webhook processed successfully');
    }
}
