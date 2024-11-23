<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class UserRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        try {
            return $this->model->all();
        } catch (Exception $e) {
            throw new Exception('Failed to fetch users: ' . $e->getMessage());
        }
    }

    public function create(array $data)
    {
        try {
            return $this->model->create($data);
        } catch (Exception $e) {
            throw new Exception('Failed to create user: ' . $e->getMessage());
        }
    }

    public function find($id)
    {
        try {
            return $this->model->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('User not found with ID ' . $id);
        } catch (Exception $e) {
            throw new Exception('Failed to fetch user: ' . $e->getMessage());
        }
    }

    public function update($id, array $data)
    {
        try {
            $user = $this->find($id);
            $user->update($data);
            return $user;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('User not found with ID ' . $id);
        } catch (Exception $e) {
            throw new Exception('Failed to update user: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $user = $this->find($id);
            $user->delete();
            return true;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('User not found with ID ' . $id);
        } catch (Exception $e) {
            throw new Exception('Failed to delete user: ' . $e->getMessage());
        }
    }
}
