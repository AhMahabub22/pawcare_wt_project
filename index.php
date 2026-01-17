<?php
$page_title = "Home";
include 'includes/config.php';
?>
<?php include 'includes/header.php'; ?>

<div class="hero">
    <div class="hero-content">
        <h1>Welcome to PawCare</h1>
        <p>Your Smart Pet Care Platform</p>
        <p>Find everything your pet needs in one place</p>
        
        <?php if (!is_logged_in()): ?>
            <div class="hero-buttons">
                <a href="register.php" class="btn">Get Started</a>
                <a href="login.php" class="btn btn-secondary">Login</a>
            </div>
        <?php else: ?>
            <div class="hero-buttons">
                <a href="dashboard.php" class="btn">Go to Dashboard</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="features">
    <div class="feature">
        <i class="fas fa-shopping-cart"></i>
        <h3>Shop Products</h3>
        <p>Find quality pet food, toys, and accessories</p>
    </div>
    <div class="feature">
        <i class="fas fa-paw"></i>
        <h3>Pet Services</h3>
        <p>Book grooming, vet visits, and more</p>
    </div>
    <div class="feature">
        <i class="fas fa-user-shield"></i>
        <h3>Secure Platform</h3>
        <p>Safe and secure transactions</p>
    </div>
</div>

<style>
.hero {
    background: linear-gradient(rgba(74, 111, 165, 0.9), rgba(74, 111, 165, 0.8)), url('https://images.unsplash.com/photo-1518717758536-85ae29035b6d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
    background-size: cover;
    background-position: center;
    color: white;
    text-align: center;
    padding: 100px 20px;
    border-radius: 8px;
    margin-bottom: 50px;
}

.hero h1 {
    font-size: 48px;
    margin-bottom: 20px;
}

.hero p {
    font-size: 20px;
    margin-bottom: 30px;
}

.hero-buttons {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 30px;
}

.hero-buttons .btn {
    padding: 15px 30px;
    font-size: 18px;
}

.features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 50px;
}

.feature {
    text-align: center;
    padding: 30px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.feature i {
    font-size: 48px;
    color: #4a6fa5;
    margin-bottom: 20px;
}

.feature h3 {
    margin-bottom: 15px;
    color: #333;
}
</style>

<?php include 'includes/footer.php'; ?>