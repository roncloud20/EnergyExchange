<?php
    $pagetitle = "Network Tree";
    require_once "Resources/dashboard_nav.php";
?>

<style>
.tree-container {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.tree-container .node {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 20px; /* Adjust spacing between nodes */
}

.tree-container .node img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-bottom: 10px; /* Adjust spacing between image and text */
}

.tree-container .node span {
    text-align: center;
}

.tree-container .node ul {
    display: flex;
    justify-content: center;
}

.tree-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    overflow-x: auto;
}

.tree-container .node {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    margin: 10px;
}

.tree-container .node img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-bottom: 5px;
}

.tree-container .node span {
    text-align: center;
    font-size: 0.75rem;
}

.tree-container .node ul {
    display: flex;
    padding: 0;
    position: relative;
    justify-content: center;
}

.tree-container .node ul:after {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    width: 0;
    height: 20px;
    border-left: 2px solid #000;
}

.tree-container .node > ul > li {
    margin: 0 15px;
    list-style-type: none;
}

.tree-container .node > ul > li:not(:first-child):before,
.tree-container .node > ul > li:not(:last-child):after {
    content: '';
    position: absolute;
    top: -20px;
    width: 30px;
    height: 2px;
    background-color: #000;
}

.tree-container .node > ul > li:not(:first-child):before {
    left: 50%;
}

.tree-container .node > ul > li:not(:last-child):after {
    right: 50%;
}



</style>

<!-- <main> -->
    <?php
    // Function to recursively build the network tree
    function buildNetworkTree($conn, $userID)
    {
        $tree = array();
        // Query to fetch the direct children of the current user
        $query = "SELECT UserID, FirstName, LastName, ProfilePicture FROM Users WHERE SponsorID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            // Recursively build the tree for each child
            $row['children'] = buildNetworkTree($conn, $row['UserID']);
            $tree[] = $row;
        }
        return $tree;
    }

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Call the function to build the network tree starting from the root user
    $rootUserID = $_SESSION['user_id']; // Change this to the desired root user ID
    $networkTree = buildNetworkTree($conn, $rootUserID);

    // Close the database connection
    $conn->close();

    // Output the network tree (you can format it as needed)
    $json_data =  json_encode($networkTree);
    ?>

    <div class="tree-container" id="tree-container">
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Sample JSON data (replace with actual JSON data from PHP response)
            const jsonData = <?php echo $json_data; ?>;

            // Function to recursively generate HTML for the tree nodes
            function generateTreeHTML(data) {
                let html = '';
                if (data.length > 0) {
                    html += '<ul>';
                    data.forEach(node => {
                        html += `<li class="node"><img src="${node.ProfilePicture}" alt="${node.FirstName}"><span>${node.FirstName} ${node.LastName}</span>`;
                        html += generateTreeHTML(node.children);
                        html += `</li>`;
                    });
                    html += '</ul>';
                }
                return html;
            }

            // Get the container element
            const container = document.getElementById('tree-container');
            // Generate the HTML for the tree
            container.innerHTML = generateTreeHTML(jsonData);
        });
    </script>
</main>

</section>
</body>
</html>
