<?php

class Classroom {
    private $pdo;

    public function __construct($pdo) {
    $this->pdo = $pdo;
    }

    //Assign Teacher to Classroom


    // Add Classroom
    public function addClassroom($name, $description, $teacher_id) {
        $stmt = $this->pdo->prepare("INSERT INTO classrooms (name, description, teacher_id) VALUES (:name, :description, :teacher_id)");
        $success = $stmt->execute(['name' => $name, 'description' => $description, 'teacher_id' => $teacher_id]);
        if ($success) {
            echo "Classroom added successfully!";
        }
        return $success;
    }

    // List Classrooms
    public function listClassrooms() {
        $stmt = $this->pdo->prepare("SELECT * FROM classrooms");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Edit Classroom
    public function editClassroom($id, $name, $description, $teacher_id) {
        $stmt = $this->pdo->prepare("UPDATE classrooms SET name = :name, description = :description, teacher_id = :teacher_id WHERE id = :id");
        return $stmt->execute(['id' => $id, 'name' => $name, 'description' => $description, 'teacher_id' => $teacher_id]);
    }

    // Delete Classroom
    public function deleteClassroom($id) {
        $stmt = $this->pdo->prepare("DELETE FROM classrooms WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getClassroomById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM classrooms WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTeacherById($teacher_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id AND role_id = 2");
        $stmt->execute(['id' => $teacher_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllTeachers() {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE role_id = 2");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




}