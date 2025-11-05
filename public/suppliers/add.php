<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?: '';
    $contact_person = $_POST['contact_person'] ?: null;
    $phone = $_POST['phone'] ?: null;
    $email = $_POST['email'] ?: null;
    $address = $_POST['address'] ?: null;
    $reliability_score = isset($_POST['reliability_score']) ? (float)$_POST['reliability_score'] : 0.0;

    if (empty($name)) {
        $error = "Supplier name is required.";
    } else {
        try {
            add_supplier($pdo, $name, $contact_person, $phone, $email, $address, $reliability_score);
            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
?>

<?php include '../../includes/header.php'; ?>

<main class="main-content">

    <div class="card mb-sm">
        <h2>Add Supplier</h2>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error mb-sm">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" class="form-grid">
            <div class="form-group">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="contact_person">Contact Person</label>
                <input type="text" id="contact_person" name="contact_person">
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email">
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="reliability_score">Reliability Score</label>
                <input type="number" id="reliability_score" name="reliability_score" step="0.01" min="0" max="10" value="0">
            </div>

            <div class="form-actions mt-sm">
                <button type="submit" class="btn btn-primary">Save Supplier</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
