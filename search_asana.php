<?php
header("Content-Type: text/html; charset=utf-8");
header("X-Content-Type-Options: nosniff");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "yoga_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['asana_name']) && !empty(trim($_POST['asana_name']))) {
    $asana_name = trim($_POST['asana_name']);
    // Retrieve all columns including 'information'
    $stmt = $conn->prepare("SELECT name, benefits, restrictions, diseases, information, tutorial_link, image FROM asanas WHERE name LIKE ?");

    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $searchTerm = "%" . $asana_name . "%"; // Use LIKE for partial matching
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    // Array of light pastel colors
    $lightColors = [
        '#E3F2FD', // Light Blue
        '#FFEBEE', // Light Pink
        '#E8F5E9', // Light Green
        '#FFF9C4', // Light Yellow
        '#F3E5F5', // Light Purple
        '#FFECB3', // Light Amber
        '#F1F8E9', // Light Lime
        '#FFCDD2', // Light Red
    ];

    if ($result->num_rows > 0) {
        echo "<div class='search-results'>";

        while ($row = $result->fetch_assoc()) {
            // Select a random light color from the array
            $randomColor = $lightColors[array_rand($lightColors)];

            echo "<div class='search-result' style='background-color: {$randomColor}; padding: 20px; margin-bottom: 20px; border-radius: 8px;'>";

            if (!empty($row['image'])) {
                $imageData = base64_encode($row['image']);
                echo "<div class='image-container' style='text-align: center;'>";
                echo "<img src='data:image/jpeg;base64,{$imageData}' alt='Asana Image' style='max-width:300px; height:auto; border-radius:8px;'>";
                echo "</div>";
            } else {
                echo "<p>No image available for this asana.</p>";
            }

            echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";


              // Display the general information
              if (!empty($row['information'])) {
                echo "<div class='section-title'><strong>Information about " . htmlspecialchars($row['name']) . "</strong></div>";
                echo "<p>" . htmlspecialchars($row['information']) . "</p>";
            }
            
            // Display the benefits
            if (!empty($row['benefits'])) {
                echo "<div class='section-title'><strong>Benefits of " . htmlspecialchars($row['name']) . "</strong></div>";
                echo "<p>" . htmlspecialchars($row['benefits']) . "</p>";
            }

            // Display the restrictions
            if (!empty($row['restrictions'])) {
                echo "<div class='section-title'><strong>Restrictions</strong></div>";
                echo "<p><strong>Restrictions:</strong> " . htmlspecialchars($row['restrictions']) . "</p>";
            }

            // Display the diseases that can be cured
            if (!empty($row['diseases'])) {
                echo "<div class='section-title'><strong>Diseases that can be Cured</strong></div>";
                echo "<p>" . htmlspecialchars($row['diseases']) . "</p>";
            }


            // Display the tutorial link if available
            if (!empty($row['tutorial_link'])) {
                echo "<div class='section-title'><strong>Learn How to Do " . htmlspecialchars($row['name']) . "</strong></div>";
                echo "<p>For a detailed tutorial, visit the link below:</p>";
                echo "<a href='" . htmlspecialchars($row['tutorial_link']) . "' class='tutorial-link' target='_blank'>Click here for the tutorial</a>";
            }

            echo "</div>"; // Close search-result div
        }

        echo "</div>"; // Close search-results div
    } else {
        echo "<p>Asana not found.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Please enter an asana name to search.</p>";
}

$conn->close();
?>
