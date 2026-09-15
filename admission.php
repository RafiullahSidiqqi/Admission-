<?php
// admission.php
require_once 'db.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name   = trim($_POST['full_name']   ?? '');
    $father_name = trim($_POST['father_name'] ?? '');
    $email       = trim($_POST['email']       ?? '');
    $phone       = trim($_POST['phone']       ?? '');
    $program     = trim($_POST['program']     ?? '');

    $errors = [];

    if ($full_name === '')   $errors[] = 'Full name is required.';
    if ($father_name === '') $errors[] = "Father's name is required.";
    if ($email === '')       $errors[] = 'Email is required.';
    if ($phone === '')       $errors[] = 'Phone is required.';
    if ($program === '')     $errors[] = 'Program is required.';

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    $allowedPrograms = ['Information Systems', 'Software Engineering', 'Computer Science'];
    if ($program !== '' && !in_array($program, $allowedPrograms, true)) {
        $errors[] = 'Invalid program selected.';
    }

    if (empty($errors)) {
        $sql = "INSERT INTO applications (full_name, father_name, email, phone, program)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param('sssss', $full_name, $father_name, $email, $phone, $program);

            if ($stmt->execute()) {
                $stmt->close();
                header('Location: admission.php?status=success');
                exit;
            } else {
                $message = 'Database error: ' . $stmt->error;
                $messageType = 'danger';
                $stmt->close();
            }
        } else {
            $message = 'Prepare failed: ' . $conn->error;
            $messageType = 'danger';
        }
    } else {
        $message = implode(' ', $errors);
        $messageType = 'danger';
    }
}

if (isset($_GET['status']) && $_GET['status'] === 'success') {
    $message = 'Application submitted successfully!';
    $messageType = 'success';
}

$sql    = "SELECT id, full_name, father_name, email, phone, program
           FROM applications
           ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Admission Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">

    <h1 class="mb-4 text-center">Student Admission Application</h1>

    <?php if ($message !== ''): ?>
        <div class="alert alert-<?= htmlspecialchars($messageType) ?> alert-dismissible fade show">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <form action="admission.php" method="post" novalidate>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="father_name" class="form-label">Father's Name</label>
                        <input type="text" class="form-control" id="father_name" name="father_name" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="program" class="form-label">Program</label>
                    <select class="form-select" id="program" name="program" required>
                        <option value="">-- Select a program --</option>
                        <option value="Information Systems">Information Systems</option>
                        <option value="Software Engineering">Software Engineering</option>
                        <option value="Computer Science">Computer Science</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Submit Application</button>
            </form>
        </div>
    </div>

    <h2 class="mb-3">Submitted Applications</h2>

    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th><th>Full Name</th><th>Father's Name</th>
                    <th>Email</th><th>Phone</th><th>Program</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['father_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['program']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center text-muted">No applications submitted yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
