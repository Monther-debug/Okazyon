<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Profile\UpdateProfileRequest;
use App\Http\Requests\User\Profile\ChangePasswordRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        return UserResource::make(auth()->user());
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        $user->update($data);

        return UserResource::make($user)->additional([
            'message' => __('user.profile_updated_successfully')
        ]);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = auth()->user();

        if (!password_verify($request->current_password, $user->password)) {
            return response()->json(['message' => __('auth.current_password_incorrect')], 400);
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json(['message' => __('auth.password_changed_successfully')], 200);
    }
}

