<?php

include "connection.php";

$message = "";
$messageType = "";
$row = null;

// GET RECORD ID
$id = filter_input(INPUT_GET, 'update', FILTER_VALIDATE_INT);

if (!$id && isset($_POST['id'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

// UPDATE SCP RECORD
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {

    $Subject = trim($_POST['Subject'] ?? '');
    $Class = trim($_POST['Class'] ?? '');
    $ImageUrl = trim($_POST['ImageUrl'] ?? '');
    $Description = trim($_POST['Description'] ?? '');
    $Containment = trim($_POST['Containment'] ?? '');

    if (!$id) {

        $message = "Invalid SCP record ID.";
        $messageType = "danger";

    } elseif (
        $Subject === '' ||
        $Class === '' ||
        $Description === '' ||
        $Containment === ''
    ) {

        $message = "Please complete all required fields.";
        $messageType = "danger";

    } else {

        $update = $connection->prepare(
            "UPDATE scp
             SET Subject = ?,
                 Class = ?,
                 ImageUrl = ?,
                 Description = ?,
                 Containment = ?
             WHERE id = ?"
        );

        if ($update) {

            $update->bind_param(
                "sssssi",
                $Subject,
                $Class,
                $ImageUrl,
                $Description,
                $Containment,
                $id
            );

            if ($update->execute()) {

                $message = "SCP record updated successfully!";
                $messageType = "success";

            } else {

                $message = "Unable to update SCP record.";
                $messageType = "danger";

            }

            $update->close();

        } else {

            $message = "Unable to prepare update statement.";
            $messageType = "danger";

        }
    }
}

// LOAD EXISTING RECORD
if ($id) {

    $recordID = $connection->prepare(
        "SELECT * FROM scp WHERE id = ?"
    );

    if ($recordID) {

        $recordID->bind_param("i", $id);

        if ($recordID->execute()) {

            $result = $recordID->get_result();
            $row = $result->fetch_assoc();

        }

        $recordID->close();
    }
}

// KEEP ENTERED VALUES IF VALIDATION FAILS
if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    $messageType === "danger"
) {

    $row = [
        'id' => $id,
        'Subject' => $_POST['Subject'] ?? '',
        'Class' => $_POST['Class'] ?? '',
        'ImageUrl' => $_POST['ImageUrl'] ?? '',
        'Description' => $_POST['Description'] ?? '',
        'Containment' => $_POST['Containment'] ?? ''
    ];
}

    function displayValue($value) {
        return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Update SCP Record - SCP Foundation</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link href="mystyles.css" rel="stylesheet">

</head>

<body class="container">

    <!-- HEADER -->
    <header class="text-center my-4">

        <h1>SCP Foundation Database</h1>

        <h2>Update SCP Record</h2>

    </header>

    <!-- BACK BUTTON -->
    <p>
        <a href="index.php" class="btn btn-dark">
            Back to SCP Database
        </a>
    </p>

    <!-- STATUS MESSAGE -->
    <?php if ($message !== ''): ?>

        <div class="alert alert-<?php echo $messageType; ?>">

            <?php echo displayValue($message); ?>

        </div>

    <?php endif; ?>

    <?php if ($row): ?>

        <!-- UPDATE FORM -->
        <div class="p-4 bg-light border shadow rounded">

            <form method="post" action="update.php">

                <input
                    type="hidden"
                    name="id"
                    value="<?php echo displayValue($row['id']); ?>">

                <!-- SUBJECT -->
                <div class="mb-3">

                    <label for="Subject" class="form-label">
                        SCP Object:
                    </label>

                    <input
                        type="text"
                        id="Subject"
                        name="Subject"
                        class="form-control"
                        value="<?php echo displayValue($row['Subject']); ?>"
                        required>

                </div>

                <!-- OBJECT CLASS -->
                <div class="mb-3">

                    <label for="Class" class="form-label">
                        Object Class:
                    </label>

                    <select
                        id="Class"
                        name="Class"
                        class="form-select"
                        required>

                        <option value="">Select Class</option>

                        <?php foreach (['Safe', 'Euclid', 'Keter'] as $classOption): ?>

                            <option
                                value="<?php echo $classOption; ?>"
                                <?php
                                if (($row['Class'] ?? '') === $classOption) {
                                    echo 'selected';
                                }
                                ?>>

                                <?php echo $classOption; ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- IMAGE -->
                <div class="mb-3">

                    <label for="ImageUrl" class="form-label">
                        Image Path:
                    </label>

                    <input
                        type="text"
                        id="ImageUrl"
                        name="ImageUrl"
                        class="form-control"
                        value="<?php echo displayValue($row['ImageUrl']); ?>">

                </div>

                <!-- DESCRIPTION -->
                <div class="mb-3">

                    <label for="Description" class="form-label">
                        Description:
                    </label>

                    <textarea
                        id="Description"
                        name="Description"
                        class="form-control"
                        rows="6"
                        required><?php echo displayValue($row['Description']); ?></textarea>

                </div>

                <!-- CONTAINMENT -->
                <div class="mb-3">

                    <label for="Containment" class="form-label">
                        Special Containment Procedures:
                    </label>

                    <textarea
                        id="Containment"
                        name="Containment"
                        class="form-control"
                        rows="6"
                        required><?php echo displayValue($row['Containment']); ?></textarea>

                </div>

                <!-- UPDATE BUTTON -->
                <button
                    type="submit"
                    name="update"
                    value="1"
                    class="btn btn-primary">

                    Update Record

                </button>

            </form>

        </div>

    <?php else: ?>

        <div class="alert alert-warning">
            SCP record not found. Please select a record
            from the database.
        </div>

    <?php endif; ?>

    <!-- FOOTER -->
    <footer class="text-center my-5 text-muted">

        <p>& copy; 2026 SCP Foundation Archive</p>

    </footer>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>

</body>
</html>