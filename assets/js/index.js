// File: index.js
// Description: /inc/enque-my-scripts.phpでこのファイルを読み込んで、/functions.phpで実行しています。

document.addEventListener('DOMContentLoaded', function () {
  // ハンバーガーメニューの制御
  const hamburger = document.querySelector('.js_humb');
  const nav = document.querySelector('.bl_sidebar_nav');
  const header = document.querySelector('.ly_header');
  const body = document.body;

  let lastScrollY = 0;
  let ticking = false;

  function updateHeader() {
    if (!header) return;

    const currentScrollY = window.scrollY;
    const isMenuOpen = nav && nav.classList.contains('is_open');

    // スクロール位置が0より大きい場合は角丸
    if (currentScrollY > 0 && !isMenuOpen) {
      header.classList.add('is_scrolled');
    } else {
      header.classList.remove('is_scrolled');
    }

    // 一定量スクロールしたら方向検出を開始
    if (currentScrollY > 100) {
      if (currentScrollY > lastScrollY) {
        // 下スクロール：ヘッダーを隠す
        header.classList.add('is_hidden');
      } else {
        // 上スクロール：ヘッダーを表示
        header.classList.remove('is_hidden');
      }
    } else {
      // ページ上部では常に表示
      header.classList.remove('is_hidden');
    }

    lastScrollY = currentScrollY;
    ticking = false;
  }

  window.addEventListener('scroll', function () {
    if (!ticking) {
      requestAnimationFrame(updateHeader);
      ticking = true;
    }
  });

  // 初めての方へボタン：フッターと重なったら非表示
  const sideFirstBtn = document.querySelector('.bl_sideFirstBtn');
  const footer = document.querySelector('.ly_footer');

  function updateSideFirstBtnVisibility() {
    if (!sideFirstBtn || !footer) return;

    const btnRect = sideFirstBtn.getBoundingClientRect();
    const footerRect = footer.getBoundingClientRect();

    if (btnRect.bottom > footerRect.top) {
      sideFirstBtn.classList.add('is_hidden');
    } else {
      sideFirstBtn.classList.remove('is_hidden');
    }
  }

  if (sideFirstBtn && footer) {
    window.addEventListener('scroll', updateSideFirstBtnVisibility);
    updateSideFirstBtnVisibility();
  }

  // パネルモーダル共通制御（WEB予約・LINE予約）
  var panelModals = document.querySelectorAll('.bl_panelModal');

  function closeAllPanelModals() {
    panelModals.forEach(function (m) { m.classList.remove('is_open'); });
    if (hamburger) hamburger.classList.remove('is_active');
    body.classList.remove('is_menuOpen');
  }

  function openPanelModal(modal) {
    if (!modal) return;
    if (nav && nav.classList.contains('is_open')) {
      nav.classList.remove('is_open');
    }
    panelModals.forEach(function (m) {
      if (m !== modal) m.classList.remove('is_open');
    });
    if (hamburger) hamburger.classList.add('is_active');
    modal.classList.add('is_open');
    body.classList.add('is_menuOpen');
  }

  function initPanelModal(modalSelector, openSelector) {
    var modal = document.querySelector(modalSelector);
    var openers = document.querySelectorAll(openSelector);
    if (!modal || openers.length === 0) return;

    openers.forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        openPanelModal(modal);
      });
    });
  }

  initPanelModal('.js_bookingModal', '.js_bookingModalOpen');
  initPanelModal('.js_lineModal', '.js_lineModalOpen');
  initPanelModal('.js_phoneModal', '.js_phoneModalOpen');

  if (hamburger && nav) {
    hamburger.addEventListener('click', function () {
      // パネルモーダルが開いていたら閉じるだけで終了
      var anyPanelOpen = false;
      document.querySelectorAll('.bl_panelModal').forEach(function (m) {
        if (m.classList.contains('is_open')) anyPanelOpen = true;
      });
      if (anyPanelOpen) {
        closeAllPanelModals();
        return;
      }
      hamburger.classList.toggle('is_active');
      nav.classList.toggle('is_open');
      body.classList.toggle('is_menuOpen');
      updateHeaderRadius();
    });

    // メニュー内のリンクをクリックしたらメニューを閉じる
    const navLinks = nav.querySelectorAll('a');
    navLinks.forEach(function (link) {
      link.addEventListener('click', function () {
        hamburger.classList.remove('is_active');
        nav.classList.remove('is_open');
        body.classList.remove('is_menuOpen');
        updateHeaderRadius();
      });
    });
  }

  // お悩みドロップダウンの制御
  const symptomsToggle = document.querySelector('.js_symptomsToggle');
  const symptomsDropdown = document.querySelector('.js_symptomsDropdown');

  if (symptomsToggle && symptomsDropdown) {
    symptomsToggle.addEventListener('click', function () {
      symptomsToggle.classList.toggle('is_active');
      symptomsDropdown.classList.toggle('is_open');
    });

    // カテゴリー名のアコーディオン
    const catToggles = symptomsDropdown.querySelectorAll('.js_catToggle');
    catToggles.forEach(function (toggle) {
      toggle.addEventListener('click', function () {
        toggle.classList.toggle('is_active');
      });
    });
  }

  // メガメニュー カテゴリーホバー
  const megaCats = document.querySelectorAll('.js_megaCat');
  const megaGroups = document.querySelectorAll('.bl_megaMenu_symptomGroup');

  megaCats.forEach(function (cat) {
    cat.addEventListener('mouseenter', function () {
      var catId = cat.getAttribute('data-cat');

      megaCats.forEach(function (c) { c.classList.remove('is_active'); });
      megaGroups.forEach(function (g) { g.classList.remove('is_active'); });

      cat.classList.add('is_active');
      var target = document.querySelector('.bl_megaMenu_symptomGroup[data-cat="' + catId + '"]');
      if (target) target.classList.add('is_active');
    });
  });

  // 店舗モーダルの制御
  var shopModal = document.querySelector('.js_shopModal');
  var shopModalOpens = document.querySelectorAll('.js_shopModalOpen');
  var shopModalCloses = document.querySelectorAll('.js_shopModalClose');

  if (shopModal && shopModalOpens.length > 0) {
    shopModalOpens.forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        shopModal.classList.add('is_open');
        body.classList.add('is_menuOpen');
      });
    });

    shopModalCloses.forEach(function (btn) {
      btn.addEventListener('click', function () {
        shopModal.classList.remove('is_open');
        body.classList.remove('is_menuOpen');
      });
    });

    // Escキーで閉じる
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && shopModal.classList.contains('is_open')) {
        shopModal.classList.remove('is_open');
        body.classList.remove('is_menuOpen');
      }
    });

    // モーダル内タブ切り替え
    var modalTabs = shopModal.querySelectorAll('.bl_shopList_tab');
    var modalPanels = shopModal.querySelectorAll('.bl_shopList_panel');

    modalTabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        var area = this.getAttribute('data-area');

        modalTabs.forEach(function (t) {
          t.classList.remove('is_active');
          t.setAttribute('aria-selected', 'false');
        });
        this.classList.add('is_active');
        this.setAttribute('aria-selected', 'true');

        modalPanels.forEach(function (panel) {
          panel.classList.remove('is_active');
        });
        var targetPanel = document.getElementById('shopModal-' + area);
        if (targetPanel) {
          targetPanel.classList.add('is_active');
        }
      });
    });
  }

  // お問い合わせページ: URLパラメータから店舗名をプリセット
  var urlParams = new URLSearchParams(window.location.search);
  var shopParam = urlParams.get('shop');
  if (shopParam) {
    function preselectShop() {
      var selects = document.querySelectorAll('.bl_formField select');
      selects.forEach(function (shopSelect) {
        var options = shopSelect.querySelectorAll('option');
        options.forEach(function (option) {
          if (option.textContent.trim() === shopParam || option.value === shopParam) {
            option.selected = true;
            shopSelect.classList.add('has-value');
          }
        });
      });
    }

    // CF7がフォームを動的に初期化する場合に備え、即時実行＋遅延実行
    preselectShop();
    document.addEventListener('wpcf7ready', preselectShop);
    setTimeout(preselectShop, 500);
  }

  // 残り枠数カウントダウン（時間帯で変動）- 店舗ページCTAのみ
  const remainingSlots = document.querySelectorAll('.bl_shopCta .js_remainingSlots');
  if (remainingSlots.length > 0) {
    var updateRemainingSlots = function () {
      var hour = new Date().getHours();
      var slots;

      // 時間帯による残り枠数
      // 0:00〜11:59 → 3
      // 12:00〜15:59 → 2
      // 16:00〜23:59 → 1
      if (hour < 12) {
        slots = 3;
      } else if (hour < 16) {
        slots = 2;
      } else {
        slots = 1;
      }

      remainingSlots.forEach(function (el) {
        el.textContent = slots;
      });
    };

    updateRemainingSlots();
    // 1分ごとに更新
    setInterval(updateRemainingSlots, 60000);
  }
});
