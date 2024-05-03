<?php
    $pagetitle = "View Cart";
    // Including the dashboards navigation bar
    require_once "Resources/dashboard_nav.php";

    // Include database connection
    require_once "Resources/db_connect.php";

    // Check if session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // $sellingPrice = "";
    // Check if user is logged in and has an active order
    if(isset($_SESSION['user_id']) ) {
        $orderID = $userID = $_SESSION['user_id'];
        // $orderID = $_SESSION['order_id'];

        // Check if form is submitted for updating quantity or removing item
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['update_quantity'])) {
                // Update quantity of the item in the cart
                $orderDetailID = $_POST['order_detail_id'];
                $quantity = $_POST['quantity'];
                $costPrice = $_POST['costPrice'];
                

                $updateSql = "UPDATE OrderDetails SET Quantity = ? WHERE OrderDetailID = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("ii", $quantity, $orderDetailID);
                $updateStmt->execute();

                // Recalculate subtotal after updating quantity
                $subtotalSql = "SELECT Quantity, SellingPrice, Subtotal FROM OrderDetails WHERE OrderDetailID = ?";
                $subtotalStmt = $conn->prepare($subtotalSql);
                $subtotalStmt->bind_param("i", $orderDetailID);
                $subtotalStmt->execute();
                $subtotalResult = $subtotalStmt->get_result();

                if ($subtotalResult->num_rows == 1) {
                    $subtotalRow = $subtotalResult->fetch_assoc();
                    $subtotal = $subtotalRow["SellingPrice"] * $subtotalRow["Quantity"];
                    $profit = $subtotal - ($costPrice * $subtotalRow["Quantity"]);

                    // Update subtotal in the database
                    $updateSubtotalSql = "UPDATE OrderDetails SET Subtotal = ?, Profit = ? WHERE OrderDetailID = ?";
                    $updateSubtotalStmt = $conn->prepare($updateSubtotalSql);
                    $updateSubtotalStmt->bind_param("ddi", $subtotal, $profit, $orderDetailID);
                    $updateSubtotalStmt->execute();
                }
            } elseif (isset($_POST['remove_item'])) {
                // Remove item from the cart
                $orderDetailID = $_POST['order_detail_id'];

                $deleteSql = "DELETE FROM OrderDetails WHERE OrderDetailID = ?";
                $deleteStmt = $conn->prepare($deleteSql);
                $deleteStmt->bind_param("i", $orderDetailID);
                $deleteStmt->execute();
            }
        }

        // Query to fetch cart items from OrderDetails table
        $sql = "SELECT od.OrderDetailID, od.ProductID, p.ProductName, p.ProductPicture, od.Quantity, p.SellingPrice, od.Subtotal, p.QuantityBalance, p.CostPrice
                FROM OrderDetails od
                INNER JOIN Products p ON od.ProductID = p.ProductID
                WHERE od.OrderID = ? AND od.orderStatus ='Pending'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $orderID);
        $stmt->execute();
        $result = $stmt->get_result();

        // Display cart items
        if ($result->num_rows > 0) {
?>
            <div class='cart-items container'>
                <h2>Your Cart</h2>
                <?php
                $total = 0; // Initialize total variable

                while($row = $result->fetch_assoc()) {
                    $subtotal = $row["Quantity"] * $row["SellingPrice"]; // Calculate subtotal
                    $costPrice =$row['CostPrice']; // store the costPrice
                ?>
                    <div class='cart-item'>
                        <div class="item-name">
                            <img src='<?php echo $row["ProductPicture"]; ?>' alt='<?php echo $row["ProductName"]; ?>'>
                            <h3><?php echo $row["ProductName"]; ?></h3>
                        </div>
                        <div class='cart-item-content'>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <input type="hidden" name="order_detail_id" value="<?php echo $row["OrderDetailID"]; ?>">
                                <label for="quantity">Quantity:</label>
                                <input type="number" id="quantity" name="quantity" value="<?php echo $row["Quantity"]; ?>" min="1" max="<?= $row["QuantityBalance"]?>" required>
                                <input type="hidden" name="costPrice" value="<?php echo $costPrice; ?>">
                                <button type="submit" name="update_quantity">Update</button>
                            </form>
                            <h3>Subtotal: $<?php echo number_format($subtotal, 2, '.', ','); ?></h3>
                            <?php $sellingPrice = $row["SellingPrice"]; ?>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <input type="hidden" name="order_detail_id" value="<?php echo $row["OrderDetailID"]; ?>">
                                <button type="submit" name="remove_item">Remove &#x1F5D1;</button>
                            </form>
                            
                        </div>
                    </div>
                <?php
                    $total += $subtotal; // Add subtotal to total
                }
                ?>
                <h2>Total: $<?php echo number_format($total, 2, '.', ','); ?></h2>
                <a href="checkout.php" class="button">checkout</a>
                <!-- <a href="delivery_selection.php" class="button">Select Delivery</a> -->
            </div>
<?php
        } else {
            echo "<p>Your cart is empty</p>";
        }
    } else {
        echo "<p>Please log in to view your cart</p>";
    }

    // Close connection
    $conn->close();
?>