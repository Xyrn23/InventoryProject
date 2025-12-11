<div class="wrapper">
  <form method="POST" action="features/authentication/auth_actions.php" data-tilt data-tilt-glare>
    <img src="assets/logo.svg" class="__logo">

    <h2 class="login__title">Admin Login</h2>

    <div class="login__field">
      <input type="text" id="username" name="username" class="login__input" placeholder=" " required>
      <label for="username" class="login__label">Username</label>
    </div>

    <div class="login__field">
      <input type="password" id="password" name="password" class="login__input" placeholder=" " required>
      <label for="password" class="login__label">Password</label>
    </div>

    <button type="submit">Login</button>

    <div style="text-align: center; margin-top: 20px;">
      <span style="color: rgba(255, 255, 255, 0.7);">Don't have an account?</span>
      <br>
      <a href="register.php" style="color: #67a6ff; text-decoration: none; font-weight: 500;">Register here</a>
    </div>
  </form>
</div>