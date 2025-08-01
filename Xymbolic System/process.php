<?php
include 'db.php';

// Helper: Log errors to a file
function log_error($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . PHP_EOL, 3, __DIR__ . '/error.log');
}

// Helper: Validate required fields
function validate_company($data) {
    $required = ['name', 'email'];
    foreach ($required as $field) {
        if (empty(trim($data[$field] ?? ''))) {
            return false;
        }
    }
    return true;
}

// Create company
if (isset($_POST['create'])) {
    if (!validate_company($_POST)) {
        log_error("Validation failed on create: " . json_encode($_POST));
        header("Location: index.php?error=Missing required fields");
        exit;
    }
    // Check for duplicate company (by name and location)
    $dup = $conn->prepare("SELECT id FROM companies WHERE name=? AND location=? AND status != 'archived'");
    $dup->bind_param("ss", $_POST['name'], $_POST['location']);
    $dup->execute();
    $dup->store_result();
    if ($dup->num_rows > 0) {
        header("Location: index.php?error=Duplicate company");
        exit;
    }
    $stmt = $conn->prepare("INSERT INTO companies (name, description, sector, location, contact_number, email, url, opening_hours, status, review) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        log_error("Prepare failed: " . $conn->error);
        header("Location: index.php?error=DB error");
        exit;
    }
    $stmt->bind_param("ssssssssss",
        $_POST['name'],
        $_POST['description'],
        $_POST['sector'],
        $_POST['location'],
        $_POST['contact_number'],
        $_POST['email'],
        $_POST['url'],
        $_POST['opening_hours'],
        $_POST['status'],
        $_POST['review']
    );
    if (!$stmt->execute()) {
        log_error("Execute failed: " . $stmt->error);
    }
}

// Update company
if (isset($_POST['update'])) {
    if (!validate_company($_POST)) {
        log_error("Validation failed on update: " . json_encode($_POST));
        header("Location: index.php?error=Missing required fields");
        exit;
    }
    $stmt = $conn->prepare("UPDATE companies SET name=?, description=?, sector=?, location=?, contact_number=?, email=?, url=?, opening_hours=?, status=?, review=? WHERE id=?");
    if (!$stmt) {
        log_error("Prepare failed: " . $conn->error);
        header("Location: index.php?error=DB error");
        exit;
    }
    $stmt->bind_param("ssssssssssi",
        $_POST['name'],
        $_POST['description'],
        $_POST['sector'],
        $_POST['location'],
        $_POST['contact_number'],
        $_POST['email'],
        $_POST['url'],
        $_POST['opening_hours'],
        $_POST['status'],
        $_POST['review'],
        $_POST['id']
    );
    if (!$stmt->execute()) {
        log_error("Execute failed: " . $stmt->error);
    }
}

// Hard Delete company (permanent)
if (isset($_POST['delete'])) {
    $stmt = $conn->prepare("DELETE FROM companies WHERE id=?");
    if (!$stmt) {
        log_error("Prepare failed: " . $conn->error);
        header("Location: index.php?error=DB error");
        exit;
    }
    $stmt->bind_param("i", $_POST['id']);
    if (!$stmt->execute()) {
        log_error("Execute failed: " . $stmt->error);
    }
}

// Add product with details (name, price, description)
if (isset($_POST['add_product']) && !empty($_POST['product_name'])) {
    // Check for duplicate product in the same company
    $dup = $conn->prepare("SELECT id FROM products WHERE company_id=? AND name=?");
    $dup->bind_param("is", $_POST['company_id'], $_POST['product_name']);
    $dup->execute();
    $dup->store_result();
    if ($dup->num_rows > 0) {
        header("Location: index.php?company_id=" . $_POST['company_id'] . "&error=Duplicate product");
        exit;
    }
    $price = $_POST['product_price'] ?? null;
    $desc = $_POST['product_description'] ?? null;
    $category = $_POST['product_category'] ?? '';
    if ($category === '_custom') {
        $category = trim($_POST['custom_category'] ?? '');
    }
    $stmt = $conn->prepare("INSERT INTO products (company_id, name, price, description, category) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        log_error("Prepare failed: " . $conn->error);
        header("Location: index.php?error=DB error");
        exit;
    }
    $stmt->bind_param("isdss", $_POST['company_id'], $_POST['product_name'], $price, $desc, $category);
    if (!$stmt->execute()) {
        log_error("Execute failed: " . $stmt->error);
    }
}

// Delete product (hard delete for products)
if (isset($_POST['delete_product'])) {
    $stmt = $conn->prepare("DELETE FROM products WHERE id=? AND company_id=?");
    if (!$stmt) {
        log_error("Prepare failed: " . $conn->error);
        header("Location: index.php?error=DB error");
        exit;
    }
    $stmt->bind_param("ii", $_POST['product_id'], $_POST['company_id']);
    if (!$stmt->execute()) {
        log_error("Execute failed: " . $stmt->error);
    }
}

