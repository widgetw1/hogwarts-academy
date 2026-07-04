<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Hogwarts Registration Portal</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- ===================== FRAME UTAMA (MOBILE-FIRST) ===================== -->
<div id="app-frame">

  <!-- Toggle Bahasa: menetap di pojok layar sepanjang Tahap 1-4 -->
  <div id="lang-toggle">
    <button type="button" class="lang-btn active" data-lang="id">ID</button>
    <span class="lang-sep">/</span>
    <button type="button" class="lang-btn" data-lang="en">EN</button>
  </div>

  <!-- Audio Background -->
  <audio id="bg-music" src="assets/audio/music.mp3" loop preload="auto"></audio>

  <!-- ===================== TAHAP 1: OPENING ===================== -->
  <section id="tahap-1" class="tahap tahap-active" style="background-image: url('assets/images/background.jpg');">
    <div class="opening-wrapper">
      <img id="img-surat-opening" class="opening-img opening-img-surat" src="assets/images/suratID.jpg" alt="Surat Undangan">
      <img class="opening-img opening-img-expres" src="assets/images/expres.jpg" alt="Hogwarts Express">
      <p id="klik-anywhere-text" class="klik-anywhere" data-i18n="klik_anywhere">klik anywhere</p>
    </div>
  </section>

  <!-- ===================== TAHAP 2: REGISTRASI ===================== -->
  <section id="tahap-2" class="tahap" style="background-image: url('assets/images/background.jpg');">
    <div class="form-card">
      <h1 class="form-title" data-i18n="form_title">Formulir Pendaftaran</h1>

      <form id="form-daftar" novalidate>
        <div class="field-group">
          <label for="nama_depan" data-i18n="label_nama_depan">Nama Depan</label>
          <input type="text" id="nama_depan" name="nama_depan" required autocomplete="given-name">
        </div>

        <div class="field-group">
          <label for="nama_belakang" data-i18n="label_nama_belakang">Nama Belakang</label>
          <input type="text" id="nama_belakang" name="nama_belakang" required autocomplete="family-name">
        </div>

        <div class="field-group">
          <label for="alamat" data-i18n="label_alamat">Alamat</label>
          <input type="text" id="alamat" name="alamat" required autocomplete="street-address">
        </div>

        <div class="field-group">
          <label for="jenis_kelamin" data-i18n="label_jenis_kelamin">Jenis Kelamin</label>
          <select id="jenis_kelamin" name="jenis_kelamin" required>
            <option value="" disabled selected data-i18n="opt_pilih">-- Pilih --</option>
            <option value="Laki-laki" data-i18n="opt_laki">Laki-laki</option>
            <option value="Perempuan" data-i18n="opt_perempuan">Perempuan</option>
          </select>
        </div>

        <div class="field-group">
          <label for="tanggal_lahir" data-i18n="label_tanggal_lahir">Tanggal Lahir</label>
          <input type="date" id="tanggal_lahir" name="tanggal_lahir" required>
        </div>

        <div class="field-group">
          <label for="status_darah" data-i18n="label_status_darah">Status Darah</label>
          <select id="status_darah" name="status_darah" required>
            <option value="" disabled selected data-i18n="opt_pilih">-- Pilih --</option>
            <option value="Pure-blood">Pure-blood</option>
            <option value="Half-blood">Half-blood</option>
            <option value="Muggle-born">Muggle-born</option>
          </select>
        </div>

        <div class="field-group">
          <label for="generasi" data-i18n="label_generasi">Generasi</label>
          <select id="generasi" name="generasi" required>
            <option value="1" selected>1</option>
          </select>
        </div>

        <p id="form-error-msg" class="form-error-msg"></p>

        <button type="submit" id="btn-daftar" class="btn-primary" data-i18n="btn_daftar">Daftar / Register</button>
      </form>
    </div>
  </section>

  <!-- ===================== TAHAP 3: SURAT KELULUSAN ===================== -->
  <section id="tahap-3" class="tahap tahap-surat">
    <a id="btn-download-surat" class="btn-download-surat" href="assets/images/surat2ID.jpg" download="Surat_Kelulusan.jpg">
      <span class="download-icon" aria-hidden="true">⬇</span>
      <span data-i18n="btn_download_surat">Download Surat</span>
    </a>

    <div class="surat-wrapper">
      <img id="img-surat-hasil" class="surat-img" src="assets/images/surat2ID.jpg" alt="Surat Kelulusan">

      <!-- Class khusus untuk teks dinamis di atas surat.
           Silakan sesuaikan top/left/transform di style.css
           pada class .surat-text-nama dan .surat-text-alamat -->
      <p id="surat-nama" class="surat-text surat-text-nama surat-text-nama-en"></p>
      <p id="surat-alamat" class="surat-text surat-text-alamat"></p>
    </div>

    <button type="button" id="btn-lanjut" class="btn-primary btn-lanjut" data-i18n="btn_lanjut">Lanjut / Next</button>
  </section>

  <!-- ===================== TAHAP 4: TWIBBON & IDHA ===================== -->
  <section id="tahap-4" class="tahap tahap-twibbon" style="background-image: url('assets/images/background.jpg');">
    <div class="twibbon-wrapper">
      <p class="twibbon-text" data-i18n="twibbon_text">silahkan posting twibbon di media sosial kalian</p>
      <img class="twibbon-img" src="assets/images/twibbon.jpg" alt="Twibbon Hogwarts">
      <p id="idha-number" class="idha-number">HW00000000</p>
      <p class="idha-warning" data-i18n="idha_warning">idha sementara tapi wajib di ingat</p>
    </div>
  </section>

</div>
<!-- ===================== END FRAME UTAMA ===================== -->

<script src="assets/js/bahasa.js"></script>
</body>
</html>