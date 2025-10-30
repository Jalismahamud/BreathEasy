<?php

namespace App\Helper;

use Illuminate\Support\Facades\Auth;

class SubscriptionHelper
{
    // ব্যবহারকারীর প্ল্যান বের করা
    public static function getUserPlan()
    {
        $user = Auth::user();
        if (!$user || !$user->is_subscribed) {
            return 'free';
        }

        $basicPlans = ['rc_breatheasy_basic_month', 'rc_breatheasy_basic_yearly'];
        $premiumPlans = ['rc_breatheasy_pro_month', 'rc_breatheasy_pro_yearly'];

        if (in_array($user->product_id, $premiumPlans)) {
            return 'premium';
        }

        if (in_array($user->product_id, $basicPlans)) {
            return 'basic';
        }

        return 'free';
    }

    /**
     * কনটেন্টে user access আছে কিনা চেক করা
     *
     * @param bool $isPremiumContent
     * @param int|null $categoryId
     * @param int|null $contentIndex (latest video index)
     * @return bool
     */
    public static function canAccess($isPremiumContent, $categoryId = null, $contentIndex = null)
    {
        $plan = self::getUserPlan();

        // Premium সবকিছু দেখতে পারবে
        if ($plan === 'premium') {
            return true;
        }

        // Free বা Basic জন্য Logic
        if ($plan === 'free' || $plan === 'basic') {

            // Premium ভিডিও হলে দেখাতে পারবে না
            if ($isPremiumContent) {
                return false;
            }

            // Regular subscription: শুধু latest 3 videos দেখার সুযোগ
            if ($plan === 'basic' && $contentIndex !== null) {
                if ($contentIndex >= 3) {
                    return false; // 3 এর বেশি ভিডিও লক
                }
            }
        }

        return true;
    }
}
