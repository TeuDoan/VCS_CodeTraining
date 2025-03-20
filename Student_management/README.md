student-management-app/
│── public/               # Publicly accessible files
│   ├── assets/           # CSS, JS, images
│   ├── uploads/          # User uploads (profile pics, learning materials)
│   ├── index.php         # Entry point, redirects to login or dashboard
│   ├── .htaccess         # URL rewriting (optional)
│
│── src/                  # Core application logic
│   ├── controllers/      # Handles business logic
│   │   ├── AuthController.php       # Login, logout, registration
│   │   ├── StudentController.php    # Manage students
│   │   ├── TeacherController.php    # Manage teachers
│   │   ├── CourseController.php     # Manage courses
│   │   ├── FileUploadController.php # Handles uploads
│   │
│   ├── middleware/       # Authentication & security
│   │   ├── AuthMiddleware.php       # Redirects unauthenticated users
│   │
│   ├── models/           # Database interaction
│   │   ├── User.php      # Handles user authentication
│   │   ├── Student.php   # Student model
│   │   ├── Teacher.php   # Teacher model
│   │   ├── Course.php    # Course model
│   │
│   ├── views/            # UI templates
│   │   ├── auth/         # Authentication-related pages
│   │   │   ├── login.php
│   │   │   ├── register.php
│   │   │   ├── forgot_password.php
│   │   │
│   │   ├── dashboard/    # Main system after login
│   │   │   ├── index.php # Dashboard home
│   │   │   ├── students.php
│   │   │   ├── teachers.php
│   │   │   ├── courses.php
│   │   │
│   │   ├── profile/      # User profile management
│   │   │   ├── edit_profile.php
│   │   │   ├── change_password.php
│
│── config/               # Configuration files
│   ├── config.php        # Database connection, app settings
│   ├── routes.php        # Defines routes for controllers
│
│── database/             # Database schema & migrations
│   ├── migrations/       # SQL migrations for tables
│   ├── seeders/          # Sample data insertion
│   ├── schema.sql        # SQL file to set up database
│
│── helpers/              # Utility functions
│   ├── functions.php     # Common helper functions
│
│── logs/                 # System logs (optional)
│   ├── error.log         # Error logs
│
│── session/              # Session management
│   ├── session.php       # Handles login sessions
│
│── .env                  # Environment variables
│── composer.json         # PHP dependencies (if using Composer)
│── README.md             # Documentation
