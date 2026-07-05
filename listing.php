<?php
// listing.php - Ungizwe Brand Intelligence Listing

require_once __DIR__ . '/config.php';

$conn = db_connect();

/* -------------------------
   INPUTS
--------------------------*/
$search      = trim($_GET['search'] ?? '');
$sort        = $_GET['sort'] ?? 'latest';
$minScore    = intval($_GET['min_score'] ?? 0);
$minSupport  = intval($_GET['min_support'] ?? 0);

/* -------------------------
   BASE QUERY
--------------------------*/
$sql = "
    SELECT brand, topic, score, num_supporting, updated_at
    FROM brand_topic_scores
    WHERE 1=1
";

$params = [];
$types  = "";

/* -------------------------
   SEARCH FILTER
--------------------------*/
if ($search !== '') {
    $sql .= " AND (brand LIKE ? OR topic LIKE ?) ";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}

/* -------------------------
   NUMERIC FILTERS
--------------------------*/
$sql .= " AND score >= ? AND num_supporting >= ? ";
$params[] = $minScore;
$params[] = $minSupport;
$types .= "ii";

/* -------------------------
   SORTING
--------------------------*/
if ($sort === 'score') {
    $sql .= " ORDER BY score DESC ";
} elseif ($sort === 'support') {
    $sql .= " ORDER BY num_supporting DESC ";
} else {
    $sql .= " ORDER BY updated_at DESC ";
}

/* -------------------------
   EXECUTE
--------------------------*/
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();

$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Ungizwe Listing</title>

<!-- CSS -->
<link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
<link href="assets/vendor/aos/aos.css" rel="stylesheet">
<link href="assets/css/main.css" rel="stylesheet">

<style>
.hero {
    padding: 40px 0;
    text-align: center;
}

.card-box {
    border: 0;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    transition: 0.3s;
}

.card-box:hover {
    transform: translateY(-5px);
}

.score {
    font-size: 40px;
    font-weight: 800;
    color: #dc3545;
}

.progress {
    height: 10px;
    border-radius: 20px;
}
</style>
</head>

<body>

<!-- HEADER -->
<header id="header" class="header d-flex align-items-center sticky-top">
  <div class="container-fluid container-xl d-flex align-items-center">

    <a href="index.html" class="logo me-auto">
      <h1 class="sitename">Ungizwe?</h1>
    </a>

    <nav class="navmenu">
      <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="index.html#about">About</a></li>
        <li><a href="index.html#team">Team</a></li>
        <li><a href="index.html#pricing">Pricing</a></li>
        <li><a href="index.html#contact">Contact</a></li>
      </ul>
    </nav>

  </div>
</header>

<main class="main">

<!-- PAGE TITLE -->
<div class="page-title">
  <div class="container">
    <h1>Brand Intelligence Listing</h1>
    <p class="text-muted">Anonymous signals from customers & employees</p>
  </div>
</div>

<!-- SEARCH + FILTERS -->
<section class="section">
  <div class="container">

    <form method="GET" class="row gy-3 align-items-end">

      <div class="col-lg-4">
        <label>Search</label>
        <input type="text"
               name="search"
               class="form-control"
               placeholder="Brand or topic..."
               value="<?= htmlspecialchars($search) ?>">
      </div>

      <div class="col-lg-3">
        <label>Sort</label>
        <select name="sort" class="form-control">
          <option value="latest" <?= $sort==='latest'?'selected':'' ?>>Latest</option>
          <option value="score" <?= $sort==='score'?'selected':'' ?>>Highest Score</option>
          <option value="support" <?= $sort==='support'?'selected':'' ?>>Most Supported</option>
        </select>
      </div>

      <div class="col-lg-2">
        <label>Min Score</label>
        <input type="number"
               name="min_score"
               class="form-control"
               value="<?= $minScore ?>">
      </div>

      <div class="col-lg-2">
        <label>Min Votes</label>
        <input type="number"
               name="min_support"
               class="form-control"
               value="<?= $minSupport ?>">
      </div>

      <div class="col-lg-1">
        <button class="btn btn-primary w-100">
          <i class="bi bi-search"></i>
        </button>
      </div>

    </form>

  </div>
</section>

<!-- RESULTS -->
<section class="section">
  <div class="container">

    <div class="row gy-4">

      <?php if (count($rows) === 0): ?>

        <div class="col-12 text-center">
          <h4>No results found.</h4>
        </div>

      <?php else: ?>

        <?php foreach ($rows as $r): ?>

        <div class="col-lg-6">

          <div class="card card-box p-4">

            <h3><?= htmlspecialchars($r['brand']) ?></h3>
            <h5 class="text-muted"><?= htmlspecialchars($r['topic']) ?></h5>

            <div class="score mt-3">
              <?= round($r['score']) ?>/100
            </div>

            <div class="progress mt-2">
              <div class="progress-bar bg-danger"
                   style="width: <?= (int) min($r['score'],100) ?>%">
              </div>
            </div>

            <div class="mt-3 text-muted">
              <?= $r['num_supporting'] ?> anonymous signals •
              Updated <?= $r['updated_at'] ?>
            </div>

          </div>

        </div>

        <?php endforeach; ?>

      <?php endif; ?>

    </div>

  </div>
</section>

</main>
<!-- Main Footer -->
<div class="container footer-top py-5">

    <div class="row gy-4">

        <!-- Brand -->
        <div class="col-lg-4 col-md-6">

            <h3 class="fw-bold">Ungizwe</h3>

            <p class="text-muted">
                A signal intelligence layer for understanding real consumer experiences across South Africa.
            </p>

            <p class="mt-3 mb-1">
                <strong>Email:</strong> support@ungizwe.co.za
            </p>

            <p>
                <strong>Location:</strong> Durban, South Africa
            </p>

        </div>


        <!-- Social -->
        <div class="col-lg-4 col-md-12">

            <h5 class="fw-semibold">Connect</h5>

            <p class="text-muted">
                Building transparency between brands and people.
            </p>

            <div class="d-flex gap-3 mt-3">

                <a href="#" class="text-dark">
                    <i class="bi bi-twitter-x"></i>
                </a>

                <a href="#" class="text-dark">
                    <i class="bi bi-facebook"></i>
                </a>

                <a href="#" class="text-dark">
                    <i class="bi bi-instagram"></i>
                </a>

                <a href="#" class="text-dark">
                    <i class="bi bi-linkedin"></i>
                </a>

            </div>

        </div>

    </div>

</div>

<!-- Bottom Bar -->
<div class="container text-center py-3 border-top">

    <small class="text-muted">
        © <?php echo date("Y"); ?> Ungizwe. All rights reserved.
    </small>

</div>

</footer>

<!-- Scroll Top -->
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
<i class="bi bi-arrow-up-short"></i>
</a>

<!-- Preloader -->
<div id="preloader"></div>

<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

<!-- Main JS File -->
<script src="assets/js/main.js"></script>
