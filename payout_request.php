<?php
    $pagetitle = "All Payout Requests";
    // Including the dashboards navigation bar
    require_once "Resources/dashboard_nav.php";

    // Include database connection
    require_once "Resources/db_connect.php";
    
    // Check if session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if user is logged in and is an admin
    if(isset($_SESSION['user_id']) && isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'admin') {
        // Retrieve pending payout requests
        $payout_query = "SELECT ph.PayoutID, ph.UserID, ph.AccountID, ph.PayoutAmount, ph.PayoutDate, u.FirstName, u.LastName, ba.AccountNumber, ba.BankName
                         FROM PayoutHistory ph
                         INNER JOIN Users u ON ph.UserID = u.UserID
                         INNER JOIN BankAcount ba ON ph.AccountID = ba.RegistrationID
                         WHERE ph.PayoutType = 'pending'";
        $payout_result = $conn->query($payout_query);
    
        // Check if pending payout requests exist
        if ($payout_result->num_rows > 0) {
            ?>
            <h2>Payout Requests</h2>
            <table>
                <tr>
                    <th>Request ID</th>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>Account Number</th>
                    <th>Bank</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                <?php
                while($row = $payout_result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $row['PayoutID']; ?></td>
                        <td><?php echo $row['UserID']; ?></td>
                        <td><?php echo $row['FirstName'] . ' ' . $row['LastName']; ?></td>
                        <td><?php echo $row['PayoutAmount']; ?></td>
                        <td><?php echo $row['AccountNumber']; ?></td>
                        <td><?php echo $row['BankName']; ?></td>
                        <td><?php echo $row['PayoutDate']; ?></td>
                        <td>
                            <form action="process_payout.php" method="POST">
                                <input type="hidden" name="payout_id" value="<?php echo $row['PayoutID']; ?>">
                                <button type="submit" name="approve">Approve</button>
                                <button type="submit" name="decline">Decline</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php
        } else {
            echo "<p>No pending payout requests found.</p>";
        }
    } else {
        echo "<p>You do not have permission to view this page.</p>";
    }
    
    // Close connection
    $conn->close();
?>
