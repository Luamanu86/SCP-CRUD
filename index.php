<?php
include "connection.php";

$message = "";

// DELETE RECORD
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete"])) {

    $deleteID = filter_input(INPUT_POST, "delete", FILTER_VALIDATE_INT);

    if (!$deleteID) {
        $message = "Invalid SCP record ID.";
    } else {

        $stmt = $connection->prepare(
            "DELETE FROM scp WHERE id = ?"
        );

        $stmt->bind_param("i", $deleteID);

        if ($stmt->execute()) {
            $message = $stmt->affected_rows > 0
                ? "SCP record deleted successfully."
                : "SCP record not found.";
        } else {
            $message = "Error deleting SCP record.";
        }

        $stmt->close();
    }
}

// LOAD ALL SCP RECORDS
$AllRecords = $connection->query(
    "SELECT * FROM scp ORDER BY Subject"
);

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>SCP Foundation Database</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link href="mystyles.css" rel="stylesheet">
</head>

<body class="container">

    <!-- HEADER -->
    <header class="text-center my-4">

        <img src="images/logo.jpg"
             alt="SCP Foundation Logo"
             class="img-fluid"
             style="max-width: 150px;">

        <h1>SCP Foundation Database</h1>

        <p>
            Select an SCP subject below to view its information.
        </p>

    </header>

    <!-- NAVIGATION -->
    <nav class="navbar navbar-dark bg-dark rounded mb-4">

        <div class="container-fluid">

            <div class="d-flex flex-wrap">

                <?php foreach ($AllRecords as $link): ?>

                    <a
                        href="index.php?link=<?php
                            echo urlencode($link['Subject']);
                        ?>"
                        class="nav-link text-light p-2">

                        <?php
                            echo htmlspecialchars($link['Subject']);
                        ?>

                    </a>

                <?php endforeach; ?>

            </div>

            <a href="create.php"
               class="btn btn-success m-2">

                Add New SCP Record

            </a>

        </div>

    </nav>

    <!-- STATUS MESSAGE -->
    <?php if (!empty($message)): ?>

        <div class="alert alert-info">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>

    <main>

        <h2>SCP Object Information</h2>

        <?php

        if (isset($_GET['link'])) {

            $Subject = $_GET['link'];

            // READ SELECTED SCP
            $stmt = $connection->prepare(
                "SELECT * FROM scp WHERE Subject = ?"
            );

            $stmt->bind_param("s", $Subject);

            if ($stmt->execute()) {

                $result = $stmt->get_result();

                if ($result->num_rows > 0) {

                    $array = $result->fetch_assoc();

                    $id = (int)$array['ID'];

                    $subject = htmlspecialchars(
                        $array['Subject']
                    );

                    $class = htmlspecialchars(
                        $array['Class']
                    );

                    $description = htmlspecialchars(
                        $array['Description']
                    );

                    $image = htmlspecialchars(
                        $array['ImageUrl']
                    );

                    ?>

                    <div class="card shadow mb-4">

                        <div class="card-body">

                            <h2 class="card-title">
                                <?php echo $subject; ?>
                            </h2>

                            <h4>
                                Object Class:
                                <?php echo $class; ?>
                            </h4>

                      <?php if (!empty(trim($array['ImageUrl'] ?? ''))): ?>

                       <div class="text-center my-4">

                          <img
                          src="<?php echo $image; ?>"
                          alt="<?php echo $subject; ?>"
                          class="img-fluid"
                          style="max-height: 350px;">

                       </div>

                      <?php endif; ?>
 
                            <h3>Description:</h3>

                            <p>
                                <?php
                                    echo nl2br($description);
                                ?>
                            </p>
                            
                            <!-- CONTAINMENT INFORMATION -->
                            
                           <h3>Special Containment Procedures:</h3>

                            <p>
                                
                                <?php
                                    echo nl2br(
                                    htmlspecialchars($array['Containment'] ?? '')
                                   );
                                ?>
                            </p>


                            <!-- UPDATE BUTTON -->
                            <a
                                href="update.php?update=<?php echo $id; ?>"
                                class="btn btn-info">

                                Update Record

                            </a>

                            <!-- DELETE FORM -->
                            <form method="POST"
                                  action="index.php"
                                  class="d-inline">

                                <input
                                    type="hidden"
                                    name="delete"
                                    value="<?php echo $id; ?>">

                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this SCP record?');">

                                    Delete Record

                                </button>

                            </form>

                        </div>

                    </div>

                    <?php

                } else {

                    echo "<p>No SCP record found.</p>";

                }

            } else {

                echo "<p>Error loading SCP record.</p>";

            }

            $stmt->close();

        } else {

            ?>

            <div class="card card-body shadow">

                <h3>Welcome to the SCP Foundation Database</h3>

                <p>
                    Select an SCP subject from the navigation
                    menu to view its information.
                </p>

                <p>
                    Administrators can create, update and delete
                    SCP records using this application.
                </p>

            </div>

            <?php

        }

        ?>

    </main>

    <!-- FOOTER -->
    <footer class="text-center my-5 text-muted">

        <p> & copy; 2026 SCP Foundation Archive</p>

    </footer>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>

</body>
</html>