/**
 * File: staff-voice-slider.js
 * Usage: 先輩スタッフの声スライダー + モーダル制御
 * @requires slider-utils.js
 * @requires slider-presets.js
 */
document.addEventListener('DOMContentLoaded', function() {
  // Swiperスライダー初期化
  initSlider('.bl_staffVoiceSwiper', createSliderConfig('staffVoice'), function(swiper) {
    initSliderNav(swiper, '.bl_staffVoiceSlider_btn__prev', '.bl_staffVoiceSlider_btn__next');
  });

  // モーダル制御初期化
  initStaffVoiceModals();
});

/**
 * スタッフの声モーダル制御
 */
function initStaffVoiceModals() {
  var slides = document.querySelectorAll('.bl_staffVoiceSlide');
  var modals = document.querySelectorAll('.bl_staffVoiceModal');
  var body = document.body;

  if (!slides.length || !modals.length) return;

  // モーダルを開く
  function openModal(modal) {
    modal.setAttribute('aria-hidden', 'false');
    modal.classList.add('is_open');
    body.style.overflow = 'hidden';
  }

  // モーダルを閉じる
  function closeModal(modal) {
    modal.setAttribute('aria-hidden', 'true');
    modal.classList.remove('is_open');
    body.style.overflow = '';
  }

  // スライドクリックでモーダルを開く
  slides.forEach(function(slide) {
    slide.addEventListener('click', function() {
      var modalId = this.getAttribute('data-modal-id');
      var modal = document.getElementById(modalId);
      if (modal) {
        openModal(modal);
      }
    });
  });

  // 閉じるボタン・オーバーレイクリックで閉じる
  modals.forEach(function(modal) {
    var closeBtn = modal.querySelector('.bl_staffVoiceModal_close');
    var overlay = modal.querySelector('.bl_staffVoiceModal_overlay');

    if (closeBtn) {
      closeBtn.addEventListener('click', function() {
        closeModal(modal);
      });
    }

    if (overlay) {
      overlay.addEventListener('click', function() {
        closeModal(modal);
      });
    }
  });

  // ESCキーで閉じる
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      modals.forEach(function(modal) {
        if (modal.classList.contains('is_open')) {
          closeModal(modal);
        }
      });
    }
  });
}
