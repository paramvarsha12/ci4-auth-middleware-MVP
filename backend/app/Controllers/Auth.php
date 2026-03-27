<?php

namespace App\Controllers;

use App\Models\AuthUserModel;
use App\Models\TeacherModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth extends Controller
{
    protected $authUserModel;
    protected $teacherModel;

    public function __construct()
    {
        $this->authUserModel = new AuthUserModel();
        $this->teacherModel = new TeacherModel();
    }

    public function register()
    {
        $rules = [
            'email' => 'required|valid_email|is_unique[auth_user.email]',
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $userData = [
            'email' => $this->request->getVar('email'),
            'first_name' => $this->request->getVar('first_name'),
            'last_name' => $this->request->getVar('last_name'),
            'password' => $this->request->getVar('password')
        ];

        try {
            $userId = $this->authUserModel->createUser($userData);
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User registered successfully',
                'user_id' => $userId
            ])->setStatusCode(ResponseInterface::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Registration failed: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $this->authUserModel->getUserByEmail($email);

        if (!$user || !$this->authUserModel->verifyPassword($password, $user['password'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        $key = getenv('JWT_SECRET') ?: 'your-secret-key';
        $payload = [
            'uid' => $user['id'],
            'iat' => time(),
            'exp' => time() + 3600
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name']
            ]
        ]);
    }

    public function createTeacher()
    {
        $rules = [
            'email' => 'required|valid_email|is_unique[auth_user.email]',
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'password' => 'required|min_length[6]',
            'university_name' => 'required|min_length[2]',
            'gender' => 'required|in_list[male,female,other]',
            'year_joined' => 'required|numeric|greater_than[1900]|less_than[2100]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $db = \Config\Database::connect();
        
        try {
            $db->transStart();

            $userData = [
                'email' => $this->request->getVar('email'),
                'first_name' => $this->request->getVar('first_name'),
                'last_name' => $this->request->getVar('last_name'),
                'password' => $this->request->getVar('password')
            ];

            $userId = $this->authUserModel->createUser($userData);

            $teacherData = [
                'user_id' => $userId,
                'university_name' => $this->request->getVar('university_name'),
                'gender' => $this->request->getVar('gender'),
                'year_joined' => $this->request->getVar('year_joined')
            ];

            $teacherId = $this->teacherModel->createTeacher($teacherData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Teacher created successfully',
                'user_id' => $userId,
                'teacher_id' => $teacherId
            ])->setStatusCode(ResponseInterface::HTTP_CREATED);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to create teacher: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getUsers()
    {
        $users = $this->authUserModel->findAll();
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $users
        ]);
    }

    public function getTeachers()
    {
        $teachers = $this->teacherModel->getAllTeachers();
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $teachers
        ]);
    }
}
