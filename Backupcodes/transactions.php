<?php
    // Include database connection
    require_once "Resources/db_connect.php";

    $pagetitle = "Transaction Details";
    // Including the dashboards navigation bar
    require_once "Resources/dashboard_nav.php";

    // Check if session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is logged in
    if(isset($_SESSION['user_id'])) {
        // Retrieve user ID from session
        $userID = $_SESSION['user_id'];

        // Retrieve all completed orders and order details for the user
        $transactionSql = "SELECT DISTINCT o.OrderID, o.OrderDate, od.ProductID, od.Quantity, od.SellingPrice, od.Subtotal, od.Profit, od.orderStatus, p.ProductName, p.ProductPicture
                            FROM Orders o
                            INNER JOIN OrderDetails od ON $userID = od.OrderID
                            INNER JOIN Products p ON od.ProductID = p.ProductID
                            WHERE o.UserID = ? AND od.orderStatus = 'Complete'";
        $transactionStmt = $conn->prepare($transactionSql);
        $transactionStmt->bind_param("i", $userID);
        $transactionStmt->execute();
        $transactionResult = $transactionStmt->get_result();

        // Check if transactions exist
        if ($transactionResult->num_rows > 0) {
            // Display transaction details
            ?>
            <div class="transaction-details">
                <h2>Transaction Details</h2>
                <table>
                    <tr>
                        <th>Order Date</th>
                        <th>Product Name</th>
                        <th>Product Picture</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                        <th>Profit</th>
                        <th>Status</th>
                    </tr>
                    <?php
                    while($row = $transactionResult->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?php echo $row['OrderDate']; ?></td>
                            <td><?php echo $row['ProductName']; ?></td>
                            <td><img src="<?php echo $row['ProductPicture']; ?>" alt="Product Image" style="max-width: 100px;"></td>
                            <td><?php echo number_format($row['Quantity'], 2, ".", ","); ?></td>
                            <td><?php echo number_format($row['SellingPrice'], 2, ".", ","); ?></td>
                            <td><?php echo number_format($row['Subtotal'], 2, ".", ","); ?></td>
                            <td><?php echo number_format($row['Profit'], 2, ".", ","); ?></td>
                            <td><?php echo $row['orderStatus']; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            <?php
        } else {
            echo "<p>No completed transactions found for user ID: $userID</p>";
        }
    } else {
        echo "<p>Please log in to view transaction details.</p>";
    }

    // Close connection
    $conn->close();
?>
