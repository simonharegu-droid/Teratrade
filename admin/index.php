<?php

declare(strict_types=1);

session_start();

require __DIR__ . '/../config/admin.php';
require __DIR__ . '/../includes/content.php';

$error = null;
$success = null;
$isAuthenticated = isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true;

if (isset($_GET['logout'])) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    header('Location: index.php');
    exit;
}

$content = load_site_content();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
            $_SESSION['admin_authenticated'] = true;
            header('Location: index.php');
            exit;
        }

        $error = 'Invalid username or password.';
    } elseif ($action === 'update_content') {
        if (!$isAuthenticated) {
            $error = 'Please log in to update content.';
        } else {
            $hero = $content['hero'] ?? [];
            $contact = $content['contact'] ?? [];
            $meta = $content['meta'] ?? [];

            $meta['title'] = trim((string)($_POST['meta_title'] ?? 'TerraTrade | Crypto Trading Reimagined'));

            $hero['eyebrow'] = trim((string)($_POST['hero_eyebrow'] ?? ''));
            $hero['headline'] = trim((string)($_POST['hero_headline'] ?? ''));
            $hero['description'] = trim((string)($_POST['hero_description'] ?? ''));
            $hero['primaryCtaLabel'] = trim((string)($_POST['hero_primary_label'] ?? ''));
            $hero['primaryCtaHref'] = trim((string)($_POST['hero_primary_href'] ?? ''));
            $hero['secondaryCtaLabel'] = trim((string)($_POST['hero_secondary_label'] ?? ''));
            $hero['secondaryCtaHref'] = trim((string)($_POST['hero_secondary_href'] ?? ''));

            $contact['headline'] = trim((string)($_POST['contact_headline'] ?? ''));
            $contact['description'] = trim((string)($_POST['contact_description'] ?? ''));
            $contact['supportEmail'] = trim((string)($_POST['contact_support_email'] ?? ''));
            $contact['enterpriseEmail'] = trim((string)($_POST['contact_enterprise_email'] ?? ''));
            $contact['address'] = trim((string)($_POST['contact_address'] ?? ''));

            $content['meta'] = $meta;
            $content['hero'] = $hero;
            $content['contact'] = $contact;

            if (save_site_content($content)) {
                $success = 'Content updated successfully.';
            } else {
                $error = 'Failed to save content. Please try again.';
            }
        }
    }

    $isAuthenticated = isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true;
}

function field_value(array $section, string $key): string
{
    $value = $section[$key] ?? '';

    return is_string($value) ? $value : '';
}

