/* =====================================================
   HOGWARTS REGISTRATION PORTAL — bahasa.js
   Berisi: kamus terjemahan ID/EN, kontrol audio, transisi
   antar tahap, dan proses AJAX pendaftaran.
   ===================================================== */

(function () {
  'use strict';

  /* =====================================================
     1. KAMUS TERJEMAHAN
     ===================================================== */
  const DICT = {
    id: {
      klik_anywhere: 'klik anywhere',
      form_title: 'Formulir Pendaftaran',
      label_nama_depan: 'Nama Depan',
      label_nama_belakang: 'Nama Belakang',
      label_alamat: 'Alamat',
      label_jenis_kelamin: 'Jenis Kelamin',
      label_tanggal_lahir: 'Tanggal Lahir',
      label_status_darah: 'Status Darah',
      label_generasi: 'Generasi',
      opt_pilih: '-- Pilih --',
      opt_laki: 'Laki-laki',
      opt_perempuan: 'Perempuan',
      btn_daftar: 'Daftar',
      btn_lanjut: 'Lanjut',
      twibbon_text: 'silahkan posting twibbon di media sosial kalian',
      idha_warning: 'idha sementara tapi wajib di ingat',
      err_wajib: 'Semua kolom wajib diisi dengan benar.',
      err_server: 'Terjadi kesalahan saat mengirim data. Coba lagi.',
      loading_daftar: 'Memproses...',
      btn_download_surat: 'Download Surat',
    },
    en: {
      klik_anywhere: 'click anywhere',
      form_title: 'Registration Form',
      label_nama_depan: 'First Name',
      label_nama_belakang: 'Last Name',
      label_alamat: 'Address',
      label_jenis_kelamin: 'Gender',
      label_tanggal_lahir: 'Date of Birth',
      label_status_darah: 'Blood Status',
      label_generasi: 'Generation',
      opt_pilih: '-- Select --',
      opt_laki: 'Male',
      opt_perempuan: 'Female',
      btn_daftar: 'Register',
      btn_lanjut: 'Next',
      twibbon_text: 'please post the twibbon on your social media',
      idha_warning: 'idha is temporary but must be remembered',
      err_wajib: 'All fields must be filled in correctly.',
      err_server: 'An error occurred while sending data. Please try again.',
      loading_daftar: 'Processing...',
    },
  };

  // Gambar surat opening (Tahap 1) yang berubah sesuai bahasa
  const IMG_SURAT_OPENING = {
    id: 'assets/images/suratID.jpg',
    en: 'assets/images/suratEG.jpg',
  };

  // Gambar surat kelulusan (Tahap 3) yang berubah sesuai bahasa
  const IMG_SURAT_HASIL = {
    id: 'assets/images/surat2ID.jpg',
    en: 'assets/images/surat2EN.jpg',
  };

  let currentLang = 'id';

  /* =====================================================
     2. ELEMENT REFERENCES
     ===================================================== */
  const tahap1 = document.getElementById('tahap-1');
  const tahap2 = document.getElementById('tahap-2');
  const tahap3 = document.getElementById('tahap-3');
  const tahap4 = document.getElementById('tahap-4');

  const bgMusic = document.getElementById('bg-music');
  const imgSuratOpening = document.getElementById('img-surat-opening');
  const imgSuratHasil = document.getElementById('img-surat-hasil');

  const formDaftar = document.getElementById('form-daftar');
  const btnDaftar = document.getElementById('btn-daftar');
  const formErrorMsg = document.getElementById('form-error-msg');
  const btnLanjut = document.getElementById('btn-lanjut');

  const suratNamaEl = document.getElementById('surat-nama');
  const suratAlamatEl = document.getElementById('surat-alamat');
  const idhaNumberEl = document.getElementById('idha-number');

  const langButtons = document.querySelectorAll('.lang-btn');

  let audioStarted = false;
  let dataPendaftar = null; // menyimpan hasil response AJAX untuk dipakai di tahap 3 & 4

  /* =====================================================
     2b. FUNGSI GELAR (Tuan/Nona / Mr/Ms) SESUAI JENIS KELAMIN & BAHASA
     ===================================================== */
  function getGelar(jenisKelamin, lang) {
    if (jenisKelamin === 'Laki-laki') {
      return lang === 'en' ? 'Mr.' : 'Tuan';
    }
    if (jenisKelamin === 'Perempuan') {
      return lang === 'en' ? 'Ms.' : 'Nona';
    }
    return '';
  }

  /* =====================================================
     3. FUNGSI TERJEMAHAN (TANPA RELOAD)
     ===================================================== */
  function applyTranslation(lang) {
    currentLang = lang;
    const dict = DICT[lang];

    document.querySelectorAll('[data-i18n]').forEach((el) => {
      const key = el.getAttribute('data-i18n');
      if (dict[key] !== undefined) {
        el.textContent = dict[key];
      }
    });

    // Ganti gambar surat opening sesuai bahasa
    if (imgSuratOpening) {
      imgSuratOpening.src = IMG_SURAT_OPENING[lang];
    }

    // Ganti gambar surat hasil sesuai bahasa
    if (imgSuratHasil) {
      imgSuratHasil.src = IMG_SURAT_HASIL[lang];
    }

    // Toggle class khusus posisi nama versi EN (lihat style.css: .surat-text-nama-en.lang-en-active)
    if (suratNamaEl) {
      suratNamaEl.classList.toggle('lang-en-active', lang === 'en');
    }

    // Update tombol aktif
    langButtons.forEach((btn) => {
      btn.classList.toggle('active', btn.getAttribute('data-lang') === lang);
    });

    // Update dropdown jenis kelamin (value tetap standar ID untuk konsistensi backend,
    // hanya label yang berubah — value asli tetap dikirim ke server)
    const selectGender = document.getElementById('jenis_kelamin');
    if (selectGender) {
      const optLaki = selectGender.querySelector('option[value="Laki-laki"]');
      const optPerempuan = selectGender.querySelector('option[value="Perempuan"]');
      if (optLaki) optLaki.textContent = dict.opt_laki;
      if (optPerempuan) optPerempuan.textContent = dict.opt_perempuan;
    }

    // Update gelar di nama surat jika data pendaftar sudah ada (ganti bahasa setelah daftar)
    if (dataPendaftar && suratNamaEl) {
      const gelar = getGelar(dataPendaftar.jenis_kelamin, lang);
      suratNamaEl.textContent = gelar ? gelar + ' ' + dataPendaftar.nama_lengkap : dataPendaftar.nama_lengkap;
    }

    document.documentElement.lang = lang;
  }

  langButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      const lang = btn.getAttribute('data-lang');
      applyTranslation(lang);
    });
  });

  /* =====================================================
     4. TRANSISI ANTAR TAHAP
     ===================================================== */
  function gotoTahap(el) {
    document.querySelectorAll('.tahap').forEach((t) => {
      t.classList.remove('tahap-active');
    });
    // paksa reflow agar transisi CSS berjalan mulus
    void el.offsetWidth;
    el.classList.add('tahap-active');
  }

  /* =====================================================
     5. TAHAP 1 -> TAHAP 2: klik anywhere + start audio
     ===================================================== */
  tahap1.addEventListener('click', function onFirstClick() {
    if (!audioStarted) {
      audioStarted = true;
      bgMusic.loop = true;
      bgMusic.play().catch(() => {
        // Jika autoplay tetap diblokir browser tertentu,
        // audio akan tetap mencoba play saat interaksi berikutnya.
      });
    }
    gotoTahap(tahap2);
    tahap1.removeEventListener('click', onFirstClick);
  });

  /* =====================================================
     6. TAHAP 2: SUBMIT FORM VIA AJAX (fetch API)
     ===================================================== */
  formDaftar.addEventListener('submit', function (e) {
    e.preventDefault();
    formErrorMsg.textContent = '';

    const namaDepan = document.getElementById('nama_depan').value.trim();
    const namaBelakang = document.getElementById('nama_belakang').value.trim();
    const alamat = document.getElementById('alamat').value.trim();
    const jenisKelamin = document.getElementById('jenis_kelamin').value;
    const tanggalLahir = document.getElementById('tanggal_lahir').value;
    const statusDarah = document.getElementById('status_darah').value;
    const generasi = document.getElementById('generasi').value;

    if (!namaDepan || !namaBelakang || !alamat || !jenisKelamin || !tanggalLahir || !statusDarah || !generasi) {
      formErrorMsg.textContent = DICT[currentLang].err_wajib;
      return;
    }

    const payload = {
      nama_depan: namaDepan,
      nama_belakang: namaBelakang,
      alamat: alamat,
      jenis_kelamin: jenisKelamin,
      tanggal_lahir: tanggalLahir,
      status_darah: statusDarah,
      generasi: generasi,
    };

    btnDaftar.disabled = true;
    const originalBtnText = btnDaftar.textContent;
    btnDaftar.textContent = DICT[currentLang].loading_daftar;

    fetch('proses_daftar.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    })
      .then((res) => res.json())
      .then((json) => {
        btnDaftar.disabled = false;
        btnDaftar.textContent = originalBtnText;

        if (!json.success) {
          formErrorMsg.textContent = json.message || DICT[currentLang].err_server;
          return;
        }

        dataPendaftar = json.data;

        // Isi teks di surat kelulusan (Tahap 3), dengan gelar sesuai jenis kelamin & bahasa aktif
        const gelar = getGelar(dataPendaftar.jenis_kelamin, currentLang);
        suratNamaEl.textContent = gelar ? gelar + ' ' + dataPendaftar.nama_lengkap : dataPendaftar.nama_lengkap;
        suratAlamatEl.textContent = dataPendaftar.alamat;

        // Isi nomor IDHA (dipakai di Tahap 4)
        idhaNumberEl.textContent = dataPendaftar.idha;

        gotoTahap(tahap3);
      })
      .catch(() => {
        btnDaftar.disabled = false;
        btnDaftar.textContent = originalBtnText;
        formErrorMsg.textContent = DICT[currentLang].err_server;
      });
  });

  /* =====================================================
     7. TAHAP 3 -> TAHAP 4
     ===================================================== */
  btnLanjut.addEventListener('click', function () {
    gotoTahap(tahap4);
  });

  /* =====================================================
     8. INISIALISASI: set bahasa default saat halaman dimuat
     ===================================================== */
  applyTranslation('id');
})();