// Add company from modal
if (isset($_POST['add_company'])) {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $sector = '';
    $opening_hours = '';
    $status = 'active';
    $review = trim($_POST['review'] ?? '0'); // <-- FIXED: get review from POST
    if ($name === '' || $email === '' || $contact_number === '' || $location === '') {
        header("Location: index.php?error=Missing required fields");
        exit;
    }
    // Check for duplicate company (by name and email)
    $dup = $conn->prepare("SELECT id FROM companies WHERE name=? AND email=? AND status != 'archived'");
    $dup->bind_param("ss", $name, $email);
    $dup->execute();
    $dup->store_result();
    if ($dup->num_rows > 0) {
        header("Location: index.php?error=Duplicate company");
        exit;
    }
    $stmt = $conn->prepare("INSERT INTO companies (name, description, sector, location, contact_number, email, url, opening_hours, status, review) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        log_error("Prepare failed: " . $conn->error);
        header("Location: index.php?error=DB error");
        exit;
    }
    $stmt->bind_param("ssssssssss", $name, $description, $sector, $location, $contact_number, $email, $url, $opening_hours, $status, $review);
    if (!$stmt->execute()) {
        log_error("Execute failed: " . $stmt->error);
        header("Location: index.php?error=DB error");
        exit;
    }
    header("Location: index.php");
    exit;
}

// AJAX inline update for company fields and product names
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['CONTENT_TYPE'], 'application/json') === 0) {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) { echo json_encode(['success'=>false,'error'=>'Invalid input']); exit; }
    if ($input['action'] === 'admin_login') {
        $username = trim($input['username'] ?? '');
        $password = trim($input['password'] ?? '');
        // DEMO ONLY: Compare password directly (no hashing)
        $stmt = $conn->prepare("SELECT password FROM admins WHERE username=? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($dbpass);
            $stmt->fetch();
            if ($password === $dbpass) {
                echo json_encode(['success'=>true]); exit;
            } else {
                echo json_encode(['success'=>false,'error'=>'Incorrect password']); exit;
            }
        } else {
            echo json_encode(['success'=>false,'error'=>'Unknown username']); exit;
        }
    }
    // Handle admin registration or password update
    if (isset($_POST['register_admin'])) {
        $username = $conn->real_escape_string($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $conn->query("INSERT INTO admins (username, password) VALUES ('$username', '$hash')");
        // ...redirect or response...
        exit;
    }
    if ($input['action'] === 'update_company') {
        $fields = ['name','location','contact_number','description','email'];
        $field = $input['field'];
        $value = trim($input['value'] ?? '');
        $company_id = intval($input['company_id']);
        if (!in_array($field, $fields)) { echo json_encode(['success'=>false,'error'=>'Invalid field']); exit; }
        $stmt = $conn->prepare("UPDATE companies SET $field=? WHERE id=?");
        if (!$stmt) { echo json_encode(['success'=>false,'error'=>'DB error']); exit; }
        $stmt->bind_param('si', $value, $company_id);
        if (!$stmt->execute()) { echo json_encode(['success'=>false,'error'=>'Update failed']); exit; }
        echo json_encode(['success'=>true]); exit;
    }
    if ($input['action'] === 'update_product') {
        $field = 'name';
        $value = trim($input['value'] ?? '');
        $product_id = intval($input['product_id']);
        $company_id = intval($input['company_id']);
        $stmt = $conn->prepare("UPDATE products SET name=? WHERE id=? AND company_id=?");
        if (!$stmt) { echo json_encode(['success'=>false,'error'=>'DB error']); exit; }
        $stmt->bind_param('sii', $value, $product_id, $company_id);
        if (!$stmt->execute()) { echo json_encode(['success'=>false,'error'=>'Update failed']); exit; }
        echo json_encode(['success'=>true]); exit;
    }
    if ($input['action'] === 'update_company_rating') {
        $company_id = intval($input['company_id'] ?? 0);
        $review = intval($input['review'] ?? 0);
        if ($company_id && $review >= 1 && $review <= 5) {
            $stmt = $conn->prepare("UPDATE companies SET review=? WHERE id=?");
            if (!$stmt) { echo json_encode(['success'=>false,'error'=>'DB error']); exit; }
            $stmt->bind_param('ii', $review, $company_id);
            if (!$stmt->execute()) { echo json_encode(['success'=>false,'error'=>'Update failed']); exit; }
            echo json_encode(['success'=>true]); exit;
        } else {
            echo json_encode(['success'=>false,'error'=>'Invalid data']); exit;
        }
    }
    echo json_encode(['success'=>false,'error'=>'Unknown action']); exit;
}

header("Location: index.php");
exit;


