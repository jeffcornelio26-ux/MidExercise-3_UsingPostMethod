<?php
// Get today's date and day name
$today = date("l, F j, Y"); // Example: "Tuesday, August 25, 2026"

// Check if BOTH name and city are set using GET method
if (isset($_GET['name'], $_GET['city'])) {
    
    // Safe way to get values from URL
    $user_name = htmlspecialchars($_GET['name']);
    $user_city = htmlspecialchars($_GET['city']);

    // Show personalized message
    echo "Welcome to our System, " . $user_name . "!<br>";
    echo "Today is " . $today . "<br>";
    echo "We hope you are having a great day in " . $user_city . "!";

} elseif (isset($_GET['name'])) {
    
    // Only name was given — ask for city too
    echo "Hello, " . htmlspecialchars($_GET['Name']) . "!<br>";
    echo "Please tell us your city too.";

} else {
    
    // Nothing was entered — show form or instructions
    echo "Welcome! Please enter your details to continue.";
    echo "<br>Example: welcome_system.php?name=Elmer&city=Muntinlupa City";
}
?>

<hr>
<h3>Quick Test Links:</h3>
<a href="welcome_system.php">No information</a><br>
<a href="welcome_system.php?name=Proffessor Pearl">Name only</a><br>
<a href="welcome_system.php?name=Proffessor Pearl&city=Muntinlupa City">Name and City</a><br>
<a href="welcome_system.php?name=Proffessor Pearl&city=Muntinlupa City">Name and City (Muntinlupa)</a>