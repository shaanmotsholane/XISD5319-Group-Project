<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
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

    <title>Admin Dashboard | Mustbedelah</title>

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

<main class="admin-page">

    <div class="admin-container">

        <div class="admin-page-heading">

            <div>

                <p class="admin-page-label">
                    Administration
                </p>

                <h1>Admin Dashboard</h1>

                <p>
                    Manage products, customer orders, messages and reports.
                </p>

            </div>

        </div>

        <section class="admin-dashboard-grid">

            <a
                href="manage-products.php"
                class="admin-dashboard-card"
            >

                <div class="admin-card-icon">

                    <i class="fa-solid fa-shirt"></i>

                </div>

                <div class="admin-card-content">

                    <h2>Manage Products</h2>

                    <p>
                        View, edit or remove products from the store.
                    </p>

                </div>

                <i class="fa-solid fa-arrow-right admin-card-arrow"></i>

            </a>

            <a
                href="add-product.php"
                class="admin-dashboard-card"
            >

                <div class="admin-card-icon">

                    <i class="fa-solid fa-circle-plus"></i>

                </div>

                <div class="admin-card-content">

                    <h2>Add Product</h2>

                    <p>
                        Add new products, prices, images and stock.
                    </p>

                </div>

                <i class="fa-solid fa-arrow-right admin-card-arrow"></i>

            </a>

            <a
                href="orders.php"
                class="admin-dashboard-card"
            >

                <div class="admin-card-icon">

                    <i class="fa-solid fa-box"></i>

                </div>

                <div class="admin-card-content">

                    <h2>View Orders</h2>

                    <p>
                        Review customer orders and order information.
                    </p>

                </div>

                <i class="fa-solid fa-arrow-right admin-card-arrow"></i>

            </a>

            <a
                href="messages.php"
                class="admin-dashboard-card"
            >

                <div class="admin-card-icon">

                    <i class="fa-solid fa-envelope"></i>

                </div>

                <div class="admin-card-content">

                    <h2>Messages</h2>

                    <p>
                        Read customer enquiries and contact messages.
                    </p>

                </div>

                <i class="fa-solid fa-arrow-right admin-card-arrow"></i>

            </a>

            <a
                href="reports.php"
                class="admin-dashboard-card"
            >

                <div class="admin-card-icon">

                    <i class="fa-solid fa-chart-column"></i>

                </div>

                <div class="admin-card-content">

                    <h2>Reports</h2>

                    <p>
                        View sales, product and order performance.
                    </p>

                </div>

                <i class="fa-solid fa-arrow-right admin-card-arrow"></i>

            </a>

            <a
                href="logout.php"
                class="admin-dashboard-card admin-logout-card"
            >

                <div class="admin-card-icon">

                    <i class="fa-solid fa-right-from-bracket"></i>

                </div>

                <div class="admin-card-content">

                    <h2>Logout</h2>

                    <p>
                        End the current administrator session securely.
                    </p>

                </div>

                <i class="fa-solid fa-arrow-right admin-card-arrow"></i>

            </a>

        </section>

    </div>

</main>

</body>

</html>