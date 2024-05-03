<?php
    // dashboard.php
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Redirect to the login page if not logged in
        header("Location: signin.php");
        exit();
    }

    // Include your database connection code here (db_connect.php or similar)
    require_once "Resources/db_connect.php";

    // Retrieve user data from the database based on user_id
    $userID = $_SESSION['user_id'];
    $sql = "SELECT * FROM Users WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user data exists
    if ($result->num_rows > 0) {
        // Fetch user data
        $userData = $result->fetch_assoc();
        $firstName = $userData['FirstName'];
        $profile_dp = $userData['ProfilePicture'];
        $userLevel = $userData['UserLevel'];

    } else {
        echo "<p>Error: User data not found</p>";
    }

    // Close the database connection
    $stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="shortcut icon" href="Assets/exchange.png" type="image/x-icon">
    <title><?= $firstName; ?></title>
    <link rel="stylesheet" href="Resources/dashboard.css"/>
</head>
<body>
    <section>
        <nav>
            <img src="Assets\exchangewhite.png" alt="Logo" width="180px" class="logo"/>
            <ul>
                <a href="dashboard.php">Overview</a>
                <a href="inventory.php">Inventory</a>
                <?php if ($_SESSION['user_level'] == 'admin') { ?>
                    <a href="create_product.php">Create Product</a>
                    <a href="payout_request.php">Payout Request</a>
                    <a href="all_transactions.php">All Transaction</a>
                    <a href="all_products.php">View Products</a>
                <?php } ?>
                <a href="transactions.php">Transactions</a>
                <a href="payout.php">Finance</a>
                <a href="network.php">Network</a>
                <a href="delivery_registration.php">Delivery Registration</a>
                <a href="logout_confirmation.php">logout</a>
            </ul>
        </nav>
        <main>
            <header>
                <h1> <?= $pagetitle;?> </h1>
                <div class="content">
                    <a href="view_cart.php">&#x1F6D2;</a>
                    <img src="<?= $profile_dp ?>" alt="Avatar" class="avatar"/>
                    <div>
                        <h4><?=$firstName; ?></h4>
                        <p><?php echo $userID . " " . $userLevel;?></p>
                    </div>
                </div>
            </header>
            
    
