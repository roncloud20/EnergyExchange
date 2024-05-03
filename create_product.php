<?php
    $pagetitle = "Create New Product";
    require_once "Resources/dashboard_nav.php";
?>

<?php
    // Include database connection
    require_once "Resources/db_connect.php";

    $msg = "";

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get form data
        $productName = $_POST['ProductName'];
        $productPicture = $_FILES['ProductPicture'];
        $quantity = $_POST['Quantity'];
        $unitOfMeasure = $_POST['UnitOfMeasure'];
        $description = $_POST['Description'];
        $costPrice = $_POST['CostPrice'];
        $sellingPrice = $_POST['SellingPrice'];
        $TotalSellingPrice = $_POST['TotalSellingPrice'];
        $category = $_POST['Category'];

        // Check if a file is uploaded
        if (!empty($productPicture['name'])) {
            // Code to handle the uploaded image
            $targetDirectory = "product_dp/";

            // Construct a unique filename based on product name and a unique number
            $uniqueNumber = uniqid();
            $targetFile = $targetDirectory . $productName . '_' . $uniqueNumber . '.' . pathinfo($_FILES['ProductPicture']['name'], PATHINFO_EXTENSION);

            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Validate file type (you can add more validation if needed)
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                $dperr = "Invalid file type. Only JPG, JPEG, and PNG files are allowed.";
                $uploadOk = 0;
            }
        } else {
            // Use default image (brand logo) if no file is uploaded
            $uploadOk = 0;
            $defaultImageURL = "product_dp/default.png";
            $targetFile = $defaultImageURL;
        }


        // Prepare and bind the SQL statement
        $sql = "INSERT INTO Products (ProductName, ProductPicture, Quantity, UnitOfMeasure, Description, CostPrice, SellingPrice, TotalSellingPrice, QuantityBalance, Category) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdssdddds", $productName, $targetFile, $quantity, $unitOfMeasure, $description, $costPrice, $sellingPrice, $TotalSellingPrice, $quantity, $category);

        // Execute the statement
        if ($stmt->execute()) {
            // Check if file upload is successful
            if ($uploadOk == 1) {
                move_uploaded_file($_FILES['ProductPicture']['tmp_name'], $targetFile);
                // $dpURL = $targetFile;
            }
            // Product inserted successfully
            $msg = "<h2>Product inserted successfully.</h2>";
        } else {
            // Error occurred
            echo "<p>Error: " . $conn->error . "</p>";
        }

        // Close the statement
        $stmt->close();
    }

    // Close the database connection
    $conn->close();
?>


<!-- <main> -->
    <div class="container">
        <?= $msg ?>
        <h2>Product Entry Form</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="file" id="image-file" name="ProductPicture" accept="image/x-png, image/jpeg" style="display: none"/>
            <label id="image-label" for="image-file">Upload image</label>
            <script>
                const input_file = document.getElementById('image-file');
                const input_label = document.getElementById('image-label');
                
                const convert_to_base64 = file => new Promise((resolve) => {
                    const file_reader = new FileReader();
                    file_reader.readAsDataURL(file);
                    file_reader.onload = () => resolve(file_reader.result);
                });
                
                input_file.addEventListener('change', async function () {
                    const file = this.files[0];  // Use 'this.files[0]' to directly access the first file
                    const my_image = await convert_to_base64(file);
                    
                    // Set background image
                    input_label.style.backgroundImage = `url(${my_image})`;
                    
                    // Update label text (optional)
                    input_label.innerText = `Image: ${file.name}`;
                });
            </script>

            <input type="text" id="ProductName" name="ProductName" placeholder="Product Name: " required><br>

            <label for="Category">Category:</label><br>
            <select id="Category" name="Category">
                <option value="oil" selected>Oil</option>
                <option value="gas">Gas</option>
            </select>

            <textarea id="Description" name="Description" placeholder="Description: "></textarea><br>

            <input type="number" id="Quantity" name="Quantity" step="0.01" placeholder="Quantity: " onchange="calculateTotalSellingPrice()" required><br>

            <label for="UnitOfMeasure">Unit of Measure:</label><br>
            <select id="UnitOfMeasure" name="UnitOfMeasure" required>
                <option value="L" selected>Liters (L)</option>
                <option value="bbl">Barrel (bbl)</option>
                <option value="scf">Standard Cubic Feet (scf)</option>
                <option value="m3">Cubic Meter (m³)</option>
            </select><br>

            <input type="number" id="CostPrice" name="CostPrice" step="0.01" placeholder="Cost Price: " required><br>

            <input type="number" id="SellingPrice" name="SellingPrice" step="0.01" placeholder="Selling Price" onchange="calculateTotalSellingPrice()" required><br>

            <input type="number" id="TotalSellingPrice" name="TotalSellingPrice" step="0.01" placeholder="Total Selling Price" readonly><br>

            <input type="submit" value="Submit">
        </form>
    </div>
</main>
</section>

<script>
    function calculateTotalSellingPrice() {
        // Get quantity and selling price values
        var quantity = document.getElementById("Quantity").value;
        var sellingPrice = document.getElementById("SellingPrice").value;

        // Calculate total selling price
        var totalSellingPrice = quantity * sellingPrice;

        // Set total selling price value
        document.getElementById("TotalSellingPrice").value = totalSellingPrice.toFixed(2);
    }
</script>