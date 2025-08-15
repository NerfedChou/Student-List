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

function addStudent() {
    let firstName = document.getElementById("firstName").value.trim();
    let lastName = document.getElementById("lastName").value.trim();
    let course = document.getElementById("course").value.trim();

    fetch("backend.php?action=addStudent", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ firstName, lastName, course })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            loadStudents(() => {
                renderTable();
                clearForm();
            });
        } else {
            alert("Error adding student");
        }
    });
}

function selectRow(index) {
    selectedIndex = index;
    let student = students[index];
    document.getElementById("firstName").value = student.firstName;
    document.getElementById("lastName").value = student.lastName;
    document.getElementById("course").value = student.course;
}

function updateStudent() {
    if (selectedIndex === -1) {
        alert("Please select a student to update");
        return;
    }

    let firstName = document.getElementById("firstName").value.trim();
    let lastName = document.getElementById("lastName").value.trim();
    let course = document.getElementById("course").value.trim();

    fetch("backend.php?action=updateStudent", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: students[selectedIndex].id, firstName, lastName, course })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            loadStudents(() => {
                renderTable();
                clearForm();
                selectedIndex = -1;
            });
        } else {
            alert("Error updating student");
        }
    });
}

function deleteStudent() {
    if (selectedIndex === -1) {
        alert("Please select a student to delete");
        return;
    }

    fetch("backend.php?action=deleteStudent", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: students[selectedIndex].id })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            loadStudents(() => {
                renderTable();
                clearForm();
                selectedIndex = -1;
            });
        } else {
            alert("Error deleting student");
        }
    });
}

function clearForm() {
    document.getElementById("firstName").value = "";
    document.getElementById("lastName").value = "";
    document.getElementById("course").value = "";
}

function loadStudents(callback) {
    fetch("backend.php?action=loadStudents")
        .then(response => response.json())
        .then(data => {
            students = data;
            if (callback) callback();
        });
}

window.onload = function() {
    loadStudents(renderTable);
};