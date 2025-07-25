<?php
session_start();
require_once '../config/database.php';
require_once '../includes/classroom.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header("Location: ../login.php");
    exit;
}

$classroom = new Classroom($pdo);
$teachers = $classroom->getAllTeachers();
$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;
$error = null;
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $teacher_id = $_POST['teacher_id'] ?? null;

    // For edit action, get ID from POST
    if ($action === 'edit') {
        $id = $_POST['id'] ?? null;
    }

    if (!empty($name) && !empty($description) && !empty($teacher_id)) {
        try {
            if ($action === 'edit' && $id) {
                // Update existing classroom
                $classroom->editClassroom($id, $name, $description, $teacher_id);
                header('Location: manage_classroom.php?success=1');
                exit;
            } else {
                // Add new classroom
                $classroom->addClassroom($name, $description, $teacher_id);
                header('Location: manage_classroom.php?success=1');
                exit;
            }
        } catch (Exception $e) {
            $error = "Error adding classroom: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all fields";
    }
}

// Delete classroom
if ($action === 'delete' && $id) {
    try {
        $classroom->deleteClassroom($id);
        header('Location: manage_classroom.php?success=1');
        exit;
    } catch (Exception $e) {
        $error = "Error deleting classroom: " . $e->getMessage();
    }
}

// Check for success message
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classrooms</title>
    <style>
        :root {
            --primary-red: #FF4136;
            --secondary-red: #85144b;
            --primary-yellow: #FFDC00;
            --secondary-yellow: #FFB700;
            --light-yellow: #FFF6E5;
        }

        /* Modal Styles */
        .modal {
            display: none;
            /* Hide it initially */
            position: fixed;
            z-index: 1000;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);

            justify-content: center;
            align-items: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 600px;
            position: relative;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 0;
            /* Remove auto centering, flex handles it */
        }

        .close-modal {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 24px;
            cursor: pointer;
            color: var(--secondary-red);
        }

        .close-modal:hover {
            color: var(--primary-red);
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: var(--light-yellow);
        }

        h2 {
            color: var(--secondary-red);
            border-bottom: 3px solid var(--primary-yellow);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto 30px;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        button[type="submit"] {
            background-color: var(--primary-red);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: var(--secondary-red);
        }

        .error {
            background-color: #FFE5E5;
            color: var(--primary-red);
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .success-message {
            background-color: #DFF2BF;
            color: #4F8A10;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
            max-width: 600px;
            margin: 0 auto 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: var(--primary-red);
            color: white;
        }

        tr:nth-child(even) {
            background-color: var(--light-yellow);
        }

        tr:hover {
            background-color: var(--primary-yellow);
        }

        a {
            color: var(--secondary-red);
            text-decoration: none;
            margin-right: 10px;
            padding: 5px 10px;
            border-radius: 4px;
            transition: all 0.3s;
        }

        a:hover {
            background-color: var(--secondary-red);
            color: white;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
    </style>
    <script>
        function openEditModal(id, name, description, teacherId) {
            const modal = document.getElementById('editModal');
            modal.classList.add('show');
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_teacher_id').value = teacherId;
            document.getElementById('edit_classroom_id').value = id;
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('show');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }
    </script>
</head>
<?php
// Success message display
if ($success) {
    echo '<div class="success-message">Operation completed successfully!</div>';
}

// Main content
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $teacher_id = $_POST['teacher_id'] ?? null;

    // For edit action, get ID from POST
    if ($action === 'edit') {
        $id = $_POST['id'] ?? null;
    }

    if (!empty($name) && !empty($description) && !empty($teacher_id)) {
        try {
            if ($action === 'edit' && $id) {
                // Update existing classroom
                $classroom->editClassroom($id, $name, $description, $teacher_id);
            } else {
                // Add new classroom
                $classroom->addClassroom($name, $description, $teacher_id);
            }
            // Redirect or show success message
            header('Location: manage_classroom.php?success=1');
            exit;
        } catch (Exception $e) {
            $error = "Error adding classroom: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all fields";
    }
}

// Delete classroom
if ($action === 'delete' && $id) {
    try {
        $classroom->deleteClassroom($id);
        header('Location: manage_classroom.php?success=1');
        exit;
    } catch (Exception $e) {
        $error = "Error deleting classroom: " . $e->getMessage();
    }
}
?>

<div class="container">
    <h2><?php echo $action === 'edit' ? 'Edit Classroom' : 'Add New Classroom'; ?></h2>
    <form method="POST">
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="form-group">
            <label for="name">Classroom Name:</label>
            <input type="text" name="name" id="name" placeholder="Enter classroom name" required
                value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" placeholder="Enter classroom description" required><?php
                                                                                                                echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '';
                                                                                                                ?></textarea>
        </div>

        <div class="form-group">
            <label for="teacher">Assign Teacher:</label>
            <select name="teacher_id" id="teacher" required>
                <option value="">Select a teacher</option>
                <?php foreach ($teachers as $teacher): ?>
                    <option value="<?php echo $teacher['id']; ?>"
                        <?php echo (isset($_POST['teacher_id']) && $_POST['teacher_id'] == $teacher['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($teacher['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit"><?php echo $action === 'edit' ? 'Update Classroom' : 'Add Classroom'; ?></button>
    </form>

    <div id="classroomList">
        <h2>All Classrooms</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Teacher</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $classrooms = $classroom->listClassrooms();
                    foreach ($classrooms as $room):
                        $teacher = $classroom->getTeacherById($room['teacher_id']);
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($room['id']); ?></td>
                            <td><?php echo htmlspecialchars($room['name']); ?></td>
                            <td><?php echo htmlspecialchars($room['description']); ?></td>
                            <td><?php echo htmlspecialchars($teacher['name'] ?? 'No teacher assigned'); ?></td>
                            <td>
                                <button onclick="openEditModal('<?php echo $room['id']; ?>', 
                                    '<?php echo htmlspecialchars($room['name'], ENT_QUOTES); ?>', 
                                    '<?php echo htmlspecialchars($room['description'], ENT_QUOTES); ?>', 
                                    '<?php echo $room['teacher_id']; ?>')"
                                    class="btn-edit">Edit</button>
                                <a href="?action=delete&id=<?php echo $room['id']; ?>"
                                    class="btn-delete"
                                    onclick="return confirm('Are you sure you want to delete this classroom?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeEditModal()">&times;</span>
        <h2>Edit Classroom</h2>
        <form method="POST" action="?action=edit">
            <input type="hidden" id="edit_classroom_id" name="id">

            <div class="form-group">
                <label for="edit_name">Classroom Name:</label>
                <input type="text" name="name" id="edit_name" required>
            </div>

            <div class="form-group">
                <label for="edit_description">Description:</label>
                <textarea name="description" id="edit_description" required></textarea>
            </div>

            <div class="form-group">
                <label for="edit_teacher_id">Assign Teacher:</label>
                <select name="teacher_id" id="edit_teacher_id" required>
                    <option value="">Select a teacher</option>
                    <?php foreach ($teachers as $teacher): ?>
                        <option value="<?php echo $teacher['id']; ?>">
                            <?php echo htmlspecialchars($teacher['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit">Update Classroom</button>
        </form>
    </div>
</div>
</body>

</html>