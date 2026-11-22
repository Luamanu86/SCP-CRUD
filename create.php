<?php

include "connection.php";

$message = "";
$messageType = "";

// CREATE NEW SCP RECORD
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {

    $Subject = trim($_POST['Subject'] ?? '');
    $Class = trim($_POST['Class'] ?? '');
    $ImageUrl = trim($_POST['ImageUrl'] ?? '');
    $Description = trim($_POST['Description'] ?? '');
    $Containment = trim($_POST['Containment'] ?? '');

    if ($Subject === '' || $Class === '' ||
        $Description === '' || $Containment === '') {

        $message = "Please complete all required fields.";
        $messageType = "danger";

    } else {

        // Insert SCP information into MySQL
        $insert = $connection->prepare(
            "INSERT INTO scp
            (Subject, Class, ImageUrl, Description, Containment)
            VALUES (?, ?, ?, ?, ?)"
        );

        if ($insert) {

            $insert->bind_param(
                "sssss",
                $Subject,
                $Class,
                $ImageUrl,
                $Description,
                $Containment
            );

            if ($insert->execute()) {

                $message = "SCP record successfully created!";
                $messageType = "success";

            } else {

                $message = "Unable to create SCP record.";
                $messageType = "danger";

            }

            $insert->close();

        } else {

            $message = "Unable to prepare database statement.";
            $messageType = "danger";

        }
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Create SCP Record - SCP Foundation</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link href="mystyles.css" rel="stylesheet">

</head>

<body class="container">

    <header class="text-center my-4">

        <h1>SCP Foundation Database</h1>

        <h2>Create a New SCP Record</h2>

    </header>

    <p>
        <a href="index.php" class="btn btn-dark">
            Back to SCP Database
        </a>
    </p>

    <!-- SUCCESS OR ERROR MESSAGE -->
    <?php if (!empty($message)): ?>

        <div class="alert alert-<?php echo $messageType; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>

    <!-- CREATE FORM -->
    <div class="p-4 bg-light border shadow rounded">

        <form method="post" action="create.php">

            <!-- SCP SUBJECT -->
            <div class="mb-3">

                <label for="Subject" class="form-label">
                    Enter Object ID:
                </label>

                <input
                    type="text"
                    id="Subject"
                    name="Subject"
                    placeholder="SCP-016"
                    class="form-control"
                    required>

            </div>

            <!-- OBJECT CLASS -->
            <div class="mb-3">

                <label for="Class" class="form-label">
                    Enter Object Class:
                </label>

                <select
                    id="Class"
                    name="Class"
                    class="form-select"
                    required>

                    <option value="">Select Class</option>
                    <option value="Safe">Safe</option>
                    <option value="Euclid">Euclid</option>
                    <option value="Keter">Keter</option>

                </select>

            </div>

            <!-- IMAGE URL -->
            <div class="mb-3">

                <label for="ImageUrl" class="form-label">
                    Enter Image Path:
                </label>

                <input
                    type="text"
                    id="ImageUrl"
                    name="ImageUrl"
                    placeholder="images/scp-016.jpg"
                    class="form-control">

            </div>

            <!-- DESCRIPTION -->
            <div class="mb-3">

                <label for="Description" class="form-label">
                    Enter Description:
                </label>

                <textarea
                    id="Description"
                    name="Description"
                    class="form-control"
                    rows="5"
                    placeholder="Enter SCP description..."
                    required></textarea>

            </div>

            <!-- CONTAINMENT -->
            <div class="mb-3">

                <label for="Containment" class="form-label">
                    Enter Special Containment Procedures:
                </label>

                <textarea
                    id="Containment"
                    name="Containment"
                    class="form-control"
                    rows="5"
                    placeholder="Enter containment procedures..."
                    required></textarea>

            </div>

            <!-- SUBMIT BUTTON -->
            <button
                type="submit"
                name="submit"
                value="1"
                class="btn btn-primary">

                Create SCP Record

            </button>

        </form>

    </div>

    <!-- FOOTER -->
    <footer class="text-center my-5 text-muted">

        <p> & copy; 2026 SCP Foundation Archive</p>

    </footer>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>

</body>
</html>