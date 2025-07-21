<?php
require_once 'includes/classroom.php';
require_once 'config/database.php';

$classroom = new Classroom($pdo);


// Add classroom
$classroom->addClassroom('Math 101', 'Algebra and Geometry', 2);
$teachers = $classroom->getAllTeachers();

foreach ($teachers as $teacher) {
    echo "Teacher: " . htmlspecialchars($teacher['name']) . "<br>";
}