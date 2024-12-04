<?php
require_once 'config.php';
require_once 'auth.php';

// Handle CORS
$allowedOrigins = ['http://localhost:5173', 'http://localhost:5174']; // Allowed origins

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);  // End preflight request
}

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$input = json_decode(file_get_contents('php://input'), true);

switch ($request[0]) {
    case 'books':
        if ($method == 'GET') {
            getBooks($pdo);
        } elseif ($method == 'POST') {
            addBook($pdo, $input);
        }
        break;
    case 'borrow':
        if ($method == 'POST') {
            borrowBook($pdo, $input);
        }
        break;
    case 'return':
        if ($method == 'POST') {
            returnBook($pdo, $input);
        }
        break;
    case 'borrowed':
        if ($method == 'GET') {
            getBorrowedBooks($pdo);
        }
        break;
    case 'register':
        if ($method == 'POST') {
            registerBorrower($pdo, $input);
        }
        break;
    case 'login':
        if ($method == 'POST') {
            loginUser($pdo, $input);
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        break;
}

function getBooks($pdo) {
    $stmt = $pdo->query('SELECT * FROM books');
    echo json_encode($stmt->fetchAll());
}

function addBook($pdo, $input) {
    $stmt = $pdo->prepare('INSERT INTO books (title, author, isbn) VALUES (?, ?, ?)');
    $stmt->execute([$input['title'], $input['author'], $input['isbn']]);
    echo json_encode(['id' => $pdo->lastInsertId()]);
}

function borrowBook($pdo, $input) {
    $stmt = $pdo->prepare('INSERT INTO borrowed_books (book_id, user_id, borrow_date, due_date) VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY))');
    $stmt->execute([$input['book_id'], $input['user_id']]);
    echo json_encode(['id' => $pdo->lastInsertId()]);
}

function returnBook($pdo, $input) {
    $stmt = $pdo->prepare('UPDATE borrowed_books SET return_date = CURDATE() WHERE id = ? AND return_date IS NULL');
    $stmt->execute([$input['borrow_id']]);
    echo json_encode(['success' => $stmt->rowCount() > 0]);
}

function getBorrowedBooks($pdo) {
    $stmt = $pdo->query('SELECT bb.*, b.title, b.author, u.username FROM borrowed_books bb JOIN books b ON bb.book_id = b.id JOIN users u ON bb.user_id = u.id WHERE bb.return_date IS NULL');
    echo json_encode($stmt->fetchAll());
}

function registerBorrower($pdo, $input) {
    if (!isset($input['username']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Username and password are required']);
        return;
    }

    try {
        $user_id = register($pdo, $input['username'], $input['password'], 'borrower');
        echo json_encode(['id' => $user_id, 'message' => 'Borrower registered successfully']);
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Registration failed. Username may already exist.']);
    }
}

function loginUser($pdo, $input) {
    if (!isset($input['username']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Username and password are required']);
        return;
    }

    $user = login($pdo, $input['username'], $input['password']);
    if ($user) {
        echo json_encode(['id' => $user['id'], 'username' => $user['username'], 'role' => $user['role']]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }
}
