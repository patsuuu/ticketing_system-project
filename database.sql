CREATE DATABASE IF NOT EXISTS ticket CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ticket;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL,
  full_name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  requester_id INT NOT NULL,
  status ENUM('New','In Progress','Resolved') NOT NULL DEFAULT 'New',
  priority ENUM('Low','Medium','High') NOT NULL DEFAULT 'Medium',
  feedback TEXT NULL,
  in_progress_at DATETIME NULL,
  resolved_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO users (username, password, role, full_name) VALUES
('admin', '1234', 'admin', 'IT Admin'),
('user', '1234', 'user', 'Regular User');
