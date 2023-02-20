<?php

namespace GenerCode\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return true;
        }

        $request->user()->sendEmailVerificationNotification();

        return ['status', 'verification-link-sent'];
    }
}
