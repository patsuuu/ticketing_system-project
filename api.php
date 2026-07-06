<?php
session_start();
require_once __DIR__ . '/db_connect.php';
header('Content-Type: application/json');

define('GOOGLE_CLIENT_ID', '573671083425-un6070u0nf8a27g3e5jki08ou4rmph80.apps.googleusercontent.com');

defaultResponse();

$action = $_GET['action'] ?? '';
try {
    switch ($action) {
        case 'login':
            handleLogin();
            break;
        case 'logout':
            handleLogout();
            break;
        case 'profile':
            handleProfile();
            break;
        case 'getTickets':
            handleGetTickets();
            break;
        case 'createTicket':
            handleCreateTicket();
            break;
        case 'addFeedback':
            handleAddFeedback();
            break;
        case 'getMessages':
            handleGetMessages();
            break;
        case 'postMessage':
            handlePostMessage();
            break;
        case 'updateStatus':
            handleUpdateStatus();
            break;
        case 'googleLogin':
            handleGoogleLogin();
            break;
        case 'register':
            handleRegister();
            break;
        case 'addUser':
            handleAddUser();
            break;
        default:
            throw new Exception('Unknown action.');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function defaultResponse() {
    // noop placeholder
}

function getStoredUsers() {
    if (empty($_SESSION['users']) || !is_array($_SESSION['users'])) {
        $_SESSION['users'] = [];
    }
    return $_SESSION['users'];
}

function getStoredUser($role) {
    $users = getStoredUsers();
    return $users[$role] ?? null;
}

function storeUserSession($user) {
    $users = getStoredUsers();
    $users[$user['role']] = $user;
    $_SESSION['users'] = $users;
    $_SESSION['active_role'] = $user['role'];
}

function removeStoredUser($role) {
    $users = getStoredUsers();
    unset($users[$role]);
    $_SESSION['users'] = $users;

    if (empty($users)) {
        unset($_SESSION['active_role']);
        return [];
    }

    if (($_SESSION['active_role'] ?? '') === $role) {
        $_SESSION['active_role'] = array_key_first($users);
    }

    return array_keys($users);
}

function handleLogin() {
    $payload = json_decode(file_get_contents('php://input'), true);
    $username = trim($payload['username'] ?? '');
    $password = trim($payload['password'] ?? '');

    if ($username === '' || $password === '') {
        throw new Exception('Missing or invalid credentials.');
    }

    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT id, username, password, role, full_name FROM users WHERE username = ? AND role = ?');
    $stmt->execute([$username, 'admin']);
    $user = $stmt->fetch();

    if (!$user || $password !== $user['password']) {
        throw new Exception('Invalid admin username or password.');
    }

    $sessionUser = [
        'id' => $user['id'],
        'username' => $user['username'],
        'role' => $user['role'],
        'full_name' => $user['full_name'],
    ];

    storeUserSession($sessionUser);

    echo json_encode([
        'success' => true,
        'user' => $sessionUser,
        'logged_in_roles' => array_keys(getStoredUsers()),
    ]);
}

function handleLogout() {
    $payload = json_decode(file_get_contents('php://input'), true);
    $role = $payload['role'] ?? ($_GET['role'] ?? ($_SESSION['active_role'] ?? ''));

    if ($role !== 'admin' && $role !== 'user') {
        unset($_SESSION['users'], $_SESSION['active_role']);
        echo json_encode(['success' => true, 'logged_in_roles' => []]);
        return;
    }

    $remainingRoles = removeStoredUser($role);
    echo json_encode(['success' => true, 'logged_in_roles' => $remainingRoles]);
}

function handleProfile() {
    $role = $_GET['role'] ?? ($_SESSION['active_role'] ?? '');
    $user = $role ? getStoredUser($role) : null;

    if (!$user) {
        throw new Exception('Not logged in.');
    }

    echo json_encode(['success' => true, 'user' => $user, 'logged_in_roles' => array_keys(getStoredUsers())]);
}

function handleGetTickets() {
    $role = $_GET['role'] ?? ($_SESSION['active_role'] ?? '');
    if ($role !== 'admin' && $role !== 'user') {
        throw new Exception('Missing role.');
    }

    $user = getStoredUser($role);
    if (!$user) {
        throw new Exception('Not logged in.');
    }

    $pdo = getPDO();
    ensureTicketColumns($pdo);
    ensureMessagesTable($pdo);

    if ($user['role'] === 'admin') {
        $stmt = $pdo->query("SELECT t.id, t.title, t.description, t.status, t.priority, t.feedback, t.resolved_at, t.in_progress_at, t.created_at, u.username AS requester, IFNULL((SELECT COUNT(*) FROM messages m WHERE m.ticket_id = t.id AND m.sender_role = 'user' AND m.seen_by_admin = 0), 0) AS unread_count FROM tickets t JOIN users u ON t.requester_id = u.id ORDER BY CASE t.priority WHEN 'High' THEN 1 WHEN 'Medium' THEN 2 WHEN 'Low' THEN 3 ELSE 4 END, t.created_at DESC");
        $tickets = $stmt->fetchAll();
    } else {
        $stmt = $pdo->prepare("SELECT t.id, t.title, t.description, t.status, t.priority, t.feedback, t.resolved_at, t.in_progress_at, t.created_at, u.username AS requester, IFNULL((SELECT COUNT(*) FROM messages m WHERE m.ticket_id = t.id AND m.sender_role = 'admin' AND m.seen_by_user = 0), 0) AS unread_count FROM tickets t JOIN users u ON t.requester_id = u.id WHERE t.requester_id = ? ORDER BY t.created_at DESC");
        $stmt->execute([$user['id']]);
        $tickets = $stmt->fetchAll();
    }

    echo json_encode(['success' => true, 'tickets' => $tickets]);
}

function handleCreateTicket() {
    $payload = json_decode(file_get_contents('php://input'), true);
    $role = $payload['role'] ?? ($_GET['role'] ?? 'user');
    $user = getStoredUser($role);

    if (!$user || $user['role'] !== 'user') {
        throw new Exception('Not logged in as a user.');
    }

    $title = trim($payload['title'] ?? '');
    $description = trim($payload['description'] ?? '');
    $priority = $payload['priority'] ?? 'Medium';

    if ($title === '' || $description === '') {
        throw new Exception('Title and description are required.');
    }

    $allowed = ['Low', 'Medium', 'High'];
    if (!in_array($priority, $allowed, true)) {
        $priority = 'Medium';
    }

    $pdo = getPDO();
    $stmt = $pdo->prepare('INSERT INTO tickets (title, description, requester_id, priority) VALUES (?, ?, ?, ?)');
    $stmt->execute([$title, $description, $user['id'], $priority]);

    echo json_encode(['success' => true, 'ticket_id' => $pdo->lastInsertId()]);
}

function ensureTicketColumns($pdo) {
    $columns = [
        'feedback' => 'TEXT NULL',
        'resolved_at' => 'DATETIME NULL',
        'in_progress_at' => 'DATETIME NULL',
    ];

    foreach ($columns as $name => $definition) {
        $stmt = $pdo->query("SHOW COLUMNS FROM tickets LIKE '$name'");
        $column = $stmt ? $stmt->fetch() : false;
        if (!$column) {
            $pdo->exec("ALTER TABLE tickets ADD COLUMN $name $definition");
        }
    }
}

function handleAddFeedback() {
    $payload = json_decode(file_get_contents('php://input'), true);
    $user = getStoredUser('user');

    if (!$user || $user['role'] !== 'user') {
        throw new Exception('Not logged in as a user.');
    }

    $ticketId = (int) ($payload['ticket_id'] ?? 0);
    $feedback = trim($payload['feedback'] ?? '');

    if ($ticketId <= 0 || $feedback === '') {
        throw new Exception('Ticket ID and feedback are required.');
    }

    $pdo = getPDO();
    ensureTicketColumns($pdo);

    $stmt = $pdo->prepare('SELECT requester_id, status FROM tickets WHERE id = ?');
    $stmt->execute([$ticketId]);
    $ticket = $stmt->fetch();

    if (!$ticket || $ticket['requester_id'] !== $user['id']) {
        throw new Exception('Ticket not found.');
    }

    if ($ticket['status'] !== 'Resolved') {
        throw new Exception('Feedback can only be submitted for resolved tickets.');
    }

    $stmt = $pdo->prepare('UPDATE tickets SET feedback = ? WHERE id = ?');
    $stmt->execute([$feedback, $ticketId]);

    echo json_encode(['success' => true]);
}

function ensureMessagesTable($pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ticket_id INT NOT NULL,
        sender_role ENUM('admin','user') NOT NULL,
        sender_name VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        seen_by_user TINYINT(1) NOT NULL DEFAULT 0,
        seen_by_admin TINYINT(1) NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $pdo->exec("ALTER TABLE messages ADD COLUMN IF NOT EXISTS seen_by_user TINYINT(1) NOT NULL DEFAULT 0");
    $pdo->exec("ALTER TABLE messages ADD COLUMN IF NOT EXISTS seen_by_admin TINYINT(1) NOT NULL DEFAULT 0");
}

function getTicketAccess($pdo, $ticketId, $user) {
    $stmt = $pdo->prepare('SELECT requester_id, status FROM tickets WHERE id = ?');
    $stmt->execute([$ticketId]);
    $ticket = $stmt->fetch();
    if (!$ticket) {
        throw new Exception('Ticket not found.');
    }
    if ($user['role'] === 'user' && $ticket['requester_id'] !== $user['id']) {
        throw new Exception('Access denied.');
    }
    return $ticket;
}

function handleGetMessages() {
    $payload = json_decode(file_get_contents('php://input'), true);
    $ticketId = (int) ($_GET['ticket_id'] ?? $payload['ticket_id'] ?? 0);
    $role = $_GET['role'] ?? ($_SESSION['active_role'] ?? '');
    $user = getStoredUser($role);

    if (!$user) {
        throw new Exception('Not logged in.');
    }
    if ($ticketId <= 0) {
        throw new Exception('Ticket ID is required.');
    }

    $pdo = getPDO();
    ensureMessagesTable($pdo);
    $ticket = getTicketAccess($pdo, $ticketId, $user);
    if ($ticket['status'] !== 'In Progress') {
        throw new Exception('Message history is only available for tickets in progress.');
    }

    if ($user['role'] === 'admin') {
        $stmt = $pdo->prepare('UPDATE messages SET seen_by_admin = 1 WHERE ticket_id = ? AND sender_role = ? AND seen_by_admin = 0');
        $stmt->execute([$ticketId, 'user']);
    } else {
        $stmt = $pdo->prepare('UPDATE messages SET seen_by_user = 1 WHERE ticket_id = ? AND sender_role = ? AND seen_by_user = 0');
        $stmt->execute([$ticketId, 'admin']);
    }

    $stmt = $pdo->prepare('SELECT id, sender_role, sender_name, message, created_at FROM messages WHERE ticket_id = ? ORDER BY created_at ASC');
    $stmt->execute([$ticketId]);
    $messages = $stmt->fetchAll();

    echo json_encode(['success' => true, 'messages' => $messages]);
}

function handlePostMessage() {
    $payload = json_decode(file_get_contents('php://input'), true);
    $ticketId = (int) ($payload['ticket_id'] ?? 0);
    $message = trim($payload['message'] ?? '');
    $role = $payload['role'] ?? ($_GET['role'] ?? ($_SESSION['active_role'] ?? ''));
    $user = getStoredUser($role);

    if (!$user) {
        throw new Exception('Not logged in.');
    }
    if ($ticketId <= 0 || $message === '') {
        throw new Exception('Ticket ID and message are required.');
    }

    $pdo = getPDO();
    ensureMessagesTable($pdo);
    $ticket = getTicketAccess($pdo, $ticketId, $user);
    if ($ticket['status'] !== 'In Progress') {
        throw new Exception('Messages can only be sent for tickets in progress.');
    }

    $senderName = $user['full_name'] ?: $user['username'];
    $stmt = $pdo->prepare('INSERT INTO messages (ticket_id, sender_role, sender_name, message, seen_by_user, seen_by_admin) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $ticketId,
        $user['role'],
        $senderName,
        $message,
        $user['role'] === 'user' ? 1 : 0,
        $user['role'] === 'admin' ? 1 : 0,
    ]);

    echo json_encode(['success' => true]);
}

function handleUpdateStatus() {
    $payload = json_decode(file_get_contents('php://input'), true);
    $role = $payload['role'] ?? ($_GET['role'] ?? 'admin');
    $user = getStoredUser($role);

    if (!$user || $user['role'] !== 'admin') {
        throw new Exception('Not logged in as an admin.');
    }

    $ticketId = (int) ($payload['ticket_id'] ?? 0);
    $status = $payload['status'] ?? '';

    $allowed = ['New', 'In Progress', 'Resolved'];
    if ($ticketId <= 0 || !in_array($status, $allowed, true)) {
        throw new Exception('Invalid ticket ID or status.');
    }

    $pdo = getPDO();
    if ($status === 'In Progress') {
        $stmt = $pdo->prepare('UPDATE tickets SET status = ?, in_progress_at = NOW(), resolved_at = NULL WHERE id = ?');
        $stmt->execute([$status, $ticketId]);
    } elseif ($status === 'Resolved') {
        $stmt = $pdo->prepare('UPDATE tickets SET status = ?, resolved_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $ticketId]);
    } else {
        $stmt = $pdo->prepare('UPDATE tickets SET status = ?, in_progress_at = NULL, resolved_at = NULL WHERE id = ?');
        $stmt->execute([$status, $ticketId]);
    }

    echo json_encode(['success' => true]);
}

function handleRegister() {
    throw new Exception('Registration is now handled through Google sign-in.');
}

function handleGoogleLogin() {
    $payload = json_decode(file_get_contents('php://input'), true);
    $idToken = trim($payload['id_token'] ?? '');

    if ($idToken === '') {
        throw new Exception('Missing Google ID token.');
    }

    $tokenInfo = verifyGoogleToken($idToken);
    if (!$tokenInfo || empty($tokenInfo['email_verified']) || $tokenInfo['email_verified'] !== 'true') {
        throw new Exception('Google token verification failed.');
    }

    if (($tokenInfo['aud'] ?? '') !== GOOGLE_CLIENT_ID) {
        throw new Exception('Invalid Google client ID.');
    }

    $email = strtolower(trim($tokenInfo['email'] ?? ''));
    if ($email === '' || substr($email, -10) !== '@gmail.com') {
        throw new Exception('Please sign in with a Gmail account.');
    }

    $fullName = trim($tokenInfo['name'] ?? strstr($email, '@', true));
    if ($fullName === '') {
        $fullName = strstr($email, '@', true);
    }

    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT id, username, role, full_name FROM users WHERE username = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $user['role'] === 'admin') {
        throw new Exception('This account is reserved for admin login. Use admin credentials instead.');
    }

    if (!$user) {
        $stmt = $pdo->prepare('INSERT INTO users (username, password, role, full_name) VALUES (?, ?, ?, ?)');
        $randomPassword = bin2hex(random_bytes(16));
        $stmt->execute([$email, $randomPassword, 'user', $fullName]);
        $user = [
            'id' => (int) $pdo->lastInsertId(),
            'username' => $email,
            'role' => 'user',
            'full_name' => $fullName,
        ];
    }

    storeUserSession($user);
    echo json_encode(['success' => true, 'user' => $user]);
}

function verifyGoogleToken($idToken) {
    $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken);

    if (function_exists('curl_version')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($response === false) {
            throw new Exception('Unable to verify Google token: ' . $error);
        }
    } else {
        $response = file_get_contents($url);
        if ($response === false) {
            throw new Exception('Unable to verify Google token.');
        }
    }

    $data = json_decode($response, true);
    return is_array($data) ? $data : null;
}