$meta = $content['meta'] ?? [];
$hero = $content['hero'] ?? [];
$contact = $content['contact'] ?? [];

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TerraTrade Admin</title>
    <style>
      :root {
        color-scheme: light dark;
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: #0f172a;
        color: #e2e8f0;
      }

      body {
        margin: 0;
        min-height: 100vh;
        display: grid;
        place-items: center;
        background: radial-gradient(circle at top, rgba(59, 130, 246, 0.2), transparent 55%), #0f172a;
      }

      .card {
        width: min(960px, 95vw);
        background: rgba(15, 23, 42, 0.9);
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 30px 60px rgba(15, 23, 42, 0.45);
      }

      h1 {
        margin-top: 0;
        font-size: 1.75rem;
        margin-bottom: 0.75rem;
      }

      p.lead {
        margin-top: 0;
        margin-bottom: 2rem;
        color: #94a3b8;
      }

      form {
        display: grid;
        gap: 1.5rem;
      }

      fieldset {
        border: 1px solid rgba(148, 163, 184, 0.25);
        border-radius: 16px;
        padding: 1.5rem;
      }

      fieldset legend {
        padding: 0 0.5rem;
        font-weight: 600;
      }

      label span {
        display: block;
        font-weight: 600;
        margin-bottom: 0.35rem;
      }

      input[type='text'],
      input[type='email'],
      textarea,
      input[type='password'] {
        width: 100%;
        padding: 0.75rem 0.9rem;
        border-radius: 12px;
        border: 1px solid rgba(148, 163, 184, 0.25);
        background: rgba(15, 23, 42, 0.6);
        color: inherit;
        font-size: 1rem;
      }

      textarea {
        min-height: 120px;
        resize: vertical;
      }

      .actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
      }

      .button {
        appearance: none;
        border: none;
        border-radius: 999px;
        padding: 0.75rem 1.8rem;
        font-weight: 600;
        cursor: pointer;
        background: linear-gradient(135deg, #38bdf8, #6366f1);
        color: #0f172a;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
      }

      .button:hover {
        transform: translateY(-1px);
        box-shadow: 0 15px 30px rgba(59, 130, 246, 0.35);
      }

      .button.secondary {
        background: transparent;
        border: 1px solid rgba(148, 163, 184, 0.25);
        color: inherit;
        box-shadow: none;
      }

      .alert {
        padding: 1rem 1.25rem;
        border-radius: 12px;
        font-weight: 600;
      }

      .alert.error {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(248, 113, 113, 0.45);
        color: #fecaca;
      }

      .alert.success {
        background: rgba(34, 197, 94, 0.12);
        border: 1px solid rgba(74, 222, 128, 0.5);
        color: #bbf7d0;
      }

      .logout {
        text-align: right;
        margin-bottom: 1.5rem;
      }

      .logout a {
        color: #38bdf8;
        text-decoration: none;
        font-weight: 600;
      }

      .login-form {
        display: grid;
        gap: 1rem;
      }

      .login-form button {
        justify-self: flex-end;
      }
    </style>
  </head>
  <body>
    <div class="card">
      <?php if ($isAuthenticated): ?>
      <div class="logout"><a href="?logout=1">Log out</a></div>
      <h1>TerraTrade content controls</h1>
      <p class="lead">
        Update the marketing site messaging for the hero and contact sections.
        Changes are saved instantly to the site.
      </p>
      <?php if ($error): ?>
      <div class="alert error"><?= h($error) ?></div>
      <?php elseif ($success): ?>
      <div class="alert success"><?= h($success) ?></div>
      <?php endif; ?>
      <form method="post">
        <input type="hidden" name="action" value="update_content" />
        <fieldset>
          <legend>Page metadata</legend>
          <label>
            <span>Browser title</span>
            <input type="text" name="meta_title" value="<?= h(field_value($meta, 'title')) ?>" required />
          </label>
        </fieldset>
        <fieldset>
          <legend>Hero section</legend>
          <label>
            <span>Eyebrow</span>
            <input type="text" name="hero_eyebrow" value="<?= h(field_value($hero, 'eyebrow')) ?>" required />
          </label>
          <label>
            <span>Headline</span>
            <input type="text" name="hero_headline" value="<?= h(field_value($hero, 'headline')) ?>" required />
          </label>
          <label>
            <span>Description</span>
            <textarea name="hero_description" required><?= h(field_value($hero, 'description')) ?></textarea>
          </label>
          <label>
            <span>Primary CTA label</span>
            <input type="text" name="hero_primary_label" value="<?= h(field_value($hero, 'primaryCtaLabel')) ?>" required />
          </label>
          <label>
            <span>Primary CTA link</span>
            <input type="text" name="hero_primary_href" value="<?= h(field_value($hero, 'primaryCtaHref')) ?>" required />
          </label>
          <label>
            <span>Secondary CTA label</span>
            <input type="text" name="hero_secondary_label" value="<?= h(field_value($hero, 'secondaryCtaLabel')) ?>" required />
          </label>
          <label>
            <span>Secondary CTA link</span>
            <input type="text" name="hero_secondary_href" value="<?= h(field_value($hero, 'secondaryCtaHref')) ?>" required />
          </label>
        </fieldset>
        <fieldset>
          <legend>Contact section</legend>
          <label>
            <span>Headline</span>
            <input type="text" name="contact_headline" value="<?= h(field_value($contact, 'headline')) ?>" required />
          </label>
          <label>
            <span>Introductory copy</span>
            <textarea name="contact_description" required><?= h(field_value($contact, 'description')) ?></textarea>
          </label>
          <label>
            <span>Support email</span>
            <input type="email" name="contact_support_email" value="<?= h(field_value($contact, 'supportEmail')) ?>" required />
          </label>
          <label>
            <span>Enterprise email</span>
            <input type="email" name="contact_enterprise_email" value="<?= h(field_value($contact, 'enterpriseEmail')) ?>" required />
          </label>
          <label>
            <span>Address</span>
            <input type="text" name="contact_address" value="<?= h(field_value($contact, 'address')) ?>" required />
          </label>
        </fieldset>
        <div class="actions">
          <span>Logged in as <strong><?= h(ADMIN_USERNAME) ?></strong></span>
          <button type="submit" class="button">Save changes</button>
        </div>
      </form>
      <?php else: ?>
      <h1>TerraTrade admin login</h1>
      <p class="lead">Sign in to manage homepage messaging.</p>
      <?php if ($error): ?>
      <div class="alert error"><?= h($error) ?></div>
      <?php endif; ?>
      <form method="post" class="login-form">
        <input type="hidden" name="action" value="login" />
        <label>
          <span>Username</span>
          <input type="text" name="username" autocomplete="username" required />
        </label>
        <label>
          <span>Password</span>
          <input type="password" name="password" autocomplete="current-password" required />
        </label>
        <button type="submit" class="button">Log in</button>
      </form>
      <?php endif; ?>
    </div>
  </body>
</html>
