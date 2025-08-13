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

    if (!firstName || !lastName || !course) {
        alert("Please fill in all fields");
        return;
    }

    students.push({ firstName, lastName, course });
    clearForm();
    renderTable();
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

    students[selectedIndex].firstName = document.getElementById("firstName").value.trim();
    students[selectedIndex].lastName = document.getElementById("lastName").value.trim();
    students[selectedIndex].course = document.getElementById("course").value.trim();

    clearForm();
    renderTable();
    selectedIndex = -1;
}

function deleteStudent() {
    if (selectedIndex === -1) {
        alert("Please select a student to delete");
        return;
    }
    students.splice(selectedIndex, 1);
    clearForm();
    renderTable();
    selectedIndex = -1;
}

function clearForm() {
    document.getElementById("firstName").value = "";
    document.getElementById("lastName").value = "";
    document.getElementById("course").value = "";
}