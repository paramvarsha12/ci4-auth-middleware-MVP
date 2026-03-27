# School Management System

A full-stack application with CodeIgniter 4 backend and React frontend for managing users and teachers.

## Project Structure

```
interstackMVP/
├── backend/
│   ├── app/
│   │   ├── Controllers/
│   │   │   └── Auth.php
│   │   ├── Models/
│   │   │   ├── AuthUserModel.php
│   │   │   └── TeacherModel.php
│   │   ├── Filters/
│   │   │   └── AuthFilter.php
│   │   └── Config/
│   │       ├── Database.php
│   │       ├── Filters.php
│   │       └── Routes.php
│   ├── database/
│   │   └── schema.sql
│   ├── public/
│   │   └── index.php
│   ├── composer.json
│   └── .env
├── frontend/
│   ├── src/
│   │   ├── components/
│   │   │   ├── Register.js
│   │   │   ├── Login.js
│   │   │   └── Dashboard.js
│   │   ├── App.js
│   │   ├── index.js
│   │   └── index.css
│   ├── public/
│   │   └── index.html
│   └── package.json
└── README.md
```

## Prerequisites

- PHP 7.4 or higher
- MySQL/MariaDB
- Node.js 14 or higher
- Composer
- npm or yarn

## Setup Instructions

### 1. Database Setup

1. Start your MySQL server
2. Create the database and tables:
   ```bash
   mysql -u root -p < backend/database/schema.sql
   ```

### 2. Backend Setup (CodeIgniter 4)

1. Navigate to the backend directory:
   ```bash
   cd backend
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Update database configuration in `.env` if needed:
   ```env
   database.default.hostname = localhost
   database.default.database = school_management
   database.default.username = root
   database.default.password = your_password
   ```

4. Start the development server:
   ```bash
   php spark serve
   ```
   
   The backend will be available at `http://localhost:8080`

### 3. Frontend Setup (React)

1. Navigate to the frontend directory:
   ```bash
   cd frontend
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Start the development server:
   ```bash
   npm start
   ```
   
   The frontend will be available at `http://localhost:3000`

## API Endpoints

### Public Routes
- `POST /api/register` - Register a new user
- `POST /api/login` - Login user and return JWT token

### Protected Routes (JWT Required)
- `POST /api/create-teacher` - Create a new teacher (with user)
- `GET /api/users` - Get all users
- `GET /api/teachers` - Get all teachers

## Usage

1. **Register**: Create a new user account
2. **Login**: Authenticate and receive JWT token
3. **Dashboard**: 
   - View all users and teachers
   - Create new teachers (protected route)
   - JWT token is automatically sent with API requests

## Features

- JWT-based authentication
- One-to-one relationship between users and teachers
- Transactional integrity when creating teachers
- Protected API routes with middleware
- Responsive frontend with React
- LocalStorage for JWT persistence

## Security Notes

- Change the JWT secret key in `AuthFilter.php` and `Auth.php` from 'your-secret-key' to a secure random string
- In production, use environment variables for sensitive configuration
- Enable HTTPS in production

## Testing

You can test the API endpoints using tools like Postman or curl:

### Register User
```bash
curl -X POST http://localhost:8080/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "first_name": "John",
    "last_name": "Doe",
    "password": "password123"
  }'
```

### Login
```bash
curl -X POST http://localhost:8080/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

### Create Teacher (Protected)
```bash
curl -X POST http://localhost:8080/api/create-teacher \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "email": "teacher@example.com",
    "first_name": "Jane",
    "last_name": "Smith",
    "password": "password123",
    "university_name": "Example University",
    "gender": "female",
    "year_joined": 2023
  }'
```
