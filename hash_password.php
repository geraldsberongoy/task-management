<?php

$password = "admin123"; // Example password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Output the hashed password
echo "Password: $password<br>";
echo "Hashed Password: $hashedPassword";