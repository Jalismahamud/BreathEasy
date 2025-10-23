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

    // public function handleWebhook(Request $request)
    // {
    //     Log::info('this is from revenue cat: ');
    //     Log::info($request->all());

    //     $authorizationHeader = $request->header('Authorization');
    //     $expectedToken = config('services.revenuecat.webhook_secret');

    //     Log::info('authorization header: ' . $authorizationHeader);
    //     Log::info('webhook secret: ' . $expectedToken);

    //     if (!$authorizationHeader || !hash_equals($expectedToken, $authorizationHeader)) {
    //         Log::error('Unauthorized RevenueCat webhook request.');
    //         return $this->error('Unauthorized', [], 401);
    //     }

    //     $event = $request->input('event');
    //     $userId = $event['app_user_id'] ?? null;
    //     $eventType = $event['type'] ?? null;

    //     if (!$userId || !$eventType) {
    //         return $this->error('Invalid payload', [], 400);
    //     }


    //     $user = User::where('revenuecat_id', $userId)->first();

    //     if (!$user) {
    //         Log::warning("User not found for webhook: $userId");
    //         return $this->error('User not found', [], 404);
    //     }

    //     $productId = $event['product_id'] ?? null;
    //     $newProductId = $event['new_product_id'] ?? null;

    //     $getPackage = function ($id) {
    //         if (!$id) return null;

    //         if (Str::contains($id, '0001_1m')) {
    //             return 'monthly';
    //         } elseif (Str::contains($id, '0001_1y')) {
    //             return 'yearly';
    //         }

    //         return null;
    //     };

    //     switch ($eventType) {
    //         case 'INITIAL_PURCHASE':
    //         case 'RENEWAL':
    //             $user->product_id = $productId;
    //             $user->package = $getPackage($productId);
    //             $user->updated_at = now();
    //             $user->is_subscribed = true;
    //             break;

    //         case 'PRODUCT_CHANGE':
    //             $user->product_id = $newProductId;
    //             $user->package = $getPackage($newProductId);
    //             $user->updated_at = now();
    //             $user->is_subscribed = true;
    //             break;

    //         case 'CANCELLATION':
    //         case 'EXPIRATION':
    //             $user->product_id = null;
    //             $user->package = null;
    //             $user->updated_at = now();
    //             $user->is_subscribed = false;
    //             break;

    //         default:
    //             Log::info("Unhandled RevenueCat event type: $eventType");
    //             return $this->error("Event type '$eventType' ignored");
    //     }

    //     $user->save();

    //     return $this->success([], 'Webhook processed successfully');
    // }


    public function handleWebhook(Request $request)
    {
        Log::info('this is from revenue cat check : Jalis is here .... ');
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
        Log::info('event data: '. json_encode($event));
        $userId = $event['app_user_id'] ?? null;
        Log::info('userId: '. $userId);
        $eventType = $event['type'] ?? null;
        Log::info('eventType: '. $eventType);

        if (!$userId || !$eventType) {
            return $this->error('Invalid payload', [], 400);
        }

        $userId = preg_replace('/\D/', '', $userId);
        $user = User::find($userId);
        Log::info('found user: '. ($user ? $user->id : 'none'));

        if (!$user) {
            Log::warning("User not found for webhook: $userId");
            return $this->error('User not found', [], 404);
        }

        $productId = $event['product_id'] ?? null;
        Log::info('productId: '. $productId);
        $newProductId = $event['new_product_id'] ?? null;
        Log::info('newProductId: '. $newProductId);

        $getPackage = function ($id) {
            if (!$id) return null;

            if (Str::contains($id, '0001_1m')) {
                return 'monthly';
            } elseif (Str::contains($id, '0001_1y')) {
                return 'yearly';
            }

            Log::info("Unknown package: $id");
            return null;
        };

        switch ($eventType) {
            case 'INITIAL_PURCHASE':
            case 'RENEWAL':
                $user->product_id = $productId;
                $user->package = $getPackage($productId);
                $user->revenuecat_id = $event['app_user_id'];
                $user->is_subscribed = true;
                $user->updated_at = now();
                Log::info('after initial purchase/renewal');
                break;


            case 'PRODUCT_CHANGE':
                $user->product_id = $newProductId;
                $user->package = $getPackage($newProductId);
                $user->revenuecat_id = $event['app_user_id'];
                $user->is_subscribed = true;
                $user->updated_at = now();
                Log::info('after product change');
                break;

            case 'CANCELLATION':
            case 'EXPIRATION':
                $user->product_id = null;
                $user->package = null;
                $user->is_subscribed = false;
                $user->updated_at = now();
                Log::info('after cancellation/expiration');
                break;

            default:
                Log::info("Unhandled RevenueCat event type: $eventType");
                return $this->error("Event type '$eventType' ignored");
        }

        $user->save();

        return $this->success([], 'Webhook processed successfully');
    }
}
