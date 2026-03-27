<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthUserModel extends Model
{
    protected $table = 'auth_user';
    protected $primaryKey = 'id';
    protected $allowedFields = ['email', 'first_name', 'last_name', 'password'];
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    public function createUser($data)
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->insert($data);
    }

    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
