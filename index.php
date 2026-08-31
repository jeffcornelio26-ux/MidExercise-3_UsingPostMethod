<?php
session_start();

if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [
        ['id' => 1, 'student_number' => '24174771', 'name' => 'Mark Jarden', 'section' => 'IT3K'],
        ['id' => 2, 'student_number' => '24174972', 'name' => 'Venice Canlas', 'section' => 'IT3K'],
        ['id' => 3, 'student_number' => '24174911', 'name' => 'Jerwin Esparza', 'section' => 'IT3K'],
        ['id' => 4, 'student_number' => '24174757', 'name' => 'Christine Espiritu', 'section' => 'IT3K'],
        ['id' => 5, 'student_number' => '24174661', 'name' => 'Eduard Bagangan', 'section' => 'IT3K'],
        ['id' => 6, 'student_number' => '24174973', 'name' => 'Elmer Cornelio', 'section' => 'IT3K'],
        ['id' => 7, 'student_number' => '24174770', 'name' => 'Bloom Diaz', 'section' => 'IT3K']
    ];
}

if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $action = $_GET['action'];

    if ($action === 'get_users' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        echo json_encode($_SESSION['users']);
        exit;
    }

    if ($action === 'add_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $studentNumber = trim($_POST['student_number'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $section = trim($_POST['section'] ?? '');

        if (!empty($studentNumber) && !empty($name) && !empty($section)) {
            $newId = count($_SESSION['users']) > 0 ? max(array_column($_SESSION['users'], 'id')) + 1 : 1;
            $newUser = [
                'id' => $newId,
                'student_number' => $studentNumber,
                'name' => $name,
                'section' => $section
            ];
            $_SESSION['users'][] = $newUser;
            echo json_encode(['success' => true, 'message' => 'User added successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Student number, name, and section are required.']);
        }
        exit;
    }

    if ($action === 'update_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $studentNumber = trim($_POST['student_number'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $section = trim($_POST['section'] ?? '');

        if (empty($studentNumber) || empty($name) || empty($section)) {
            echo json_encode(['success' => false, 'message' => 'Student number, name, and section are required.']);
            exit;
        }

        $updated = false;
        foreach ($_SESSION['users'] as &$user) {
            if ($user['id'] === $id) {
                $user['student_number'] = $studentNumber;
                $user['name'] = $name;
                $user['section'] = $section;
                $updated = true;
                break;
            }
        }
        unset($user);

        echo json_encode([
            'success' => $updated,
            'message' => $updated ? 'User updated successfully!' : 'User not found.'
        ]);
        exit;
    }

    if ($action === 'delete_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $_SESSION['users'] = array_values(array_filter($_SESSION['users'], function($user) use ($id) {
            return $user['id'] !== $id;
        }));
        echo json_encode(['success' => true, 'message' => 'User deleted successfully!']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple PHP CRUD System</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 700px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { color: #333; text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
         .instructions { background-color: #f8f9fa; border-left: 4px solid #28a745; padding: 12px 15px; margin: 20px 0; border-radius: 4px; }
         .instructions h3 { margin: 0 0 8px; color: #333; }
         .instructions ol { margin: 0; padding-left: 22px; line-height: 1.6; }
         .instructions p { margin: 10px 0 0; color: #666; font-size: 14px; }
         .secondary-btn { background-color: #6c757d; margin-left: 5px; }
         .secondary-btn:hover { background-color: #5a6268; }
         .edit-btn { background-color: #007bff; padding: 5px 10px; margin-right: 5px; }
         .edit-btn:hover { background-color: #0069d9; }
         .action-buttons { white-space: nowrap; }
         .table-wrapper { overflow-x: auto; }
         .search-box { margin: 20px 0 10px; }
         .search-box label { margin-bottom: 5px; }
         #searchStatus { margin: 8px 0 0; color: #666; font-size: 14px; }
         .feedback-box { margin-top: 25px; padding: 15px; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; }
         .feedback-box h3 { margin: 0 0 10px; color: #333; }
         #actionHistory { margin: 0; padding-left: 20px; color: #555; }
         #actionHistory li { margin-bottom: 6px; }
         #actionHistory .empty-history { color: #777; font-style: italic; }
         .action-time { color: #777; font-size: 12px; margin-left: 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f8f9fa; }
        .delete-btn { background-color: #dc3545; padding: 5px 10px; }
        .delete-btn:hover { background-color: #c82333; }
        #message { margin-top: 15px; font-weight: bold; text-align: center; }
    </style>
</head>
<body>

<div class="container">
    <h2>User Management System</h2>

    <section class="instructions" aria-labelledby="instructions-heading">
        <h3 id="instructions-heading">How to use this page</h3>
        <ol>
            <li>Enter the user’s student number in the <strong>Student Number</strong> field.</li>
            <li>Enter the user’s <strong>Name</strong>, and <strong>section.</strong></li>
            <li>Click <strong>Add User</strong> to add the user to the list below.</li>
            <li>To edit a user, click <strong>Edit</strong>. To remove a user, click <strong>Delete</strong> and confirm the action.</li>
        </ol>
    </section>

    <form id="userForm">
        <input type="hidden" id="userId">
        <div class="form-group">
            <label for="student_number">Student Number:</label>
            <input type="text" id="student_number" placeholder="e.g., 24170000" required>
        </div>
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" required>
        </div>
        <div class="form-group">
            <label for="section">Section:</label>
            <input type="text" id="section" value="IT3K" required>
        </div>
        <button type="submit" id="formSubmitButton">Add User</button>
        <button type="button" id="cancelEditButton" class="secondary-btn" style="display: none;">Cancel Edit</button>
    </form>

    <div id="message"></div>

    <h3>User List</h3>
       <div class="search-box">
        <label for="searchInput">Search Users:</label>
        <input type="text" id="searchInput" placeholder="Search by student number, name, or section..." aria-describedby="searchStatus">
        <p id="searchStatus">Showing all users.</p>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Number</th>
                    <th>Name</th>
                    <th>Section</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
            </tbody>
        </table>
    </div>

    <section class="feedback-box" aria-labelledby="feedback-heading">
        <h3 id="feedback-heading">Previous Actions</h3>
        <ul id="actionHistory">
            <li class="empty-history">Actions performed during this visit will appear here.</li>
        </ul>
    </section>
</div>

<script>
    let allUsers = [];
    let actionHistory = [];

    function loadUsers() {
        fetch('?action=get_users')
            .then(response => response.json())
            .then(data => {
                allUsers = data;
                renderUsers();
            })
            .catch(error => console.error('Error loading users:', error));
    }

    function renderUsers() {
        const tbody = document.getElementById('userTableBody');
        const searchText = document.getElementById('searchInput').value.trim().toLowerCase();
        const matchingUsers = allUsers.filter(user => {
            return [user.student_number, user.name, user.section]
                .some(value => (value || '').toLowerCase().includes(searchText));
        });

        tbody.innerHTML = '';

        if (allUsers.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No users found.</td></tr>';
            document.getElementById('searchStatus').textContent = 'No users available.';
            return;
        }

        if (matchingUsers.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No matching users found.</td></tr>';
            document.getElementById('searchStatus').textContent = 'No matching users found.';
            return;
        }

        document.getElementById('searchStatus').textContent =
            searchText ? `Showing ${matchingUsers.length} matching user(s) out of ${allUsers.length}.` : 'Showing all users.';

        matchingUsers.forEach(user => {
            const row = document.createElement('tr');
            [user.id, user.student_number, user.name, user.section].forEach(value => {
                const cell = document.createElement('td');
                cell.textContent = value || '';
                row.appendChild(cell);
            });

            const actionCell = document.createElement('td');
            actionCell.className = 'action-buttons';

            const editButton = document.createElement('button');
            editButton.type = 'button';
            editButton.className = 'edit-btn';
            editButton.textContent = 'Edit';
            editButton.addEventListener('click', () => editUser(user));

            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.className = 'delete-btn';
            deleteButton.textContent = 'Delete';
            deleteButton.addEventListener('click', () => deleteUser(user.id));

            actionCell.appendChild(editButton);
            actionCell.appendChild(deleteButton);
            row.appendChild(actionCell);
            tbody.appendChild(row);
        });
    }

    document.getElementById('searchInput').addEventListener('input', renderUsers);

    function addFeedback(message) {
        actionHistory.unshift({
            message: message,
            time: new Date().toLocaleTimeString()
        });

        actionHistory = actionHistory.slice(0, 3);

        const historyList = document.getElementById('actionHistory');
        historyList.innerHTML = '';

        actionHistory.forEach(action => {
            const item = document.createElement('li');
            item.textContent = action.message;

            const time = document.createElement('span');
            time.className = 'action-time';
            time.textContent = `(${action.time})`;

            item.appendChild(time);
            historyList.appendChild(item);
        });
    }

    document.getElementById('userForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const id = document.getElementById('userId').value;
        const studentNumber = document.getElementById('student_number').value;
        const name = document.getElementById('name').value;
        const section = document.getElementById('section').value;

        const formData = new URLSearchParams();
        formData.append('id', id);
        formData.append('student_number', studentNumber);
        formData.append('name', name);
        formData.append('section', section);

        fetch(`?action=${id ? 'update_user' : 'add_user'}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('message').innerText = data.message;
            document.getElementById('message').style.color = data.success ? 'green' : 'red';
            if (data.success) {
                addFeedback(data.message);
                resetForm();
                loadUsers();
            }
        })
        .catch(error => console.error('Error adding user:', error));
    });

    function editUser(user) {
        document.getElementById('userId').value = user.id;
        document.getElementById('student_number').value = user.student_number || '';
        document.getElementById('name').value = user.name;
        document.getElementById('section').value = user.section;
        document.getElementById('formSubmitButton').textContent = 'Update User';
        document.getElementById('cancelEditButton').style.display = 'inline-block';
        document.getElementById('student_number').focus();
    }

    function resetForm() {
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('section').value = 'IT3K';
        document.getElementById('formSubmitButton').textContent = 'Add User';
        document.getElementById('cancelEditButton').style.display = 'none';
    }

    document.getElementById('cancelEditButton').addEventListener('click', resetForm);

    function deleteUser(id) {
        if (!confirm('Are you sure you want to delete this user?')) return;

        const formData = new URLSearchParams();
        formData.append('id', id);

        fetch('?action=delete_user', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('message').innerText = data.message;
            document.getElementById('message').style.color = data.success ? 'green' : 'red';
            if (data.success) {
                addFeedback(data.message);
            }
            loadUsers();
        })
        .catch(error => console.error('Error deleting user:', error));
    }

    loadUsers();
</script>

</body>
</html>

