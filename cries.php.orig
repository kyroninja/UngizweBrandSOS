<?php

$recent_brands = [];

// Try Redis first
try {
    $redis = new Redis();

    if ($redis->connect('127.0.0.1', 6379, 1)) {
        $redis->select(0);

        $cached = $redis->get("recent_brands");

        if ($cached) {
            $decoded = json_decode($cached, true);

            if (is_array($decoded) && !empty($decoded)) {
                $recent_brands = $decoded;
            }
        }
    }

} catch (Exception $e) {
    // Redis unavailable
}

// Fallback to MySQL
if (empty($recent_brands)) {

    $conn = new mysqli("localhost", "root", "", "ungizwedb");

    if (!$conn->connect_error) {

        $sql = "
            SELECT
                brand,
                topic,
                score,
                MAX(id) AS last_updated
            FROM brand_topic_scores
            GROUP BY brand, topic
            ORDER BY last_updated DESC
            LIMIT 5
        ";

        $result = $conn->query($sql);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $recent_brands[] = $row;
            }
        }

        $conn->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>UngizweForm</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">
</head>

<body class="blog-details-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.html" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="assets/img/logo.webp" alt=""> -->
        <h1 class="sitename">Ungizwe?</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.html#hero" class="active">Home</a></li>
          <li><a href="index.html#about">About</a></li>
          <li><a href="index.html#team">Team</a></li>
          <li><a href="index.html#pricing">Pricing</a></li>
          <li><a href="index.html#contact">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted" href="#about">Get Started</a>

    </div>
  </header>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title" data-aos="fade">
      <div class="container">
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.html">Home</a></li>
            <li class="current">Cry Out</li>
          </ol>
        </nav>
        <h1>Mzanzi Cries for Help</h1>
      </div>
    </div><!-- End Page Title -->

    <div class="container">
      <div class="row">

        <div class="col-lg-8">


          <!-- Cry Submission Form Section -->
<section id="cry-form" class="blog-comment-form section">

  <div class="container" data-aos="fade-up" data-aos-delay="100">

    <form action="submit_cry.php" method="post" role="form">

      <div class="form-header">
        <h3>Submit a Cry for Help</h3>
        <p>Share your experience anonymously. Each submission is stored as a raw signal in our system for analysis and pattern detection.</p>
        <h4>Privacy & Fair Use</h4>
        <p>

Your submission is completely anonymous and will never be linked to your identity. By submitting a signal, you confirm that it reflects your genuine experience or opinion and is provided in good faith.

Ungizwe is an independent platform that aggregates anonymous submissions using AI to identify recurring themes and trends. Individual submissions are not presented as verified facts or allegations, and published insights represent aggregated community sentiment rather than the views of Ungizwe.

Please do not include confidential information, personal information, or defamatory, abusive, or unlawful content.
</p>
<div class="col-12 mt-3">
  <div class="form-check">
    <input
      class="form-check-input"
      type="checkbox"
      value="1"
      id="consentCheck"
      name="consent"
      required
    >

<label class="form-check-label" for="consentCheck">
  I confirm that this submission reflects my honest experience or opinion.
  I understand it will be anonymised and analysed as part of aggregated brand insights.
</label>


  </div>
</div>


      </div>

      <div class="row gy-3">

        <!-- Brand (DB: brand) -->
        <div class="col-md-12">
          <div class="input-group">
            <label for="brand">Brand *</label>
            <input type="text" name="brand" id="brand" placeholder="e.g. Vodacom, Shoprite" required>
            <span class="error-text">Brand is required</span>
          </div>
        </div>

        <!-- Cry (DB: cry) -->
        <div class="col-12">
          <div class="input-group">
            <label for="cry">Your Experience *</label>
            <textarea name="cry" id="cry" rows="6" placeholder="Describe what happened..." required></textarea>
            <span class="error-text">Please share your experience</span>
          </div>
        </div>

        <!-- Submit -->
        <div class="col-12 text-center">
          <button type="submit">Send Cry</button>
        </div>

      </div>

    </form>

  </div>

</section><!-- /Cry Submission Form Section -->

        </div>

        <div class="col-lg-4 sidebar">

          <div class="widgets-container" data-aos="fade-up" data-aos-delay="200">

            <!-- Search Widget -->
  <div class="search-widget widget-item">
    <h3 class="widget-title">Search</h3>
    <form action="search.php" method="get">
      <input type="text" name="q" placeholder="Search..." autocomplete="off" required>
      <button type="submit" title="Search">
        <i class="bi bi-search"></i>
      </button>
    </form>
  </div>

            <!-- Recent Posts Widget -->
            <div class="recent-posts-widget widget-item p-3 rounded-4 shadow-sm bg-white">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="widget-title m-0 fw-semibold">
                Recently Updated Brands
            </h3>

            <span class="badge bg-dark-subtle text-dark">
                Live feed
            </span>
        </div>

        <?php if (!empty($recent_brands)): ?>

            <div class="list-group list-group-flush">

                <?php foreach ($recent_brands as $item): ?>

                    <div class="list-group-item px-0 py-3 border-0 border-bottom">

                        <div class="d-flex justify-content-between align-items-start">

                            <!-- Left content -->
                            <div class="me-3">

                                <h5 class="mb-1 fw-bold text-primary">
                                    <?= htmlspecialchars($item['brand']) ?>
                                </h5>

                                <p class="mb-1 text-muted small">
                                    <?= htmlspecialchars($item['topic']) ?>
                                </p>

                            </div>

                            <!-- Right badge -->
                            <div class="text-end">

                                <span class="badge rounded-pill bg-success px-3 py-2">
                                    <?= htmlspecialchars($item['score']) ?> / 100
                                </span>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="text-center py-4">

                <div class="text-muted mb-2">
                    No recent activity yet
                </div>

                <small class="text-secondary">
                    Brand signals will appear here once users submit cries.
                </small>

            </div>

        <?php endif; ?>

    </div>
            <!--/Recent Posts Widget -->



          </div>

        </div>

      </div>
    </div>

  </main>

  <footer id="footer" class="footer">

    <!-- Newsletter -->
    <div class="footer-newsletter py-5 bg-light border-top">
        <div class="container">
            <div class="row justify-content-center text-center">

                <div class="col-lg-6">

                    <h4 class="fw-bold">Stay Updated</h4>

                    <p class="text-muted">
                        Get insights on brand sentiment, community signals, and platform updates.
                    </p>



                </div>

            </div>
        </div>
    </div>

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
