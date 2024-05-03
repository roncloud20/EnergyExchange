<?php
    $pagetitle = "Checkout";
    // Including the dashboards navigation bar
    require_once "Resources/dashboard_nav.php";

    // Include database connection
    require_once "Resources/db_connect.php";

    // Check if session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is logged in
    if(isset($_SESSION['user_id'])) {
        // Fetch user's cart items from the OrderDetails table
        $userID = $_SESSION['user_id'];
        $uniqueID = "$userID" . substr(abs(crc32(uniqid())), -8);
        $sql = "SELECT od.OrderDetailID, od.ProductID, od.Profit, p.ProductName, p.ProductPicture, od.Quantity, p.SellingPrice, od.Subtotal, p.QuantityBalance
                FROM OrderDetails od
                INNER JOIN Products p ON od.ProductID = p.ProductID
                WHERE od.OrderID = ? AND od.orderStatus = 'Pending'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();

        // Display cart items and total amount
        if ($result->num_rows > 0) {
            ?>
            <div class='checkout-items container'>
            <p><a href='delivery_registration.php' class='button' style='display: inline'>Register A Delivery Location</a> <a href='inventory.php' class='button' style='display: inline'>Continue Shopping</a></p>
                <h2>Review Your Order</h2>
                <form action="process_payment.php" method="post">
                    <table>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                        <?php
                        $totalAmount = 0;
                        $totalProfit = 0;
                        while($row = $result->fetch_assoc()) {
                            $totalAmount += $row["Subtotal"];
                            $totalProfit += $row["Profit"];
                            ?>
                            <tr>
                                <td><?php echo $row["ProductName"]; ?></td>
                                <td><?php echo number_format($row["Quantity"], 2); ?></td>
                                <td>$<?php echo number_format($row["SellingPrice"], 2); ?></td>
                                <td>$<?php echo number_format($row["Subtotal"], 2); ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <td colspan="3"><strong>Total:</strong></td>
                            <td>$<?php echo number_format($totalAmount, 2); ?></td>
                            <!-- <td>$<?php // echo number_format($totalProfit, 2); ?></td> -->
                        </tr>
                    </table>
                    <?php
                    // echo "<p>Click <a href='inventory.php' class='button' style='display: inline'>here</a> to continue shopping.</p>";

                    // Fetch user's delivery locations from the DeliveryLocations table
                    $sql_delivery = "SELECT * FROM DeliveryLocations WHERE UserID = ?";
                    $stmt_delivery = $conn->prepare($sql_delivery);
                    $stmt_delivery->bind_param("i", $userID);
                    $stmt_delivery->execute();
                    $result_delivery = $stmt_delivery->get_result();

                    // Display delivery locations
                    if ($result_delivery->num_rows > 0) {
                        ?>
                        <div class='delivery-locations'>
                            <h2>Select Delivery Location</h2>
                            <form action="process_payment.php" method="post">
                                <?php
                                // echo "<a href='delivery_registration.php'>Register Delivery Location</a>";
                                while($row = $result_delivery->fetch_assoc()) {
                                    ?>
                                    <div class="location">
                                        <label>
                                            <input type="radio" name="location_id" value="<?php echo $row['LocationID']; ?>" required>
                                            <strong><?php echo $row["CompanyName"]; ?></strong><br>
                                            <?php echo $row["Address"] . ", " . $row["City"] . ", " . $row["State"] . ", " . $row["Country"] . ", " . $row["PostalCode"]; ?><br>
                                            Contact: <?php echo $row["ContactName"] . " (" . $row["ContactNumber"] . ")"; ?>
                                        </label>
                                    </div>
                                    <?php
                                }
                                ?>
                                <input type="hidden" name="total_amount" value="<?php echo $totalAmount; ?>">
                                <input type="hidden" name="total_profit" value="<?php echo $totalProfit; ?>">
                                <input type="hidden" name="order_id" value="<?php echo $userID; ?>">
                                <input type="hidden" name="unique_id" value="<?php echo $uniqueID; ?>">
                                <button type="submit">Continue to Checkout</button>
                            </form>
                        </div>
                <?php
                
                    } else {
                        echo "<p>No delivery locations found. Please register a delivery location <a href='delivery_registration.php' class='button' style='display: inline'>here</a>.</p>";
                    }    
                ?>
                    
                </form>
            </div>
            <?php
        } else {
            echo "<p>Your cart is empty</p>";
        }
    } else {
        echo "<p>Please log in to proceed to checkout</p>";
    }

    // Close connection
    $conn->close();
?>