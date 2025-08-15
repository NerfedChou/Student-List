let students = [];
let selectedIndex = -1;

function renderTable() {
    const tbody = document.querySelector("#studentTable tbody");
    tbody.innerHTML = "";
    students.forEach((student, index) => {
        let row = `<tr onclick="selectRow(${index})">
            <td>${index + 1}</td>
            <td>${student.firstName}</td>
            <td>${student.lastName}</td>
            <td>${student.course}</td>
        </tr>`;
        tbody.innerHTML += row;
    });
}

function selectRow(index) {
    selectedIndex = index;
    let student = students[index];
    document.getElementById("firstName").value = student.firstName;
    document.getElementById("lastName").value = student.lastName;
    document.getElementById("course").value = student.course;
}

function clearForm() {
    document.getElementById("firstName").value = "";
    document.getElementById("lastName").value = "";
    document.getElementById("course").value = "";
}

function addStudent() {
    let firstName = document.getElementById("firstName").value.trim();
    let lastName = document.getElementById("lastName").value.trim();
    let course = document.getElementById("course").value.trim();
    fetchAction("addStudent", {firstName, lastName, course});
}

function updateStudent() {
    if (selectedIndex === -1) {
        alert("Please select a student to update");
        return;
    }
    let firstName = document.getElementById("firstName").value.trim();
    let lastName = document.getElementById("lastName").value.trim();
    let course = document.getElementById("course").value.trim();
    let id = students[selectedIndex].id;
    fetchAction("updateStudent", {id, firstName, lastName, course}, () => {
        selectedIndex = -1;
    });
}

function deleteStudent() {
    if (selectedIndex === -1) {
        alert("Please select a student to delete");
        return;
    }
    fetchAction("deleteStudent", {id: students[selectedIndex].id}, () => {
    });
}

// Ayaw ranig tanduga
function loadStudents(callback) {
    fetch("backend.php?action=loadStudents")
        .then(response => response.json())
        .then(data => {
            students = data;
            if (callback) callback();
        });
}

function fetchAction(action, data, callback) {
    fetch(`backend.php?action=${action}`, {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loadStudents(() => {
                    renderTable();
                    clearForm();
                    if (callback) callback(result);
                });
            } else {
                alert(result.message || "Error performing action");
            }
        });
}

window.onload = function () {
    loadStudents(renderTable);
};

//oten