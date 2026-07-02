<?php
$conn = new mysqli("localhost", "root", "", "ungizwedb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = trim($_GET['q'] ?? '');

if ($query === '') {
    header("Location: index.html");
    exit();
}

/*
Fetch data
*/
$stmt = $conn->prepare("
    SELECT brand, topic, score, num_supporting, updated_at
    FROM brand_topic_scores
    WHERE brand LIKE CONCAT('%', ?, '%')
       OR topic LIKE CONCAT('%', ?, '%')
    ORDER BY score DESC
");

$stmt->bind_param("ss", $query, $query);
$stmt->execute();

$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

$stmt->close();
$conn->close();

/*
Derived values
*/
$brand = $rows[0]['brand'] ?? $query;

$total = 0;
foreach ($rows as $r) {
    $total += $r['score'];
}

$avgScore = count($rows) ? round($total / count($rows)) : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= htmlspecialchars($brand) ?> | Ungizwe Search</title>

<link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
<link href="assets/vendor/aos/aos.css" rel="stylesheet">
<link href="assets/css/main.css" rel="stylesheet">

<style>
.hero-score {
    font-size: 70px;
    font-weight: 800;
    color: #dc3545;
}

.progress {
    height: 10px;
    border-radius: 20px;
}

.card-box {
    border-radius: 16px;
    border: 0;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    padding: 25px;
    transition: 0.3s;
}

.card-box:hover {
    transform: translateY(-5px);
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



<!-- HERO -->
<section class="section light-background">
  <div class="container text-center" data-aos="fade-up">

    <h2 class="fw-bold"><?= htmlspecialchars($brand) ?></h2>

    <p class="text-muted">Anonymous Brand Intelligence Signal</p>

    <div class="hero-score">
      <?= $avgScore ?>/100
    </div>

    <p>
      Based on <strong><?= count($rows) ?></strong> anonymous signals
    </p>

  </div>
</section>

<!-- RESULTS -->
<section class="section">
  <div class="container">

    <div class="section-title" data-aos="fade-up">
      <h2>Topic Breakdown</h2>
      <p>AI-generated insights from anonymous customer and employee reports</p>
    </div>

    <div class="row gy-4">

      <?php if (count($rows) === 0): ?>

        <div class="col-12 text-center">
          <h4>No matching insights found.</h4>
        </div>

      <?php else: ?>

        <?php foreach ($rows as $row): ?>

        <div class="col-lg-6" data-aos="fade-up">

          <div class="card-box">

            <h3><?= htmlspecialchars($row['topic']) ?></h3>

            <h4 class="mt-2">
              <?= round($row['score']) ?> / 100
            </h4>

            <div class="progress mb-3">
              <div class="progress-bar bg-danger"
                   style="width: <?= min($row['score'],100) ?>%">
              </div>
            </div>

            <ul class="list-unstyled">
              <li>
                <i class="bi bi-check"></i>
                <?= $row['num_supporting'] ?> anonymous signals
              </li>
              <li>
                <i class="bi bi-clock"></i>
                Updated <?= $row['updated_at'] ?>
              </li>
            </ul>

            <!-- VOTE BUTTON -->
            <form action="vote.php" method="post" class="mt-3">

              <input type="hidden" name="brand" value="<?= htmlspecialchars($row['brand']) ?>">
              <input type="hidden" name="topic" value="<?= htmlspecialchars($row['topic']) ?>">

              <button type="submit" class="btn btn-success btn-sm w-100">
                👍 Agree with signal (<?= $row['num_supporting'] ?>)
              </button>

            </form>

          </div>

        </div>

        <?php endforeach; ?>

      <?php endif; ?>

    </div>

  </div>
</section>

<div class="container">

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">

        <a href="listing.php" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> All Listings
        </a>

        <form action="search.php" method="get" class="d-flex" style="max-width:400px;">
            <input
                type="text"
                name="q"
                class="form-control"
                placeholder="Search another brand..."
                value="<?= htmlspecialchars($query) ?>">

            <button class="btn btn-primary ms-2">
                <i class="bi bi-search"></i>
            </button>
        </form>

    </div>

</div>

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
