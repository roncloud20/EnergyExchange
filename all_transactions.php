<?php
    // Include database connection
    require_once "Resources/db_connect.php";

    $pagetitle = "All Order Details";
    // Including the dashboard navigation bar
    require_once "Resources/dashboard_nav.php";

    // Retrieve complete and pending order details with product names and user names in descending order of OrderDetailID
    $completeOrderDetailSql = "SELECT od.OrderDetailID, od.OrderID, od.ProductID, od.UniqueOrderID, od.Quantity, od.SellingPrice, od.Subtotal, od.Profit, od.orderStatus, p.ProductName, u.FirstName, u.LastName
                               FROM OrderDetails od
                               INNER JOIN Products p ON od.ProductID = p.ProductID
                               INNER JOIN Users u ON od.OrderID = u.UserID
                               WHERE od.orderStatus = 'Complete'
                               ORDER BY od.OrderDetailID DESC";

    $pendingOrderDetailSql = "SELECT od.OrderDetailID, od.OrderID, od.ProductID, od.UniqueOrderID, od.Quantity, od.SellingPrice, od.Subtotal, od.Profit, od.orderStatus, p.ProductName, u.FirstName, u.LastName
                              FROM OrderDetails od
                              INNER JOIN Products p ON od.ProductID = p.ProductID
                              INNER JOIN Users u ON od.OrderID = u.UserID
                              WHERE od.orderStatus = 'Pending'
                              ORDER BY od.OrderDetailID DESC";

    $completeOrderDetailResult = $conn->query($completeOrderDetailSql);

    // Execute the pending order query again
    $pendingOrderDetailResult = $conn->query($pendingOrderDetailSql);

    // Calculate total profit for complete orders
    $totalProfitComplete = 0;
    while ($row = $completeOrderDetailResult->fetch_assoc()) {
        $totalProfitComplete += $row['Profit'];
    }

    // Calculate total profit for pending orders
    $totalProfitPending = 0;
    while ($row = $pendingOrderDetailResult->fetch_assoc()) {
        $totalProfitPending += $row['Profit'];
    }

    // Check if order details exist
    if ($completeOrderDetailResult->num_rows > 0 || $pendingOrderDetailResult->num_rows > 0) {
        ?>
        <h2>All Order Details</h2>
        <div class="accordion container">
            <button class="accordion-btn active">Complete Orders (Total Profit: <?php echo number_format($totalProfitComplete, 2, ".", ","); ?>)</button>
            <div class="accordion-panel active">
                <table>
                    <tr>
                        <!-- <th>Order Detail ID</th>
                        <th>Order ID</th> -->
                        <th>User Name</th>
                        <th>Product Name</th>
                        <th>Unique Order ID</th>
                        <th>Quantity</th>
                        <th>Selling Price</th>
                        <th>Subtotal</th>
                        <th>Profit</th>
                        <th>Status</th>
                        <th>View</th>
                    </tr>
                    <?php
                    // Display each complete order detail
                    $completeOrderDetailResult->data_seek(0); // Reset the result set pointer
                    while($row = $completeOrderDetailResult->fetch_assoc()) {
                        ?>
                        <tr>
                            <!-- <td><?php //echo $row['OrderDetailID']; ?></td>
                            <td><?php //echo $row['OrderID']; ?></td> -->
                            <td><?php echo $row['FirstName'] . ' ' . $row['LastName']; ?></td>
                            <td><?php echo $row['ProductName']; ?></td>
                            <td><?php echo $row['UniqueOrderID']; ?></td>
                            <td><?php echo number_format($row['Quantity'], 2, ".", ","); ?></td>
                            <td><?php echo number_format($row['SellingPrice'], 2, ".", ","); ?></td>
                            <td><?php echo number_format($row['Subtotal'], 2, ".", ","); ?></td>
                            <td><?php echo number_format($row['Profit'], 2, ".", ","); ?></td>
                            <td><?php echo $row['orderStatus']; ?></td>
                            <td><a href="order_details.php?order_id=<?php echo $row['UniqueOrderID']; ?>" class='button'>View Details</a></td>

                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>

            <button class="accordion-btn">Pending Orders (Total Profit: <?php echo number_format($totalProfitPending, 2, ".", ","); ?>)</button>
            <div class="accordion-panel">
                <table>
                    <tr>
                        <!-- <th>Order Detail ID</th>
                        <th>Order ID</th> -->
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
                    // Reset the result set pointer for pending orders
                    $pendingOrderDetailResult->data_seek(0);
                    // Display each pending order detail
                    while($row = $pendingOrderDetailResult->fetch_assoc()) {
                        ?>
                        <tr>
                            <!-- <td><?php //echo $row['OrderDetailID']; ?></td>
                            <td><?php //echo $row['OrderID']; ?></td> -->
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
            </div>
        </div>
        <?php
    } else {
        echo "<p>No order details found.</p>";
    }

    // Close connection
    $conn->close();
?>

<script>
    // Get all accordion buttons
    const accordionBtns = document.querySelectorAll('.accordion-btn');

    // Add click event listener to each accordion button
    accordionBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Toggle active class on button
            btn.classList.toggle('active');

            // Toggle active class on corresponding panel
            const panel = btn.nextElementSibling;
            panel.classList.toggle('active');

            // Toggle accordion functionality
            if (panel.classList.contains('active')) {
                panel.style.maxHeight = panel.scrollHeight + 'px';
            } else {
                panel.style.maxHeight = 0;
            }
        });
    });
</script>

<style>
    .accordion-btn {
        /* background-color: #f4f4f4; */
        background-color: #273a89;
        color:#fff;
        /* color: #333; */
        cursor: pointer;
        padding: 18px;
        width: 100%;
        border: none;
        text-align: left;
        outline: none;
        transition: background-color 0.3s;
    }

    .accordion-btn.active {
        background-color: #ddd;
        color: black;
    }

    .accordion-panel {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }

    .accordion-panel.active {
        /*max-height: 500px; /* Adjust as needed */
    }
</style>
