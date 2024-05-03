<?php
    // Include database connection
    require_once "Resources/db_connect.php";

    $pagetitle = "Order Details";
    // Including the dashboard navigation bar
    require_once "Resources/dashboard_nav.php";

    // Retrieve order ID from URL parameter
    $orderID = $_GET['order_id'];

    // Fetch order details from the database based on order ID
    $orderDetailSql = "SELECT od.OrderDetailID, od.OrderID, od.ProductID, od.UniqueOrderID, od.Quantity, od.SellingPrice, od.Subtotal, od.Profit, od.orderStatus, p.ProductName, u.FirstName, u.LastName
                       FROM OrderDetails od
                       INNER JOIN Products p ON od.ProductID = p.ProductID
                       INNER JOIN Users u ON od.OrderID = u.UserID
                       WHERE od.UniqueOrderID = $orderID
                       ORDER BY od.OrderDetailID DESC";

    $orderDetailResult = $conn->query($orderDetailSql);

    echo "<div class='container'>";
    // Check if order details exist
    if ($orderDetailResult->num_rows > 0) {
        ?>
        <h2>Order Details for Order ID <?php echo $orderID; ?></h2>
        <table>
            <tr>
                <th>User Name</th>
                <th>Product Name</th>
                <th>Unique Order ID</th>
                <th>Quantity</th>
                <th>Selling Price</th>
                <th>Subtotal</th>
                <th>Profit</th>
                <th>Status</th>
            </tr>
            <?php
            // Display each order detail
            while($row = $orderDetailResult->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo $row['FirstName'] . ' ' . $row['LastName']; ?></td>
                    <td><?php echo $row['ProductName']; ?></td>
                    <td><?php echo $row['UniqueOrderID']; ?></td>
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
        <?php
    } else {
        echo "<p>No order details found for Order ID $orderID.</p>";
    }

    // Fetch delivery location information from the database based on order ID
    $deliveryLocationSql = "SELECT dl.CompanyName, dl.Address, dl.City, dl.State, dl.Country, dl.PostalCode, dl.ContactName, dl.ContactNumber
                            FROM Orders o
                            INNER JOIN DeliveryLocations dl ON o.LocationID = dl.LocationID
                            WHERE o.UniqueOrderID = $orderID";

    $deliveryLocationResult = $conn->query($deliveryLocationSql);

    // Check if delivery location information exists
    if ($deliveryLocationResult->num_rows > 0) {
        $deliveryLocation = $deliveryLocationResult->fetch_assoc();
        ?>
        <h2>Delivery Location Information for Order ID <?php echo $orderID; ?></h2>
        <div class="delivery-info">
            <table>
                <tr>
                    <th>Company Name:</th> 
                    <td><?php echo $deliveryLocation['CompanyName']; ?></td>
                </tr>
                <tr>
                    <th>Address:</th> 
                    <td><?php echo $deliveryLocation['Address']; ?> </td>
                </tr>
                <tr>
                    <th>City:</th> 
                    <td><?php echo $deliveryLocation['City']; ?></td>
                </tr>
                <tr>
                    <th>State:</th> 
                    <td><?php echo $deliveryLocation['State']; ?></td>
                </tr>
                <tr>
                    <th>Country:</th> 
                    <td><?php echo $deliveryLocation['Country']; ?><td>
                </tr>
                <tr>
                    <th>Postal Code:</th> 
                    <td><?php echo $deliveryLocation['PostalCode']; ?></td>
                </tr>
                <tr>
                    <th>Contact Name:</th> 
                    <td><?php echo $deliveryLocation['ContactName']; ?></td>
                </tr>
                <tr>
                    <th>Contact Number:</th> 
                    <td><?php echo $deliveryLocation['ContactNumber']; ?></td>
                </tr>
            </table>
        </div>
        <?php
    } else {
        echo "<p>No delivery location information found for Order ID $orderID.</p>";
    }
    echo "</div>";

    // Close connection
    $conn->close();
?>
