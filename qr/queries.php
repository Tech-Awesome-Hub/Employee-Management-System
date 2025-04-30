CREATE DATABASE workers_db;

USE workers_db;

-- Employees Table
CREATE TABLE tbl_employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(250) NOT NULL,
    other_name VARCHAR(250),
    last_name VARCHAR(250) NOT NULL,
    username VARCHAR(250) NOT NULL,
    password VARCHAR(250) NOT NULL,
    employee_id VARCHAR(50) UNIQUE,
    dob DATE,
    por VARCHAR(250),
    next_of_kin VARCHAR(250),
    ssnit_no VARCHAR(250),
    gh_card_no VARCHAR(20),
    nhis_no VARCHAR(20),
    gender VARCHAR(10),
    joining_date DATE DEFAULT CURRENT_DATE,
    phone VARCHAR(10),
    estate VARCHAR(50),
    department VARCHAR(100),
    section VARCHAR(100),
    role TINYINT DEFAULT 0,
    status TINYINT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tbl_status (
    code TINYINT PRIMARY KEY,
    label VARCHAR(50)
);
INSERT INTO tbl_status (code, label) VALUES
(0, 'Active'),
(1, 'Inactive'),
(2, 'Suspended'),
(3, 'On Leave'),
(4, 'Terminated');

CREATE TABLE tbl_role (
    code TINYINT PRIMARY KEY,
    label VARCHAR(50)
);
INSERT INTO tbl_role (code, label) VALUES
(0, 'Administration Member'),
(1, 'HR'),
(2, 'Supervisor'),
(3, 'Manager'),
(4, 'Factory Hand'),
(5, 'Admin'),
(5, 'Technician'),
(6, 'Security Personel'),
(7, 'Accountant'),
(8, 'IT Manager');

CREATE TABLE tbl_admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(250) NOT NULL,
    last_name VARCHAR(250) NOT NULL,
    username VARCHAR(250) NOT NULL,
    password VARCHAR(250) NOT NULL,
    admin_id VARCHAR(50) UNIQUE,
    role TINYINT DEFAULT 0,
    status TINYINT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tbl_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(250) NOT NULL,
    last_name VARCHAR(250) NOT NULL,
    username VARCHAR(250) NOT NULL,
    password VARCHAR(250) NOT NULL,
    user_id VARCHAR(50) UNIQUE,
    access ENUM('Full', 'Partial', 'View Only') DEFAULT 'Full';
    role TINYINT DEFAULT 0,
    status TINYINT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tbl_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  employee_id VARCHAR(50) NOT NULL,
  username VARCHAR(100) UNIQUE,
  password VARCHAR(255),
  role VARCHAR(20), -- e.g., 'supervisor', 'manager', etc.
  role TINYINT DEFAULT 0,
  status TINYINT DEFAULT 0,
  FOREIGN KEY (employee_id) REFERENCES tbl_employees(employee_id)
);
CREATE TABLE tbl_timesheet (
  id INT AUTO_INCREMENT PRIMARY KEY,
  employee_id VARCHAR(50) NOT NULL,
  supervisor_id INT NOT NULL,
  department VARCHAR(100) NOT NULL,
  date DATE NOT NULL,
  shift VARCHAR(30) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES tbl_employees(employee_id) ON DELETE CASCADE
);

CREATE TABLE tbl_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(50) NOT NULL,
    supervisor_id INT,
    attendance_date DATE,
    department VARCHAR(100),
    shift VARCHAR(50),
    esatate VARCHAR(100),
    status ENUM('present', 'absent', 'off', 'leave') DEFAULT 'present',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_attendance (employee_id, attendance_date, supervisor_id),
    FOREIGN KEY (employee_id) REFERENCES tbl_employees(employee_id) ON DELETE CASCADE
);
CREATE TABLE tbl_timesheet_summary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(50) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    attendance_period_start DATE,
    attendance_period_end DATE,
    days_of_attendance INT DEFAULT 0,
    canteen_days INT DEFAULT 0,
    overtime INT DEFAULT 0,
    absent_days INT DEFAULT 0,
    auto_leave_deduction INT DEFAULT 0,
    FOREIGN KEY (employee_id) REFERENCES tbl_employees(employee_id) ON DELETE CASCADE
);

CREATE TABLE tbl_payroll (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(50),
    daily_mark DECIMAL(10,2),
    salary_amount DECIMAL(10,2),
    deductions DECIMAL(10,2),
    net_pay DECIMAL(10,2),
    pay_date DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (employee_id) REFERENCES tbl_employees(employee_id) ON DELETE CASCADE
);

CREATE TABLE tbl_shift (
    id INT PRIMARY KEY,
    name VARCHAR(50),
    department VARCHAR(50),
    estate VARCHAR(50),
    status TINYINT DEFAULT 0,
);

