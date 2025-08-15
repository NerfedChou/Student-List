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

function handleAction($studentList, $method, $fields, $successMsg, $errorMsg)
{
    $data = json_decode(file_get_contents('php://input'), true);
    foreach ($fields as $field) {
        if (empty($data[$field])) {
            echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
            exit;
        }
    }
    if (call_user_func_array(
        [$studentList, $method],
        array_map(function ($f) use ($data) {
            return $data[$f];
        }, $fields)
    )) {
        echo json_encode(['success' => true, 'message' => $successMsg]);
    } else {
        echo json_encode(['success' => false, 'message' => $errorMsg]);
    }
    exit;
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
    handleAction($studentList, 'addStudent', ['firstName', 'lastName', 'course'], "Student added successfully", "Error adding student");
}
if ($action === 'updateStudent') {
    handleAction($studentList, 'updateStudent', ['id', 'firstName', 'lastName', 'course'], "Student updated successfully", "Error updating student");
}
if ($action === 'deleteStudent') {
    handleAction($studentList, 'deleteStudent', ['id'], "Student deleted successfully", "Error deleting student");
}

if ($action === 'loadStudents') {
    echo json_encode($studentList->loadStudents());
    exit;
}