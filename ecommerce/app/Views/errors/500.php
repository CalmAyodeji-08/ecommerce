<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Error — <?= defined('APP_NAME') ? e(APP_NAME) : 'AZFAT BEST BOUTIQUE' ?></title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, sans-serif; background: #f5f5f0; color: #111;
               display: flex; align-items: center; justify-content: center;
               min-height: 100vh; padding: 1.5rem; }
        .card { background: #fff; border-radius: 12px; padding: 3rem 2rem;
                max-width: 480px; width: 100%; text-align: center; box-shadow: 0 4px 16px rgba(0,0,0,.1); }
        h1 { font-size: 1.5rem; margin-bottom: 0.75rem; }
        p  { color: #666; margin-bottom: 1.5rem; }
        a  { display: inline-block; background: #111; color: #fff; padding: 0.75rem 1.5rem;
             border-radius: 4px; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Something went wrong</h1>
        <p>We've logged the error and will fix it shortly. Please try again in a moment.</p>
        <a href="/">Back to Home</a>
    </div>
</body>
</html>
