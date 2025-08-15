<?php
//oten connection
$conn = mysqli_connect("localhost", "root", "", "jan");

if (!$conn) {
    die(json_encode(['success' => false, 'message' => "Connection failed: " . mysqli_connect_error()]));
}
$action = isset($_GET['action']) ? $_GET['action'] : '';

//oten data is passed
if ($action === 'addStudent') {
    $data = json_decode(file_get_contents('php://input'), true);
    $firstName = isset($data['firstName']) ? $data['firstName'] : '';
    $lastName = isset($data['lastName']) ? $data['lastName'] : '';
    $course = isset($data['course']) ? $data['course'] : '';

    if(!$firstName || !$lastName || !$course){
        echo json_encode(['success' => false, 'message' => "All fields are required"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO studentlist (`First Name`, `Last Name`, `Course`) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $firstName, $lastName, $course);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => "Student added successfully"]);
    } else {
        echo json_encode(['success' => false, 'message' => "Error adding student: " . $stmt->error]);
    }
    $stmt->close();
    exit;
}
if ($action === 'updateStudent') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = isset($data['id']) ? intval($data['id']) : 0;
    $firstName = isset($data['firstName']) ? $data['firstName'] : '';
    $lastName = isset($data['lastName']) ? $data['lastName'] : '';
    $course = isset($data['course']) ? $data['course'] : '';

    if(!$id || !$firstName || !$lastName || !$course){
        echo json_encode(['success' => false, 'message' => "All fields are required"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE studentlist SET `First Name`=?, `Last Name`=?, `Course`=? WHERE id=?");
    $stmt->bind_param("sssi", $firstName, $lastName, $course, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => "Student updated successfully"]);
    } else {
        echo json_encode(['success' => false, 'message' => "Error updating student: " . $stmt->error]);
    }
    $stmt->close();
    exit;
}
if ($action === 'deleteStudent') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = isset($data['id']) ? intval($data['id']) : 0;

    if(!$id){
        echo json_encode(['success' => false, 'message' => "All fields are required"]);
        exit;
    }
    $stmt = $conn->prepare("DELETE FROM studentlist WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => "Student deleted successfully"]);
    } else {
        echo json_encode(['success' => false, 'message' => "Error deleting student: " . $stmt->error]);
    }
    $stmt->close();
    exit;

}
if ($action === 'loadStudents') {
    $result = $conn->query("SELECT id, `First Name` as firstName, `Last Name` as lastName, `Course` as course FROM studentlist");
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    echo json_encode($students);
    exit;
}