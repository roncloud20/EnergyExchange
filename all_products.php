<?php
    // Include database connection
    require_once "Resources/db_connect.php";

    $pagetitle = "All Products";
    // Including the dashboard navigation bar
    require_once "Resources/dashboard_nav.php";

    // Retrieve all products from the database
    $sql = "SELECT * FROM Products";
    $result = $conn->query($sql);
?>
<div class="container">

    <h2>All Products</h2>
    <table>
        <tr>
            <th>Product ID</th>
            <th>Product Image</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Selling Price</th>
            <th>Action</th>
        </tr>
        <?php
    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            ?>
            <tr>
                <td><?php echo $row['ProductID']; ?></td>
                <td><img src="<?php echo $row['ProductPicture']; ?>" alt="Product Image" style="max-width: 100px;"></td>
                <td><?php echo $row['ProductName']; ?></td>
                <td><?php echo $row['Quantity']; ?></td>
                <td><?php echo $row['SellingPrice']; ?></td>
                <td><a href="edit_product.php?product_id=<?php echo $row['ProductID']; ?>" class="button">Edit</a></td>
            </tr>
            <?php
        }
    } else {
        echo "<tr><td colspan='5'>No products found.</td></tr>";
    }
    ?>
</table>
</div>

<?php
    // Close connection
    $conn->close();
?>
