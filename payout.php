<?php
    $pagetitle = "Payment Cashout";
    // Including the dashboards navigation bar
    require_once "Resources/dashboard_nav.php";

    // Include database connection
    require_once "Resources/db_connect.php";

    // Validate amount against wallet balance
    $user_id = $_SESSION['user_id'];
    $wallet_query = "SELECT Balance FROM Wallets WHERE UserID = ?";
    $wallet_stmt = $conn->prepare($wallet_query);
    $wallet_stmt->bind_param("i", $user_id);
    $wallet_stmt->execute();
    $wallet_result = $wallet_stmt->get_result();
    $balrow = $wallet_result->fetch_assoc();
    $balance = $balrow['Balance'];

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $account_id = $_POST['account_id'];
        $amount = $_POST['amount'];
        $user_id = $_SESSION['user_id'];

        // Validate amount against wallet balance
        $wallet_query = "SELECT Balance FROM Wallets WHERE UserID = ?";
        $wallet_stmt = $conn->prepare($wallet_query);
        $wallet_stmt->bind_param("i", $user_id);
        $wallet_stmt->execute();
        $wallet_result = $wallet_stmt->get_result();
        $row = $wallet_result->fetch_assoc();
        $balance = $row['Balance'];

        if ($amount > $balance) {
            echo "Withdrawal amount cannot exceed wallet balance.";
        } else {
            // Insert payout transaction into PayoutHistory table
            $insert_query = "INSERT INTO PayoutHistory (UserID, AccountID, PayoutAmount, PayoutType) VALUES (?, ?, ?, 'pending')";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("iii", $user_id, $account_id, $amount);

            if ($insert_stmt->execute()) {
                echo "Payout transaction initiated successfully.";
            } else {
                echo "Error initiating payout transaction: " . $conn->error;
            }
        }
    }
?>
<div class="container">
    <h1>Cashout</h1>
    <div style="width: 200px">
        <a href='add_bank.php' class="button">Add Bank Account</a>
    </div>
    <form method="post">
        <label for="account_id">Select Bank Account:</label>
        <select name="account_id" id="account_id">
            <?php
            // Retrieve user's bank accounts
            $bank_query = "SELECT RegistrationID, AccountNumber, BankName FROM BankAcount WHERE UserID = ?";
            $bank_stmt = $conn->prepare($bank_query);
            $bank_stmt->bind_param("i", $user_id);
            $bank_stmt->execute();
            $bank_result = $bank_stmt->get_result();
            if($bank_result->num_rows > 0) {
                // Display bank account options
                while ($row = $bank_result->fetch_assoc()) {
                    echo "<option value=\"{$row['RegistrationID']}\">{$row['BankName']} - {$row['AccountNumber']}</option>";
                }
            } else {
                echo "No Account Register a Bank Account: <div style='width: 200px'>
                <a href='add_bank.php' class='button'>Add Bank Account</a>
            </div>";
            }
            ?>
        </select>
        <label for="amount">Enter Amount:</label>
        <input type="number" name="amount" id="amount" step="0.01" min="1" max="<?= $balance?>" required>
       
        <button type="submit">Submit</button>
    </form>
</div>

<!-- Display User Payout History -->
<div class="container">
    <?php
        // Check if user is logged in
        if(isset($_SESSION['user_id'])) {
            // Retrieve user ID from session
            $user_id = $_SESSION['user_id'];

            // Retrieve payment history for the user
            $payment_query = "SELECT PayoutID, PayoutAmount, PayoutDate, PayoutType, BankName, AccountNumber
                            FROM PayoutHistory ph
                            INNER JOIN BankAcount ba ON ph.AccountID = ba.RegistrationID
                            WHERE ph.UserID = ?";
            $payment_stmt = $conn->prepare($payment_query);
            $payment_stmt->bind_param("i", $user_id);
            $payment_stmt->execute();
            $payment_result = $payment_stmt->get_result();
            
            // Check if payments exist
            if ($payment_result->num_rows > 0) {
                // Display payment history
                ?>
                <h2>Payment History</h2>
                <table>
                    <tr>
                        <th>Payment ID</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Bank Name</th>
                        <th>Account Number</th>
                    </tr>
                    <?php
                    while($row = $payment_result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?php echo $row['PayoutID']; ?></td>
                            <td><?php echo $row['PayoutAmount']; ?></td>
                            <td><?php echo $row['PayoutDate']; ?></td>
                            <td><?php echo $row['PayoutType']; ?></td>
                            <td><?php echo $row['BankName']; ?></td>
                            <td><?php echo $row['AccountNumber']; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <?php
            } else {
                echo "<p>No payment history found for user ID: $user_id</p>";
            }
        } else {
            echo "<p>Please log in to view payment history.</p>";
        }

        // Close connection
        $conn->close();
    ?>
</div>