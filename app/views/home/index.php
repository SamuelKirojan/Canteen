<?php
// Ensure doctor is logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php?r=auth/account");
    exit;
}

// Temporary placeholders until your DB is ready
// Later you will replace these with real queries.
$totalPatients = 12;
$activeMeds = 31;
$patientsNeedingAttention = 4;
$updatesToday = 7;

// Example recent patients list (placeholder)
$recentPatients = [
    [ "name" => "John Doe", "age" => 42, "updated" => "2 hours ago", "id" => 1 ],
    [ "name" => "Maria Santos", "age" => 31, "updated" => "5 hours ago", "id" => 2 ],
    [ "name" => "Adam Hart", "age" => 64, "updated" => "Today", "id" => 3 ],
];

// Example medication activity log
$medActivity = [
    [ "patient" => "John Doe", "action" => "Medication updated", "time" => "1 hour ago", "id" => 1 ],
    [ "patient" => "Maria Santos", "action" => "Medication stopped", "time" => "3 hours ago", "id" => 2 ],
    [ "patient" => "Adam Hart", "action" => "New medication added", "time" => "Yesterday", "id" => 3 ],
];
?>

<div class="container py-4">

    <!-- Doctor Greeting -->
    <h2 class="fw-bold mb-3">Welcome, Dr. <?= htmlspecialchars($_SESSION['user']['name']) ?></h2>
    <p class="text-muted mb-4">Here is your medical activity summary.</p>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="p-3 bg-light border rounded">
                <h5 class="fw-bold mb-1"><?= $totalPatients ?></h5>
                <small class="text-muted">Total Patients</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-3 bg-light border rounded">
                <h5 class="fw-bold mb-1"><?= $activeMeds ?></h5>
                <small class="text-muted">Active Medications</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-3 bg-light border rounded">
                <h5 class="fw-bold mb-1"><?= $patientsNeedingAttention ?></h5>
                <small class="text-muted">Needs Attention</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-3 bg-light border rounded">
                <h5 class="fw-bold mb-1"><?= $updatesToday ?></h5>
                <small class="text-muted">Updates Today</small>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <form method="GET" action="index.php">
        <input type="hidden" name="r" value="menu/index">
        <div class="input-group mb-4">
            <input type="text" name="search" class="form-control" placeholder="Search patients by name or ID...">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <!-- Recently Updated Patients -->
    <h4 class="fw-bold mb-3">Recently Updated Patients</h4>
    <div class="list-group mb-4">
        <?php foreach ($recentPatients as $p): ?>
            <a href="index.php?r=orders/info&id=<?= $p['id'] ?>" class="list-group-item list-group-item-action">
                <div class="d-flex justify-content-between">
                    <div>
                        <strong><?= htmlspecialchars($p['name']) ?></strong><br>
                        <small class="text-muted">Age: <?= $p['age'] ?></small>
                    </div>
                    <small class="text-muted"><?= $p['updated'] ?></small>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Medication Activity Log -->
    <h4 class="fw-bold mb-3">Recent Medication Activity</h4>
    <div class="list-group mb-4">
        <?php foreach ($medActivity as $m): ?>
            <a href="index.php?r=orders/info&id=<?= $m['id'] ?>" class="list-group-item list-group-item-action">
                <div class="d-flex justify-content-between">
                    <div>
                        <strong><?= htmlspecialchars($m['patient']) ?></strong><br>
                        <small class="text-muted"><?= $m['action'] ?></small>
                    </div>
                    <small class="text-muted"><?= $m['time'] ?></small>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Buttons -->
    <div class="d-flex gap-2">
        <a href="index.php?r=menu/index" class="btn btn-secondary">View All Patients</a>
        <a href="index.php?r=orders/update" class="btn btn-primary">Add Medication</a>
    </div>

</div>
