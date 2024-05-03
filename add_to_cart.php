<?php
    $pagetitle = "Add To Cart";
    // Including the dashboards navigation bar
    require_once "Resources/dashboard_nav.php";

    // Include database connection
    require_once "Resources/db_connect.php";

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get product ID and quantity from the form
        $productID = $_POST["product_id"];
        $quantity = $_POST["quantity"];

        // Retrieve product details from the database
        $sql_product = "SELECT * FROM Products WHERE ProductID = ?";
        $stmt_product = $conn->prepare($sql_product);
        $stmt_product->bind_param("i", $productID);
        $stmt_product->execute();
        $result_product = $stmt_product->get_result();

        if ($result_product->num_rows > 0) {
            $row_product = $result_product->fetch_assoc();
            $sellingPrice = $row_product["SellingPrice"];
            $costPrice = $row_product["CostPrice"];
            $subtotal = $sellingPrice * $quantity;
            $profit = ($sellingPrice * $quantity) - ($costPrice * $quantity);
            $orderStatus = "Pending";

            // Insert order details into OrderDetails table
            $sql_insert = "INSERT INTO OrderDetails (OrderID, ProductID, Quantity, SellingPrice, Subtotal, Profit, orderStatus) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            // Assuming order ID 1 for demonstration, you should replace this with actual order ID
            $orderID = $_SESSION['user_id']; 
            $stmt_insert->bind_param("iiiddds", $orderID, $productID, $quantity, $sellingPrice, $subtotal, $profit, $orderStatus);
            if ($stmt_insert->execute()) {
                $_SESSION['order_id'] = $orderID;
                echo "Product added to cart successfully.";
                header("Location: view_cart.php");
            } else {
                echo "Error: " . $stmt_insert->error;
            }
        } else {
            echo "Product not found";
        }
    } else {
        echo "Invalid request";
    }

    // Close connection
    $conn->close();
?>