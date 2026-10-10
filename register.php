<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

$errors  = [];
$message = '';
$success = false;
$old = ['username' => '', 'email' => '', 'phone' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirmPassword'] ?? '';
    $old = ['username' => $username, 'email' => $email, 'phone' => $phone];

    // Username: letters, numbers, underscore, 7-10 chars
    if ($username === '') {
        $errors['username'] = 'Please enter a username';
    } elseif (!preg_match('/^[A-Za-z0-9_]{7,10}$/', $username)) {
        $errors['username'] = 'Username must be 7 to 10 characters (letters, numbers, underscore only)';
    }

    // Email
    if ($email === '') {
        $errors['email'] = 'Please enter your email';
    } elseif (!preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $email)) {
        $errors['email'] = 'Enter a valid email (e.g. name@gmail.com)';
    }

    // PH mobile number: 09XXXXXXXXX or +639XXXXXXXXX
    if ($phone === '') {
        $errors['phone'] = 'Please enter your phone number';
    } elseif (!preg_match('/^(09|\+639)\d{9}$/', $phone)) {
        $errors['phone'] = 'Enter a valid PH mobile number (e.g. 09171234567)';
    } elseif (preg_match('/^(\d)\1+$/', substr($phone, -9))) {
        $errors['phone'] = 'Phone number looks fake (repeated digits)';
    }

    // Password 7-12 chars
    if ($password === '') {
        $errors['password'] = 'Please enter a password';
    } elseif (!preg_match('/^.{7,12}$/', $password)) {
        $errors['password'] = 'Password must be 7 to 12 characters';
    }

    // Confirm password
    if ($confirm === '') {
        $errors['confirmPassword'] = 'Please confirm your password';
    } elseif ($confirm !== $password) {
        $errors['confirmPassword'] = 'Passwords do not match';
    }

    // Check kung may kapareho na
    if (empty($errors)) {
        $users = load_users();
        $uname = strtolower($username);
        $mail  = strtolower($email);

        foreach ($users as $u) {
            if ($u['username'] === $uname || $u['email'] === $mail) {
                $message = 'Username or email is already registered';
                break;
            }
        }

        if ($message === '') {
            $users[] = [
                'username' => $uname,
                'email'    => $mail,
                'phone'    => $phone,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ];
            save_users($users);
            $success = true;
            $message = 'Registration successful! Redirecting to login...';
            header('Refresh: 2; url=login.php');
        }
    }
}

$pageTitle = 'Register';
include __DIR__ . '/includes/form-head.php';
?>

  <div class="card">
    <h2>Register</h2>
    <?php if ($message): ?>
      <div id="message" class="<?= $success ? 'ok' : 'fail' ?>" style="display:block;"><?= e($message) ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php" novalidate>

      <div class="input-group">
        <input type="text" name="username" placeholder="Username" value="<?= e($old['username']) ?>">
        <span class="icon">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>
        </span>
      </div>
      <?php field_error($errors, 'username'); ?>

      <div class="input-group">
        <input type="email" name="email" placeholder="Email Address" value="<?= e($old['email']) ?>">
        <span class="icon">
          <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
        </span>
      </div>
      <?php field_error($errors, 'email'); ?>

      <div class="input-group">
        <input type="tel" name="phone" placeholder="Phone Number" value="<?= e($old['phone']) ?>">
        <span class="icon">
          <svg viewBox="0 0 24 24"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2C9.7 21 3 14.3 3 6a2 2 0 0 1 2-2z"/></svg>
        </span>
      </div>
      <?php field_error($errors, 'phone'); ?>

      <div class="input-group">
        <input type="password" name="password" placeholder="Password">
        <span class="icon">
          <svg viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
        </span>
      </div>
      <?php field_error($errors, 'password'); ?>

      <div class="input-group">
        <input type="password" name="confirmPassword" placeholder="Confirm Password">
        <span class="icon">
          <svg viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
        </span>
      </div>
      <?php field_error($errors, 'confirmPassword'); ?>

      <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
  </div>

<?php include __DIR__ . '/includes/form-foot.php'; ?>
