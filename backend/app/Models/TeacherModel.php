<?php

namespace App\Models;

use CodeIgniter\Model;

class TeacherModel extends Model
{
    protected $table = 'teachers';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'university_name', 'gender', 'year_joined'];
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function createTeacher($data)
    {
        return $this->insert($data);
    }

    public function getTeacherByUserId($userId)
    {
        return $this->where('user_id', $userId)->first();
    }

    public function getAllTeachers()
    {
        return $this->findAll();
    }
}
