<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include '../config/db.php';

/*
|--------------------------------------------------------------------------
| Load customer messages
|--------------------------------------------------------------------------
*/

$messages_sql = "
    SELECT
        message_id,
        name,
        email,
        message,
        created_at
    FROM contact_messages
    ORDER BY created_at DESC
";

$messages_result = mysqli_query($conn, $messages_sql);

if (!$messages_result) {
    die("Unable to load customer messages.");
}

/*
|--------------------------------------------------------------------------
| Store messages and calculate statistics
|--------------------------------------------------------------------------
*/

$messages = [];
$total_messages = 0;
$messages_today = 0;

$today = date('Y-m-d');

while ($message_row = mysqli_fetch_assoc($messages_result)) {
    $messages[] = $message_row;
    $total_messages++;

    $message_date = date(
        'Y-m-d',
        strtotime($message_row['created_at'])
    );

    if ($message_date === $today) {
        $messages_today++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Customer Messages | Mustbedelah</title>

    <link
        rel="icon"
        type="image/jpeg"
        href="../images/logo.jpeg"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

    <link rel="stylesheet" href="admin.css">

</head>

<body>

<?php include 'admin-nav.php'; ?>

<main class="admin-messages-container">

    <div class="admin-page-heading">

        <div>

            <h1>Customer Messages</h1>

            <p>
                View messages submitted through the website contact form.
            </p>

        </div>

    </div>

    <section class="admin-message-stats">

        <article class="admin-stat-card">

            <div class="admin-stat-icon">

                <i class="fa-solid fa-envelope"></i>

            </div>

            <div>

                <span>Total Messages</span>

                <strong>
                    <?php echo number_format($total_messages); ?>
                </strong>

            </div>

        </article>

        <article class="admin-stat-card">

            <div class="admin-stat-icon">

                <i class="fa-solid fa-calendar-day"></i>

            </div>

            <div>

                <span>Messages Today</span>

                <strong>
                    <?php echo number_format($messages_today); ?>
                </strong>

            </div>

        </article>

    </section>

    <?php if (empty($messages)) { ?>

        <section class="admin-empty-state">

            <i class="fa-solid fa-envelope-open"></i>

            <h2>No messages found</h2>

            <p>
                Messages submitted through the contact page will appear here.
            </p>

        </section>

    <?php } else { ?>

        <section class="messages-list">

            <?php foreach ($messages as $customer_message) { ?>

                <article class="message-card">

                    <div class="message-card-header">

                        <div class="message-sender">

                            <div class="message-avatar">

                                <?php
                                $sender_name =
                                    trim($customer_message['name']);

                                echo htmlspecialchars(
                                    strtoupper(
                                        substr($sender_name, 0, 1)
                                    )
                                );
                                ?>

                            </div>

                            <div>

                                <h2>
                                    <?php
                                    echo htmlspecialchars(
                                        $customer_message['name']
                                    );
                                    ?>
                                </h2>

                                <a
                                    href="mailto:<?php
                                        echo htmlspecialchars(
                                            $customer_message['email']
                                        );
                                    ?>"
                                >
                                    <?php
                                    echo htmlspecialchars(
                                        $customer_message['email']
                                    );
                                    ?>
                                </a>

                            </div>

                        </div>

                        <div class="message-date">

                            <strong>
                                <?php
                                echo date(
                                    'd M Y',
                                    strtotime(
                                        $customer_message['created_at']
                                    )
                                );
                                ?>
                            </strong>

                            <span>
                                <?php
                                echo date(
                                    'H:i',
                                    strtotime(
                                        $customer_message['created_at']
                                    )
                                );
                                ?>
                            </span>

                        </div>

                    </div>

                    <div class="message-content">

                        <?php
                        echo nl2br(
                            htmlspecialchars(
                                $customer_message['message']
                            )
                        );
                        ?>

                    </div>

                    <div class="message-card-actions">

                        <a
                            href="mailto:<?php
                                echo htmlspecialchars(
                                    $customer_message['email']
                                );
                            ?>?subject=<?php
                                echo rawurlencode(
                                    'Reply from Mustbedelah'
                                );
                            ?>"
                            class="admin-action-button"
                        >

                            <i class="fa-solid fa-reply"></i>

                            Reply by Email

                        </a>

                    </div>

                </article>

            <?php } ?>

        </section>

    <?php } ?>

</main>

</body>
</html>