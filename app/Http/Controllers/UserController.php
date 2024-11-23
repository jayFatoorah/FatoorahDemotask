<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        try {
            $users = $this->userRepository->all();
            return response()->json($users, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch users', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'role' => 'required|in:admin,user',
            ]);

            $data['password'] = bcrypt($data['password']);
            $user = $this->userRepository->create($data);

            return response()->json($user, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create user', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = $this->userRepository->find($id);
            return response()->json($user, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch user', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'name' => 'string',
                'email' => 'email|unique:users,email,' . $id,
                'password' => 'string|min:6',
            ]);

            if (isset($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }

            $user = $this->userRepository->update($id, $data);
            return response()->json($user, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update user', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $loginUserId = auth()->user()->id;
            if($loginUserId == $id){
                return response()->json(['error' => 'Can not delete self user'], 401);    
            }
            $this->userRepository->delete($id);
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete user', 'message' => $e->getMessage()], 500);
        }
    }
    
    public function showProfile()
    {
        $user = auth()->user();
        return response()->json($user);
    }

    public function updateProfile(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'string',
                'email' => 'email|unique:users,email,' . auth()->user()->id,
                'password' => 'string|min:6',
            ]);

            if (isset($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }

            $user = auth()->user();
            $user->update($data);

            return response()->json($user, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update user', 'message' => $e->getMessage()], 500);
        }
    }
}
