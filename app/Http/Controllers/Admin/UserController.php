<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
class UserController extends Controller
{

    public function index(Request $request)
    {
        $users = QueryBuilder::for(User::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::scope('name', 'name'),
                AllowedFilter::scope('status', 'status'),
                AllowedFilter::scope('gender', 'gender'),
                AllowedFilter::scope('date_of_birth', 'dateOfBirth'),
                AllowedFilter::scope('phone_number', 'phoneNumber'),
                AllowedFilter::scope('type', 'type'),
            ])
            ->paginate(10);
        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        return UserResource::make($user);
    }


    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => "User with ID: {$user->id} deleted successfully."], 200);
    }


    public function alterBan(User $user)
    {
        $user->update(['status' => $user->status === 'banned' ? 'active' : 'banned']);
        return response()->json(['message' => "User with ID: {$user->id} has been {$user->status}."], 200);
    }
}
