<?php

class StudentList
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function addStudent($firstName, $lastName, $course)
    {
        $stmt = $this->conn->prepare("INSERT INTO studentlist (`First Name`, `Last Name`, `Course`) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $firstName, $lastName, $course);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function updateStudent($id, $firstName, $lastName, $course)
    {
        $stmt = $this->conn->prepare("UPDATE studentlist SET `First Name`=?, `Last Name`=?, `Course`=? WHERE id=?");
        $stmt->bind_param("sssi", $firstName, $lastName, $course, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function deleteStudent($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM studentlist WHERE id=?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function loadStudents()
    {
        $result = $this->conn->query("SELECT id, `First Name` as firstName, `Last Name` as lastName, `Course` as course FROM studentlist");
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        return $students;
    }
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "jan");
if (!$conn) {
    error_log("Connection failed: " . mysqli_connect_error());
    die(json_encode(['success' => false, 'message' => "Database connection error"]));
}

$studentList = new StudentList($conn);
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'addStudent') {
    $data = json_decode(file_get_contents('php://input'), true);
    if ($studentList->addStudent($data['firstName'], $data['lastName'], $data['course'])) {
        echo json_encode(['success' => true, 'message' => "Student added successfully"]);
    } else {
        echo json_encode(['success' => false, 'message' => "Error adding student"]);
    }
    exit;
}

if ($action === 'updateStudent') {
    $data = json_decode(file_get_contents('php://input'), true);
    if ($studentList->updateStudent($data['id'], $data['firstName'], $data['lastName'], $data['course'])) {
        echo json_encode(['success' => true, 'message' => "Student updated successfully"]);
    } else {
        echo json_encode(['success' => false, 'message' => "Error updating student"]);
    }
    exit;
}

if ($action === 'deleteStudent') {
    $data = json_decode(file_get_contents('php://input'), true);
    if ($studentList->deleteStudent($data['id'])) {
        echo json_encode(['success' => true, 'message' => "Student deleted successfully"]);
    } else {
        echo json_encode(['success' => false, 'message' => "Error deleting student"]);
    }
    exit;
}

if ($action === 'loadStudents') {
    echo json_encode($studentList->loadStudents());
    exit;
}
?>