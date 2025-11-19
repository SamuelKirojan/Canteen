<?php
// Doctor Dashboard + Patient List

if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

// Redirect if not logged in as doctor
if (empty($_SESSION['user_id'])) {
    header('Location: index.php?r=auth/account&t=login');
    exit;
}

require_once APP_ROOT . '/app/core/Database.php';

$pdo = Database::getInstance();

// ---------------------------------------------------------------------
// 1. SUMMARY COUNTS
// ---------------------------------------------------------------------

// Total patients
$totalPatients = (int)$pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();

// Active medications
$activeMedications = (int)$pdo->query(
    "SELECT COUNT(*) FROM medications WHERE status = 'active'"
)->fetchColumn();

// Patients needing attention (example rule:
// active meds whose end_date is within 2 days or already passed)
$patientsNeedingAttention = (int)$pdo->query("
    SELECT COUNT(DISTINCT patient_id)
    FROM medications
    WHERE status = 'active'
      AND end_date IS NOT NULL
      AND end_date <= DATE_ADD(CURDATE(), INTERVAL 2 DAY)
")->fetchColumn();

// Updates today (any medication touched today)
$updatesToday = (int)$pdo->query("
    SELECT COUNT(*)
    FROM medications
    WHERE DATE(COALESCE(updated_at, created_at)) = CURDATE()
")->fetchColumn();

// ---------------------------------------------------------------------
// 2. SEARCH HANDLING
// ---------------------------------------------------------------------
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where  = '1';
$params = [];

if ($search !== '') {
    $where          = 'p.name LIKE :search';
    $params[':search'] = '%' . $search . '%';
}

// ---------------------------------------------------------------------
// 3. PATIENT LIST (WITH LAST UPDATE)
// ---------------------------------------------------------------------
// last_update = latest of patient.updated_at OR any medication.updated_at/created_at

$sqlPatients = "
    SELECT 
        p.id,
        p.name,
        p.age,
        p.allergies,
        DATE_FORMAT(
            GREATEST(
                COALESCE(p.updated_at, p.created_at),
                COALESCE(MAX(m.updated_at), MAX(m.created_at), p.created_at)
            ),
            '%Y-%m-%d %H:%i'
        ) AS last_update
    FROM patients p
    LEFT JOIN medications m ON m.patient_id = p.id
    WHERE $where
    GROUP BY 
        p.id, p.name, p.age, p.allergies, p.created_at, p.updated_at
    ORDER BY last_update DESC
";

$stmtPatients = $pdo->prepare($sqlPatients);
$stmtPatients->execute($params);
$patients = $stmtPatients->fetchAll(PDO::FETCH_ASSOC);

// Recently updated = top 3 from this ordered list
$recentPatients = array_slice($patients, 0, 3);

// ---------------------------------------------------------------------
// 4. MEDICATION ACTIVITY LOG
// ---------------------------------------------------------------------
// Simple rule for "action":
//  - status = 'stopped'              → "Medication stopped"
//  - status = 'active' and created today → "New medication added"
//  - else                            → "Medication updated"

$sqlMedActivity = "
    SELECT 
        m.id,
        p.name AS patient,
        CASE
            WHEN m.status = 'stopped' THEN 'Medication stopped'
            WHEN m.status = 'active' 
                 AND DATE(m.created_at) = CURDATE()
                 THEN 'New medication added'
            ELSE 'Medication updated'
        END AS action,
        DATE_FORMAT(COALESCE(m.updated_at, m.created_at), '%Y-%m-%d %H:%i') AS time
    FROM medications m
    INNER JOIN patients p ON p.id = m.patient_id
    ORDER BY COALESCE(m.updated_at, m.created_at) DESC
    LIMIT 5
";

$medActivity = $pdo->query($sqlMedActivity)->fetchAll(PDO::FETCH_ASSOC);

// ensure old vars exist so other includes don't complain
$favoriteIds = $favoriteIds ?? [];
$items       = $items ?? [];
?>

<section class="py-4">
  <div class="container">

    <!-- Doctor Greeting -->
    <div class="mb-4">
      <h2 class="fw-bold mb-1">
        Welcome, Doctor
        <?php
        if (!empty($_SESSION['user_email'])) {
            echo htmlspecialchars($_SESSION['user_email']);
        }
        ?>
      </h2>
      <p class="text-muted mb-0">
        Here is an overview of your patients and their active medications.
      </p>
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
        <input type="text" name="search" class="form-control"
               placeholder="Search patients by name..."
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
              <a href="index.php?r=orders/info&id=<?= (int)$p['id'] ?>" class="list-group-item list-group-item-action">
                <div class="d-flex justify-content-between">
                  <div>
                    <strong><?= htmlspecialchars($p['name']) ?></strong><br>
                    <small class="text-muted">
                      Age: <?= (int)$p['age'] ?> &middot;
                      Allergies: <?= htmlspecialchars($p['allergies']) ?>
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
              <a href="index.php?r=orders/info&id=<?= (int)$m['id'] ?>" class="list-group-item list-group-item-action">
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
          <!-- later: link this to real medication create form -->
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
                      <a href="index.php?r=orders/info&id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-primary">
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
</section>