CREATE TABLE tbl_estate (
    code INT PRIMARY KEY,
    label VARCHAR(50),
    status TINYINT DEFAULT 0
);
-- Leaves Table
CREATE TABLE tbl__leaves (
    leave_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    supervisor_id INT,
    manager_id INT,
    hr_id INT,
    leave_type VARCHAR(50),
    start_date DATE,
    end_date DATE,
    
    leave_type ENUM('sick', 'casual', 'annual', 'unpaid', 'maternity') NOT NULL,
    reason TEXT,
    status ENUM('pending_supervisor', 'pending_manager', 'pending_hr', 'pending_final_manager', 'approved', 'rejected') DEFAULT 'pending_supervisor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES tbl_employees(employee_id)
);

CREATE TABLE tbl_leaves (
    code INT PRIMARY KEY,
    label VARCHAR(50),
    status TINYINT DEFAULT 0,
);

CREATE TABLE tbl_leave_request (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(50),
    supervisor_id VARCHAR(50),
    manager_id VARCHAR(50),
    hr_id VARCHAR(100),
    department VARCHAR(50),
    leave_type VARCHAR(50) NOT NULL,
    reason TEXT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_days INT GENERATED ALWAYS AS (DATEDIFF(end_date, start_date) + 1) STORED,
    status_supervisor ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
    status_manager ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
    status_hr ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
    final_status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
    reviewed_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (employee_id) REFERENCES tbl_employees(employee_id) ON DELETE CASCADE,
);

CREATE TABLE tbl_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255),
    message TEXT,
    is_read TINYINT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE employees
ADD COLUMN hr_role ENUM('Full Access', 'View Only') DEFAULT 'Full Access' AFTER role;

<!--  -->
INSERT INTO tbl_employees (
    first_name, other_name, last_name, username, password,
    employee_id, dob, por, next_of_kin, ssnit_no,
    gh_card_no, nhis_no, gender, joining_date,
    phone, shift, department, role, status, created_at
)
VALUES
-- HR
('Helen', 'A', 'Owusu', 'helen.hr', 'pass123', 'DP001', '1985-05-14', 'Admin Staff', 'Kwame Owusu', 'SSNIT001', 'GHA-12345', 'NHIS001', 'Female', '2023-09-01', '0550000001', '08:00-17:00', 'HR', 1, 1, NOW()),

-- Supervisors (1 per dept)
('Stephen', 'B', 'Amoah', 'stephen.sup', 'pass123', 'DP002', '1987-04-12', 'Supervisor', 'Yaw Amoah', 'SSNIT002', 'GHA-12346', 'NHIS002', 'Male', '2023-09-02', '0550000002', '08:00-17:00', 'Technical', 2, 1, NOW()),
('Cynthia', 'C', 'Appiah', 'cynthia.sup', 'pass123', 'DP003', '1989-03-10', 'Supervisor', 'Ama Appiah', 'SSNIT003', 'GHA-12347', 'NHIS003', 'Female', '2023-09-03', '0550000003', '08:00-17:00', 'Accounts', 2, 1, NOW()),
('Yaw', 'D', 'Mensah', 'yaw.sup', 'pass123', 'DP004', '1990-02-08', 'Supervisor', 'Kojo Mensah', 'SSNIT004', 'GHA-12348', 'NHIS004', 'Male', '2023-09-04', '0550000004', '08:00-17:00', 'Peeling', 2, 1, NOW()),
('Akosua', 'E', 'Boakye', 'akosua.sup', 'pass123', 'DP005', '1986-06-15', 'Supervisor', 'Esi Boakye', 'SSNIT005', 'GHA-12349', 'NHIS005', 'Female', '2023-09-05', '0550000005', '08:00-17:00', 'Administration', 2, 1, NOW()),

-- Managers
('Kofi', 'F', 'Badu', 'kofi.mgr', 'pass123', 'DP006', '1985-01-01', 'Tech Manager', 'Nana Badu', 'SSNIT006', 'GHA-12350', 'NHIS006', 'Male', '2023-09-06', '0550000006', '08:00-17:00', 'Technical', 3, 1, NOW()),
('Ama', 'G', 'Asante', 'ama.mgr', 'pass123', 'DP007', '1988-08-20', 'Accounts Manager', 'Kwabena Asante', 'SSNIT007', 'GHA-12351', 'NHIS007', 'Female', '2023-09-07', '0550000007', '08:00-17:00', 'Accounts', 3, 1, NOW()),
('Kwame', 'H', 'Oteng', 'kwame.mgr', 'pass123', 'DP008', '1984-09-11', 'Peeling Manager', 'Akua Oteng', 'SSNIT008', 'GHA-12352', 'NHIS008', 'Male', '2023-09-08', '0550000008', '08:00-17:00', 'Peeling', 3, 1, NOW()),
('Linda', 'I', 'Arthur', 'linda.mgr', 'pass123', 'DP009', '1983-11-21', 'Admin Manager', 'Kwaku Arthur', 'SSNIT009', 'GHA-12353', 'NHIS009', 'Female', '2023-09-09', '0550000009', '08:00-17:00', 'Administration', 3, 1, NOW()),

