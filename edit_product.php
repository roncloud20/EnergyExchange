<?php
    // Include database connection
    require_once "Resources/db_connect.php";

    $pagetitle = "Edit Product";
    // Including the dashboard navigation bar
    require_once "Resources/dashboard_nav.php";

    // Check if ProductID is provided
    if(isset($_GET['product_id'])) {
        $product_id = $_GET['product_id'];

        // Query to fetch product details
        $sql = "SELECT * FROM Products WHERE ProductID = $product_id";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            // Product found, fetch its details
            $row = $result->fetch_assoc();
            $product_name = $row['ProductName'];
            $quantity = $row['Quantity'];
            $selling_price = $row['SellingPrice'];
            // Similarly, fetch other fields you want to edit
        } else {
            // Product not found
            echo "Product not found.";
            exit;
        }
    } else {
        // Product ID not provided
        echo "Product ID not provided.";
        exit;
    }

    // Check if form is submitted for updating product
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $product_name = $_POST['product_name'];
        $quantity = $_POST['quantity'];
        $selling_price = $_POST['selling_price'];
        // Sanitize and validate form data (you can add validation logic here)
        
        // Update product details in the database
        $update_sql = "UPDATE Products SET ProductName = ?, Quantity = ?, SellingPrice = ? WHERE ProductID = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sddi", $product_name, $quantity, $selling_price, $product_id);

        if ($update_stmt->execute()) {
            // Product updated successfully
            header("Location: all_products.php");
            exit();
        } else {
            // Error occurred while updating product
            echo "Error updating product. Please try again.";
        }
    }
?>

<div class="container">

    <h2>Edit Product</h2>
    <form method="post">
        <?php echo "<img src='" . $row["ProductPicture"] . "' alt='" . $row["ProductName"] . "'><br>"; ?>
        <label for="product_name">Product Name:</label>
        <input type="text" name="product_name" value="<?php echo $product_name; ?>" required><br>
        
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" value="<?php echo $quantity; ?>" required><br>
        
        <label for="selling_price">Selling Price:</label>
        <input type="number" step="0.01" name="selling_price" value="<?php echo $selling_price; ?>" required><br>
        
        <!-- Add more fields for editing other product details -->
        
        <input type="submit" value="Update Product">
    </form>
</div>

<?php
    // Close connection
    $conn->close();
?>
