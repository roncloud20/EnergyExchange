
<?php
    $pagetitle = "Payment Processing";
    // Including the dashboards navigation bar
    require_once "Resources/dashboard_nav.php";

    // Check if session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Selecting Location
    $_SESSION['location_id'] = $_POST['location_id'];
    $locationID = $_SESSION['location_id'];

    // Debugging: Display location ID
    // echo "Location ID: " . $locationID . "<br>";

    // Insert new order into Orders table
    $_SESSION['total_amount'] = $_POST['total_amount'];
    $totalAmount = $_SESSION['total_amount'];

    // Debugging: Display total amount
    // echo "Total Amount: " . $totalAmount . "<br>";

    // Insert new totalProfit into Orders table
    $_SESSION['total_profit'] = $_POST['total_profit'];
    $totalProfit = $_SESSION['total_profit'];

    // Debugging: Display total amount
    // echo "Total Profit: " . $totalProfit . "<br>";

    // Insert new uniqueID into Orders table
    $_SESSION['unique_id'] = $_POST['unique_id'];
    $uniqueID = $_SESSION['unique_id'];

    // Debugging: Display unique id
    // echo "UniqueID: " . $uniqueID . "<br>"; 

    // Check if user is logged in
    if(isset($_SESSION['user_id'])) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if(isset($_POST['payment_success'])) {
                // Retrieve user ID from session
                $userID = $_SESSION['user_id'];
                $locationID = $_POST['location_id'];
                $totalAmount = $_POST['total_amount'];
                $totalProfit = $_POST['total_profit'];
                $insertOrderSql = "INSERT INTO Orders (UserID, TotalAmount, LocationID) VALUES (?, ?, ?)";
                $insertOrderStmt = $conn->prepare($insertOrderSql);
                $insertOrderStmt->bind_param("idi", $userID, $totalAmount, $locationID);
                $insertOrderStmt->execute();
                $orderID = $insertOrderStmt->insert_id;

                // Update orderStatus in OrderDetails table
                $updateStatusSql = "UPDATE OrderDetails SET orderStatus = 'Complete' WHERE OrderID = ?";
                $updateStatusStmt = $conn->prepare($updateStatusSql);
                $updateStatusStmt->bind_param("i", $userID);
                $updateStatusStmt->execute();

                // Redirect to success page
                header("Location: view_cart.php");
                exit();
            } elseif (isset($_POST['payment_failed'])) {
                // Process failed payment
                echo "<h2>Payment Failed. Please try again.</h2>";
            }
        } else {
            // Redirect to checkout page if no form submission
            header("Location: checkout.php");
            exit();
        }
?>
        <div class="payment-processing container">
            <h2>Payment Processing</h2>
            <form action="process_payment.php" method="POST">
                <input type="hidden" name="total_amount" value="<?php echo $totalAmount; ?>">
                <input type="hidden" name="location_id" value="<?php echo $locationID; ?>">
                <input type="hidden" name="unique_id" value="<?php echo $uniqueID; ?>">
                <!-- <input type="submit" name="payment_success" value="Successful Payment"/> -->
                <a href="payment_success.php" class="button">Successful Payment</a>
                <button type="submit" name="payment_failed">Failed Payment</button>
            </form>
        </div>
<?php
    } else {
        echo "<p>Please log in to proceed with the payment processing.</p>";
    }
?>