<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - LaundryGo</title>
  <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body class="page-register">

<div class="auth-bg">
  <div class="auth-card">

    <!-- LEFT: FORM REGISTER -->
    <div class="auth-left">

      <div class="brand brand-center">
        <div class="brand-logo">
          <img src="../assets/img/Login/logo.png" alt="LaundryGo"
               onerror="this.style.display='none'; document.querySelector('.brand-mark').style.display='flex';">
          <div class="brand-mark" aria-hidden="true">LG</div>
        </div>
        <div class="brand-title">Laundry<span>Go</span></div>
      </div>

      <h2 class="auth-title">Buat akun baru</h2>
        <p class="sub-register">Lengkapi data berikut untuk mulai menggunakan LaundryGo</p>

      <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'gagal'): ?>
        <div class="alert alert-danger">❌ Pendaftaran gagal, coba lagi!</div>
      <?php endif; ?>

      <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'username_ada'): ?>
        <div class="alert alert-danger">❌ Username sudah digunakan!</div>
      <?php endif; ?>

      <form action="../controllers/RegisterController.php" method="POST" autocomplete="on">

        <div class="field">
          <label>Nama Lengkap</label>
          <input type="text" name="nama" placeholder="Masukkan nama lengkap" required>
        </div>

        <div class="field">
          <label>Username</label>
          <input type="text" name="username" placeholder="Buat username" required>
        </div>

        <div class="field">
          <label>Password</label>
          <div class="password-box">
            <input type="password" name="password" id="password" placeholder="Buat password" required>
            <button type="button" class="toggle" onclick="togglePassword()" aria-label="Lihat/Sembunyikan Password">
              <svg class="icon-eye" width="20" height="20" viewBox="0 0 24 24" fill="none"
                   xmlns="http://www.w3.org/2000/svg">
                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"
                      stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="field">
          <label>Alamat</label>
          <input type="text" name="alamat" placeholder="Masukkan alamat" required>
        </div>

        <div class="field">
          <label>No HP</label>
          <input type="text" name="no_hp" placeholder="08xxxxxxxxxx" required>
        </div>

        <button type="submit" name="daftar" class="btn-auth">DAFTAR</button>

        <div class="auth-footer">
          Sudah punya akun? <a href="login.php">Login</a>
        </div>

      </form>

    </div>

    <!-- RIGHT: PANEL -->
    <div class="auth-right">
      <div class="right-inner">
        <img class="illus" src="../assets/img/login-illus.png" alt="Ilustrasi"
             onerror="this.style.display='none';">
      </div>

      <span class="bubble b1"></span>
      <span class="bubble b2"></span>
      <span class="bubble b3"></span>
    </div>

  </div>
</div>

<script>
function togglePassword() {
  const pass = document.getElementById("password");
  pass.type = pass.type === "password" ? "text" : "password";
}
</script>

</body>
</html>