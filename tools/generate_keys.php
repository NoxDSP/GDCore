<?php
/**
 * Key Generation Tool
 * Generates secure keys for GDCore configuration
 */

echo "=== GDCore Key Generator ===\n\n";

// Generate SECRET_KEY
echo "SECRET_KEY (for general encryption):\n";
echo bin2hex(random_bytes(32)) . "\n\n";

// Generate password hash for admin
echo "Enter new admin password: ";
$password = trim(fgets(STDIN));

if (strlen($password) >= 6) {
    echo "\nPassword hash for database:\n";
    echo password_hash($password, PASSWORD_BCRYPT) . "\n\n";
    echo "Update your database with:\n";
    echo "UPDATE accounts SET password = '" . password_hash($password, PASSWORD_BCRYPT) . "' WHERE userName = 'admin';\n\n";
} else {
    echo "\nPassword too short (minimum 6 characters)\n\n";
}

// Generate GJP example
echo "To test GJP encoding:\n";
if (strlen($password) >= 6) {
    $gjp = base64_encode($password);
    echo "GJP for '$password': $gjp\n";
}

echo "\n=== Done ===\n";
echo "Copy the SECRET_KEY to your .env file.\n";
echo "Update the admin password in the database.\n";
