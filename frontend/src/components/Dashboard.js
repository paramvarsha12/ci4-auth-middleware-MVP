import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';

const Dashboard = () => {
  const [users, setUsers] = useState([]);
  const [teachers, setTeachers] = useState([]);
  const [teacherFormData, setTeacherFormData] = useState({
    email: '',
    first_name: '',
    last_name: '',
    password: '',
    university_name: '',
    gender: 'male',
    year_joined: ''
  });
  const [message, setMessage] = useState('');
  const [isError, setIsError] = useState(false);
  const navigate = useNavigate();

  const token = localStorage.getItem('token');

  useEffect(() => {
    if (!token) {
      navigate('/login');
      return;
    }

    fetchUsers();
    fetchTeachers();
  }, [token, navigate]);

  const fetchUsers = async () => {
    try {
      const response = await fetch('http://localhost:8080/api/users', {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });

      if (response.ok) {
        const data = await response.json();
        setUsers(data.data);
      } else if (response.status === 401) {
        setMessage('Session expired. Please login again.');
        setIsError(true);
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        navigate('/login');
      }
    } catch (error) {
      console.error('Error fetching users:', error);
    }
  };

  const fetchTeachers = async () => {
    try {
      const response = await fetch('http://localhost:8080/api/teachers', {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });

      if (response.ok) {
        const data = await response.json();
        setTeachers(data.data);
      } else if (response.status === 401) {
        setMessage('Session expired. Please login again.');
        setIsError(true);
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        navigate('/login');
      }
    } catch (error) {
      console.error('Error fetching teachers:', error);
    }
  };

  const handleTeacherChange = (e) => {
    setTeacherFormData({
      ...teacherFormData,
      [e.target.name]: e.target.value
    });
  };

  const handleCreateTeacher = async (e) => {
    e.preventDefault();
    try {
      const response = await fetch('http://localhost:8080/api/create-teacher', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify(teacherFormData)
      });

      const data = await response.json();

      if (response.ok) {
        setMessage('Teacher created successfully!');
        setIsError(false);
        setTeacherFormData({
          email: '',
          first_name: '',
          last_name: '',
          password: '',
          university_name: '',
          gender: 'male',
          year_joined: ''
        });
        fetchUsers();
        fetchTeachers();
      } else {
        if (response.status === 401) {
          setMessage('Session expired. Please login again.');
          localStorage.removeItem('token');
          localStorage.removeItem('user');
          navigate('/login');
        } else {
          setMessage(data.message || 'Failed to create teacher');
        }
        setIsError(true);
      }
    } catch (error) {
      setMessage('Network error. Please try again.');
      setIsError(true);
    }
  };

  const handleLogout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    navigate('/login');
  };

  const user = JSON.parse(localStorage.getItem('user') || '{}');

  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <h2>Dashboard</h2>
        <div>
          <span>Welcome, {user.first_name} {user.last_name}</span>
          <button onClick={handleLogout} className="logout-btn">Logout</button>
        </div>
      </div>

      {message && (
        <div className={isError ? 'error' : 'success'}>
          {message}
        </div>
      )}

      <div className="data-section">
        <h3>Create New Teacher</h3>
        <form onSubmit={handleCreateTeacher}>
          <div>
            <input
              type="email"
              name="email"
              placeholder="Email"
              value={teacherFormData.email}
              onChange={handleTeacherChange}
              required
            />
          </div>
          <div>
            <input
              type="text"
              name="first_name"
              placeholder="First Name"
              value={teacherFormData.first_name}
              onChange={handleTeacherChange}
              required
            />
          </div>
          <div>
            <input
              type="text"
              name="last_name"
              placeholder="Last Name"
              value={teacherFormData.last_name}
              onChange={handleTeacherChange}
              required
            />
          </div>
          <div>
            <input
              type="password"
              name="password"
              placeholder="Password"
              value={teacherFormData.password}
              onChange={handleTeacherChange}
              required
            />
          </div>
          <div>
            <input
              type="text"
              name="university_name"
              placeholder="University Name"
              value={teacherFormData.university_name}
              onChange={handleTeacherChange}
              required
            />
          </div>
          <div>
            <select name="gender" value={teacherFormData.gender} onChange={handleTeacherChange}>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div>
            <input
              type="number"
              name="year_joined"
              placeholder="Year Joined"
              value={teacherFormData.year_joined}
              onChange={handleTeacherChange}
              required
            />
          </div>
          <button type="submit">Create Teacher</button>
        </form>
      </div>

      <div className="data-section">
        <h3>Users ({users.length})</h3>
        {users.map(user => (
          <div key={user.id} className="data-item">
            <strong>ID:</strong> {user.id} | 
            <strong>Name:</strong> {user.first_name} {user.last_name} | 
            <strong>Email:</strong> {user.email}
          </div>
        ))}
      </div>

      <div className="data-section">
        <h3>Teachers ({teachers.length})</h3>
        {teachers.map(teacher => (
          <div key={teacher.id} className="data-item">
            <strong>ID:</strong> {teacher.id} | 
            <strong>User ID:</strong> {teacher.user_id} | 
            <strong>University:</strong> {teacher.university_name} | 
            <strong>Gender:</strong> {teacher.gender} | 
            <strong>Year Joined:</strong> {teacher.year_joined}
          </div>
        ))}
      </div>
    </div>
  );
};

export default Dashboard;