-- Factory Hands (3 per department)
('Peter', 'J', 'Adjei', 'peter.fh', 'pass123', 'DP010', '1992-07-01', 'Factory Hand', 'Mary Adjei', 'SSNIT010', 'GHA-12354', 'NHIS010', 'Male', '2023-09-10', '0550000010', '08:00-17:00', 'Accounts', 4, 1, NOW()),
('Grace', 'K', 'Dapaah', 'grace.fh', 'pass123', 'DP011', '1993-07-02', 'Factory Hand', 'George Dapaah', 'SSNIT011', 'GHA-12355', 'NHIS011', 'Female', '2023-09-11', '0550000011', '08:00-17:00', 'Accounts', 4, 1, NOW()),
('Yaw', 'L', 'Boateng', 'yaw2.fh', 'pass123', 'DP012', '1994-07-03', 'Factory Hand', 'Alice Boateng', 'SSNIT012', 'GHA-12356', 'NHIS012', 'Male', '2023-09-12', '0550000012', '08:00-17:00', 'Accounts', 4, 1, NOW()),

('Rose', 'M', 'Mensima', 'rose.fh', 'pass123', 'DP013', '1995-07-04', 'Factory Hand', 'Yaw Mensima', 'SSNIT013', 'GHA-12357', 'NHIS013', 'Female', '2023-09-13', '0550000013', '08:00-17:00', 'Technical', 4, 1, NOW()),
('Daniel', 'N', 'Owusu', 'daniel.fh', 'pass123', 'DP014', '1996-07-05', 'Factory Hand', 'Kojo Owusu', 'SSNIT014', 'GHA-12358', 'NHIS014', 'Male', '2023-09-14', '0550000014', '08:00-17:00', 'Technical', 4, 1, NOW()),
('Abena', 'O', 'Kyei', 'abena.fh', 'pass123', 'DP015', '1997-07-06', 'Factory Hand', 'Esi Kyei', 'SSNIT015', 'GHA-12359', 'NHIS015', 'Female', '2023-09-15', '0550000015', '08:00-17:00', 'Technical', 4, 1, NOW()),

('Kojo', 'P', 'Antwi', 'kojo.fh', 'pass123', 'DP016', '1998-07-07', 'Factory Hand', 'Ama Antwi', 'SSNIT016', 'GHA-12360', 'NHIS016', 'Male', '2023-09-16', '0550000016', '08:00-17:00', 'Peeling', 4, 1, NOW()),
('Vida', 'Q', 'Mensah', 'vida.fh', 'pass123', 'DP017', '1999-07-08', 'Factory Hand', 'Kofi Mensah', 'SSNIT017', 'GHA-12361', 'NHIS017', 'Female', '2023-09-17', '0550000017', '08:00-17:00', 'Peeling', 4, 1, NOW()),
('Kwabena', 'R', 'Darko', 'kwabena.fh', 'pass123', 'DP018', '2000-07-09', 'Factory Hand', 'Akua Darko', 'SSNIT018', 'GHA-12362', 'NHIS018', 'Male', '2023-09-18', '0550000018', '08:00-17:00', 'Peeling', 4, 1, NOW()),

('Naana', 'S', 'Asamoah', 'naana.fh', 'pass123', 'DP019', '2001-07-10', 'Factory Hand', 'Yaw Asamoah', 'SSNIT019', 'GHA-12363', 'NHIS019', 'Female', '2023-09-19', '0550000019', '08:00-17:00', 'Administration', 4, 1, NOW()),
('Bright', 'T', 'Tetteh', 'bright.fh', 'pass123', 'DP020', '2002-07-11', 'Factory Hand', 'Grace Tetteh', 'SSNIT020', 'GHA-12364', 'NHIS020', 'Male', '2023-09-20', '0550000020', '08:00-17:00', 'Administration', 4, 1, NOW()),
('Esi', 'U', 'Dadzie', 'esi.fh', 'pass123', 'DP021', '2003-07-12', 'Factory Hand', 'Kwame Dadzie', 'SSNIT021', 'GHA-12365', 'NHIS021', 'Female', '2023-09-21', '0550000021', '08:00-17:00', 'Administration', 4, 1, NOW());


