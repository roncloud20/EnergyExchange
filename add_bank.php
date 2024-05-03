<?php
    $pagetitle = "Add Bank Account";
    // Including the dashboards navigation bar
    require_once "Resources/dashboard_nav.php";

    // Include database connection
    require_once "Resources/db_connect.php";

    // Displaying the full name of the user
    $userID = $_SESSION['user_id'];
    $name_query = "SELECT CONCAT(FirstName, ' ', LastName) AS FullName FROM Users WHERE UserID = ?";
    $name_stmt = $conn->prepare($name_query);
    $name_stmt->bind_param("i", $userID);
    $name_stmt->execute();
    $name_result = $name_stmt->get_result();
    $row = $name_result->fetch_assoc();
    $full_name = $row['FullName'];

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $account_number = $_POST['account_number'];
        $account_name = $_POST['account_name'];
        $bank_name = $_POST['bank_name'];
        $userID = $_SESSION['user_id'];

        // Check if the account number already exists
        $check_query = "SELECT COUNT(*) AS count FROM BankAcount WHERE AccountNumber = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("s", $account_number);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $row = $check_result->fetch_assoc();
        $account_exists = $row['count'] > 0;

        if ($account_exists) {
            echo "Account number already exists. Please use a different account number.";
        } else {
            // Insert bank account record into BankAcount table
            $insert_query = "INSERT INTO BankAcount (UserID, AccountNumber, AccountName, BankName) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("isss", $userID, $account_number, $account_name, $bank_name);
            
            if ($insert_stmt->execute()) {
                echo "Bank account registered successfully.";
            } else {
                echo "Error registering bank account: " . $conn->error;
            }
    
        }
    }
?>
<div class="container">
    <h1>User Bank Account Registration</h1>
    <form action="" method="post">
        <h2 for="account_name">Account Name: <?= $full_name?></h2>
        <input type="text" name="account_number" id="account_number" placeholder="Account Number:" required>
        <input type="hidden" name="account_name" id="account_name" value="<?= $full_name?>" required>
        <input type="text" name="bank_name" id="bank_name" placeholder="Bank Name:" required>
        <button type="submit">Register Account</button>
    </form>
</div>