<?php
// Script to create the first Super Admin (CEO) account
// Please delete this file after running it once in your browser for security reasons!

require_once 'includes/koneksi.php';

use Kreait\Firebase\Exception\Auth\EmailExists;

echo "<h2>Setup First Admin</h2>";

$adminData = [
    'email' => 'ceo@swiftsc.com',
    'password' => 'SwiftAdmin2026!',
    'displayName' => 'Super CEO',
];

try {
    // 1. Create in Firebase Auth
    $createdUser = $auth->createUser($adminData);
    $uid = $createdUser->uid;
    echo "Firebase Auth user created with UID: " . $uid . "<br>";

    // 2. Add to Firestore Users Collection
    $database->setDocument('users', $uid, [
        'name' => 'Super CEO',
        'email' => 'ceo@swiftsc.com',
        'role' => 'ceo', // CEO role for highest access
        'role_id' => 1,
        'pool_id' => null, // null means access to all pools
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    echo "Firestore user document created!<br>";
    echo "<strong>Success!</strong> You can now log in using:<br>";
    echo "Email: ceo@swiftsc.com<br>";
    echo "Password: SwiftAdmin2026!<br><br>";
    echo "<a href='login.php'>Go to Login</a>";

} catch (EmailExists $e) {
    echo "<span style='color:red;'>Error: The email ceo@swiftsc.com is already registered in Firebase Auth!</span>";
} catch (\Exception $e) {
    echo "<span style='color:red;'>Error: " . $e->getMessage() . "</span>";
}
?>
