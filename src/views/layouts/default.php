<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <title><?= $title ?> - ferenc.rocks</title>

    <!-- SEO -->
    <meta name="description" content="<?= isset($meta_description) ? $meta_description : "I'm Ferenc Faluvégi, the 'Swiss Army Knife' developer that teams call when they need to build, fix, or scale their platforms with purpose." ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= $title ?>">
    <meta property="og:description" content="<?= isset($meta_description) ? $meta_description : 'Default description here' ?>">
    <meta property="og:type" content="<?= isset($og_type) ? $og_type : 'website' ?>">
    <meta property="og:url" content="<?= isset($og_url) ? $og_url : (isset($_SERVER['REQUEST_URI']) ? "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" : '') ?>">
    <meta property="og:image" content="<?= isset($og_image) ? $og_image : '/assets/og-image.jpg' ?>">
    <meta property="og:locale" content="en_US">

    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">

    <!-- Styles -->
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <div class="site-container">
        <header class="site-header">
            <img src="/images/frocks-logo.webp" alt="Logo" class="nav-logo" />
            <nav class="main-nav">
                <ul>
                    <li><a href="/" class="<?= $_SERVER['REQUEST_URI'] === '/' ? 'active' : '' ?>">[Home]</a></li>
                    <li><a href="/curriculum-vitae" class="<?= $_SERVER['REQUEST_URI'] === '/curriculum-vitae' ? 'active' : '' ?>">[Curriculum Vitae]</a></li>
                    <li><a href="/contact" class="<?= $_SERVER['REQUEST_URI'] === '/contact' ? 'active' : '' ?>">[Contact]</a></li>
                </ul>
            </nav>
        </header>
        <main class="site-content">
            <?= $content ?>
        </main>
    </div>
</body>
</html>