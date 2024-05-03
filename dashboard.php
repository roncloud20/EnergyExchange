<?php
    $pagetitle = "Dashboard Overview";
    require_once "Resources/dashboard_nav.php";

    $totalPersonalCom = 0; // Initialize total variable
    $totalDownlineCommission = 0; // Initialize total variable
    $totalPendingPayment = 0; // Initialize total variable
    $totalApprovedPayment = 0; // Initialize total variable
    $totalFailedPayment = 0; // Initialize total variable


    // Retrieve user wallet from the database based on user_id
    $userID = $_SESSION['user_id'];
    $walletsql = "SELECT * FROM wallets WHERE UserID = ?";
    $walletstmt = $conn->prepare($walletsql);
    $walletstmt->bind_param("i", $userID);
    $walletstmt->execute();
    $walletresult = $walletstmt->get_result();

    // Check if user data exists
    if ($walletresult->num_rows > 0) {
        // Fetch user data
        $userWallet = $walletresult->fetch_assoc();
        $balance = $userWallet['Balance'];
    } else {
        echo "<p>Error: User data not found</p>";
    }
?>
        
            <h1>Quick Balance</h1>
            <div class="allbal">
                <!-- Wallet Balance -->
                <section class="balance">
                    <?php
                    echo "<h2>Wallet Balance</h2>";
                    echo "<h3>$" . number_format($balance, 2,".", ",") . "</h3>";
                ?>
                </section>

                <!-- Personal Commision -->
                <section class="balance">
                    <h2>Personal Sales</h2>
                    <?php
                        $personalCom = "SELECT * FROM commissions WHERE UserID = ? AND CommissionType = 'personal_sales'";
                        $personalComstmt = $conn->prepare($personalCom);
                        $personalComstmt->bind_param("i", $userID);
                        $personalComstmt->execute();
                        $personComResult = $personalComstmt->get_result();
                    
                        // Check if user data exists
                        if ($personComResult->num_rows > 0) {
                            $totalPersonalCom = 0; // Initialize total variable
                            while($row = $personComResult->fetch_assoc()) {
                                $totalPersonalCom += $row["Amount"];
                            }
                        }
                    ?>
                    <h3> $<?php echo number_format($totalPersonalCom, 2, '.', ','); ?></h3>
                </section>

                <!-- Downline Commission -->
                <section class="balance">
                    <h2>Downline Sales </h2>
                    <?php
                        $downlineCommission = "SELECT * FROM commissions WHERE UserID = ? AND CommissionType = 'override_bonus'";
                        $downlineCommissionStmt = $conn->prepare($downlineCommission);
                        $downlineCommissionStmt->bind_param("i", $userID);
                        $downlineCommissionStmt->execute();
                        $downlineCommissionResult = $downlineCommissionStmt->get_result();
                    
                        // Check if user data exists
                        if ($downlineCommissionResult->num_rows > 0) {
                            $totalDownlineCommission = 0; // Initialize total variable
                            while($row = $downlineCommissionResult->fetch_assoc()) {
                                $totalDownlineCommission += $row["Amount"];
                            }
                        }
                        ?>
                    <h3> $<?php echo number_format($totalDownlineCommission, 2, '.', ','); ?></h3>
                </section>

                <!-- Pending Payment -->
                <section class="balance">
                    <h2>Pending Payment</h2>
                    <?php
                        $pendingPayment = "SELECT * FROM PayoutHistory WHERE UserID = ? AND PayoutType = 'pending'";
                        $pendingPaymentStmt = $conn->prepare($pendingPayment);
                        $pendingPaymentStmt->bind_param("i", $userID);
                        $pendingPaymentStmt->execute();
                        $pendingPaymentResult = $pendingPaymentStmt->get_result();
                    
                        // Check if user data exists
                        if ($pendingPaymentResult->num_rows > 0) {
                            $totalPendingPayment = 0; // Initialize total variable
                            while($row = $pendingPaymentResult->fetch_assoc()) {
                                $totalPendingPayment += $row["PayoutAmount"];
                            }
                        }
                        ?>
                    <h3> $<?php echo number_format($totalPendingPayment, 2, '.', ','); ?></h3>
                </section>

                <!-- Approved Payment -->
                <section class="balance">
                    <h2>Approved Payment</h2>
                    <?php
                        $approvedPayment = "SELECT * FROM PayoutHistory WHERE UserID = ? AND PayoutType = 'approved'";
                        $approvedPaymentStmt = $conn->prepare($approvedPayment);
                        $approvedPaymentStmt->bind_param("i", $userID);
                        $approvedPaymentStmt->execute();
                        $approvedPaymentResult = $approvedPaymentStmt->get_result();
                    
                        // Check if user data exists
                        if ($approvedPaymentResult->num_rows > 0) {
                            $totalApprovedPayment = 0; // Initialize total variable
                            while($row = $approvedPaymentResult->fetch_assoc()) {
                                $totalApprovedPayment += $row["PayoutAmount"];
                            }
                        }
                        ?>
                    <h3> $<?php echo number_format($totalApprovedPayment, 2, '.', ','); ?></h3>
                </section>
                
                <!-- Approved Payment -->
                <section class="balance">
                    <h2>Failed Payment</h2>
                    <?php
                        $failedPayment = "SELECT * FROM PayoutHistory WHERE UserID = ? AND PayoutType = 'failed'";
                        $failedPaymentStmt = $conn->prepare($failedPayment);
                        $failedPaymentStmt->bind_param("i", $userID);
                        $failedPaymentStmt->execute();
                        $failedPaymentResult = $failedPaymentStmt->get_result();
                    
                        // Check if user data exists
                        if ($failedPaymentResult->num_rows > 0) {
                            $totalFailedPayment = 0; // Initialize total variable
                            while($row = $failedPaymentResult->fetch_assoc()) {
                                $totalFailedPayment += $row["PayoutAmount"];
                            }
                        }
                        ?>
                    <h3> $<?php echo number_format($totalFailedPayment, 2, '.', ','); ?></h3>
                </section>
            </div>
        <div class="section2">

            <div class="chart">
                <?php
                // Fetch daily sales commissions for the logged-in user
                $query = "SELECT DATE(CommissionDate) AS CommissionDay, SUM(Amount) AS TotalAmount
                        FROM Commissions
                        WHERE UserID = ?
                        GROUP BY DATE(CommissionDate)
                        ORDER BY DATE(CommissionDate) ASC";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $userID);
                $stmt->execute();
                $result = $stmt->get_result();

                $xValues = [];
                $yValues = [];
                
                // Organize data for the chart
                while ($row = $result->fetch_assoc()) {
                    $xValues[] = $row['CommissionDay'];
                    $yValues[] = $row['TotalAmount'];
                }
                ?>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

                <canvas id="myChart" style="width:100%;max-width:600px"></canvas>
                <script>
                    var xValues = <?php echo json_encode($xValues); ?>;
                    var yValues = <?php echo json_encode($yValues); ?>;
                    // var barColors = ["red", "green","blue","orange","brown"];
                    var barColors = "#273A89";
                    
                    new Chart("myChart", {
                        type: "bar",
                        data: {
                            labels: xValues,
                            datasets: [{
                                backgroundColor: barColors,
                                data: yValues
                            }]
                        },
                        options: {
                            legend: {display: false},
                            title: {
                                display: true,
                                text: "Total Daily Sales"
                            }
                        }
                    });
                </script>
            </div>
            <div class="downlineSales">
                <?php
                // Fetch downlines associated with the user
                $downlinesSql = "SELECT u.UserID, u.FirstName, u.LastName, u.ProfilePicture, COUNT(c.UserID) AS PersonalSalesCount
                FROM Users u
                LEFT JOIN MLMStructure m ON u.UserID = m.UserID
                LEFT JOIN Commissions c ON u.UserID = c.UserID AND c.CommissionType = 'personal_sales'
                WHERE m.SponsorID = ?
                GROUP BY u.UserID";
                $downlinesStmt = $conn->prepare($downlinesSql);
                $downlinesStmt->bind_param("i", $userID);
                $downlinesStmt->execute();
                $downlinesResult = $downlinesStmt->get_result();
                
                // Check if downlines exist
                if ($downlinesResult->num_rows > 0) {
                    ?>
                <div class="downlines-section">
                    <h2>Downlines Personal Sales</h2>
                    <div class="downlines-container">
                        <?php
                        while($row = $downlinesResult->fetch_assoc()) {
                            ?>
                            <div class="downline-item">
                                <img src="<?php echo $row['ProfilePicture']; ?>" alt="Profile Picture" class="avatar">
                                <div class="downline-details">
                                    <h3><?php echo $row['FirstName'] . ' ' . $row['LastName']; ?></h3>
                                    <h3><?php echo "User ID: " . $row['UserID']; ?></h3>
                                </div>
                                <h3 class="count"><?php echo $row['PersonalSalesCount']; ?></h3>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
                } else {
                    echo "<p>No downlines found for user ID: $userID</p>";
                }
                
                // Close connection
                $conn->close();
                ?>
            </div>
        </div>
    </main>
</section>
</body>
</html>

<style>
    .balance {
        background-color: #fff;
        border-radius: 20px;
        border: 1px solid black;
        display: flex;
        margin: 30px;
        padding: 10px;
    }

    .section2 {
        display: flex;
        flex-direction: row wrap;
        padding: 0 20px;
        justify-content: space-between;
    }
    .chart, .downlineSales {
        background-color: white;
        border-radius: 20px;
        padding: 20px;
    }

    .downline-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 2px solid black;
    }

    .downline-item .count {
        background-color: #00B5FF;
        padding: 3px 5px;
        text-align: center;
        color: white;
    }
</style>