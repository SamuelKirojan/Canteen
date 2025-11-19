<?php
// Redirect if not logged in as doctor (user)
if (!isset($_SESSION['user'])) {
    header("Location: index.php?r=auth/account");
    exit;
}

// ===== Placeholder summary data (replace with DB later) =====
$totalPatients           = 12;
$activeMedications       = 31;
$patientsNeedingAttention = 4;
$updatesToday            = 7;

// Dummy search logic for now (just filters the placeholder array by name)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// ===== Placeholder patients list (this will become real data later) =====
$patients = [
    [ "id" => 1, "name" => "John Doe",      "age" => 42, "allergies" => "Penicillin", "last_update" => "2 hours ago" ],
    [ "id" => 2, "name" => "Maria Santos",  "age" => 31, "allergies" => "None",       "last_update" => "5 hours ago" ],
    [ "id" => 3, "name" => "Adam Hart",     "age" => 64, "allergies" => "Peanuts",    "last_update" => "Today" ],
    [ "id" => 4, "name" => "Clara Lee",     "age" => 27, "allergies" => "Seafood",    "last_update" => "Yesterday" ],
];

// Filter by search (simple name contains)
if ($search !== '') {
    $patients = array_filter($patients, function($p) use ($search) {
        return stripos($p['name'], $search) !== false;
    });
}

// ===== Recently updated patients (pick top 3) =====
$recentPatients = array_slice($patients, 0, 3);

// ===== Placeholder medication activity log =====
$medActivity = [
    [ "patient" => "John Doe",     "action" => "Medication updated",     "time" => "1 hour ago",  "id" => 1 ],
    [ "patient" => "Maria Santos", "action" => "Medication stopped",     "time" => "3 hours ago", "id" => 2 ],
    [ "patient" => "Adam Hart",    "action" => "New medication added",   "time" => "Yesterday",   "id" => 3 ],
];
?>

<div class="container py-4">

    <!-- Doctor Greeting -->
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Welcome, Dr. <?= htmlspecialchars($_SESSION['user']['name']) ?></h2>
        <p class="text-muted mb-0">Here is an overview of your patients and their active medications.</p>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="p-3 bg-light border rounded">
                <h5 class="fw-bold mb-1"><?= $totalPatients ?></h5>
                <small class="text-muted">Total Patients</small>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="p-3 bg-light border rounded">
                <h5 class="fw-bold mb-1"><?= $activeMedications ?></h5>
                <small class="text-muted">Active Medications</small>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="p-3 bg-light border rounded">
                <h5 class="fw-bold mb-1"><?= $patientsNeedingAttention ?></h5>
                <small class="text-muted">Needs Attention</small>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="p-3 bg-light border rounded">
                <h5 class="fw-bold mb-1"><?= $updatesToday ?></h5>
                <small class="text-muted">Updates Today</small>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <form method="GET" action="index.php" class="mb-4">
        <input type="hidden" name="r" value="menu/index">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search patients by name..."
                   value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <div class="row g-4">
        <!-- Left: Recently Updated Patients + Medication Activity -->
        <div class="col-lg-5">
            <!-- Recently Updated Patients -->
            <h4 class="fw-bold mb-3">Recently Updated Patients</h4>
            <?php if (!empty($recentPatients)): ?>
                <div class="list-group mb-4">
                    <?php foreach ($recentPatients as $p): ?>
                        <a href="index.php?r=orders/info&id=<?= $p['id'] ?>" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong><?= htmlspecialchars($p['name']) ?></strong><br>
                                    <small class="text-muted">
                                        Age: <?= (int)$p['age'] ?> &middot; Allergies: <?= htmlspecialchars($p['allergies']) ?>
                                    </small>
                                </div>
                                <small class="text-muted"><?= htmlspecialchars($p['last_update']) ?></small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted mb-4">No patients match your search or no recent updates.</p>
            <?php endif; ?>

            <!-- Medication Activity Log -->
            <h4 class="fw-bold mb-3">Recent Medication Activity</h4>
            <?php if (!empty($medActivity)): ?>
                <div class="list-group mb-4">
                    <?php foreach ($medActivity as $m): ?>
                        <a href="index.php?r=orders/info&id=<?= $m['id'] ?>" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong><?= htmlspecialchars($m['patient']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($m['action']) ?></small>
                                </div>
                                <small class="text-muted"><?= htmlspecialchars($m['time']) ?></small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">No recent medication activity.</p>
            <?php endif; ?>

            <!-- Quick Actions -->
            <div class="d-flex gap-2">
                <a href="index.php?r=orders/update" class="btn btn-primary">Add Medication</a>
            </div>
        </div>

        <!-- Right: Full Patient List -->
        <div class="col-lg-7">
            <h4 class="fw-bold mb-3">All Patients</h4>

            <?php if (!empty($patients)): ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Age</th>
                                <th>Allergies</th>
                                <th>Last Updated</th>
                                <th style="width: 1%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($patients as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['name']) ?></td>
                                    <td><?= (int)$p['age'] ?></td>
                                    <td><?= htmlspecialchars($p['allergies']) ?></td>
                                    <td><?= htmlspecialchars($p['last_update']) ?></td>
                                    <td>
                                        <a href="index.php?r=orders/info&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">No patients found.</p>
            <?php endif; ?>
        </div>
    </div>

</div>
