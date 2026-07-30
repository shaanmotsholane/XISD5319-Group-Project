<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="admin-navbar">

    <div class="admin-navbar-container">

        <a href="dashboard.php" class="admin-brand">
            mustbedelah Admin
        </a>

        <button
            type="button"
            class="admin-menu-toggle"
            id="adminMenuToggle"
            aria-label="Open admin navigation"
            aria-expanded="false"
            aria-controls="adminNavLinks"
        >
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="admin-nav-links" id="adminNavLinks">

            <a
                href="dashboard.php"
                class="<?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>"
            >
                Dashboard
            </a>

            <a
                href="manage-products.php"
                class="<?php echo $current_page === 'manage-products.php' ? 'active' : ''; ?>"
            >
                Products
            </a>

            <a
                href="add-product.php"
                class="<?php echo $current_page === 'add-product.php' ? 'active' : ''; ?>"
            >
                Add Product
            </a>

            <a
                href="orders.php"
                class="<?php echo $current_page === 'orders.php' ? 'active' : ''; ?>"
            >
                Orders
            </a>

            <a
                href="messages.php"
                class="<?php echo $current_page === 'messages.php' ? 'active' : ''; ?>"
            >
                Messages
            </a>

            <a
                href="reports.php"
                class="<?php echo $current_page === 'reports.php' ? 'active' : ''; ?>"
            >
                Reports
            </a>

            <a
                href="../index.php"
                target="_blank"
                rel="noopener noreferrer"
            >
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                View Website
            </a>

            <a href="logout.php" class="admin-nav-logout">
                <i class="fa-solid fa-right-from-bracket"></i>
                Logout
            </a>

        </div>

    </div>

</nav>

<script>
const adminMenuToggle = document.getElementById("adminMenuToggle");
const adminNavLinks = document.getElementById("adminNavLinks");

if (adminMenuToggle && adminNavLinks) {
    adminMenuToggle.addEventListener("click", function () {
        const menuIsOpen = adminNavLinks.classList.toggle("show");

        adminMenuToggle.setAttribute(
            "aria-expanded",
            menuIsOpen ? "true" : "false"
        );

        adminMenuToggle.innerHTML = menuIsOpen
            ? '<i class="fa-solid fa-xmark"></i>'
            : '<i class="fa-solid fa-bars"></i>';
    });
}
</script>