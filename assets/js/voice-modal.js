/**
 * File: voice-modal.js
 * Usage: お客様の声モーダル機能
 * 使用ページ: single-shop, single-symptoms
 */

(function () {
  'use strict';

  function initVoiceModal() {
    var modal = document.querySelector('.js_voiceModal');
    var modalInner = modal ? modal.querySelector('.bl_voiceModal_inner') : null;
    var openBtns = document.querySelectorAll('.js_voiceModalOpen');
    var closeBtns = document.querySelectorAll('.js_voiceModalClose');

    if (!modal || !modalInner || openBtns.length === 0) return;

    // モーダルデータを統合
    var voiceData = [];
    if (window.voiceModalData && Array.isArray(window.voiceModalData)) {
      window.voiceModalData.forEach(function (dataArray) {
        if (Array.isArray(dataArray)) {
          voiceData = voiceData.concat(dataArray);
        }
      });
    }

    var templateUri = window.voiceModalTemplateUri || '';

    // モーダルを開く
    openBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var voiceId = parseInt(this.dataset.voiceId, 10);
        var data = voiceData.find(function (item) {
          return item.id === voiceId;
        });

        if (!data) return;

        // モーダル内容を生成
        var html = '<div class="bl_voiceModal_header">';

        // サムネイル
        html += '<div class="bl_voiceModal_thumb">';
        if (data.has_thumbnail && data.thumbnail_large) {
          html += data.thumbnail_large;
        } else {
          html +=
            '<img src="' +
            templateUri +
            '/assets/img/no-image.png" alt="">';
        }
        html += '</div>';

        // 情報部分
        html += '<div class="bl_voiceModal_info">';
        html +=
          '<h3 class="bl_voiceModal_ttl zenmaru">' +
          escapeHtml(data.title) +
          '</h3>';

        if (data.voice_name) {
          html +=
            '<p class="bl_voiceModal_name">' +
            escapeHtml(data.voice_name) +
            '</p>';
        }

        html += '<dl class="bl_voiceModal_meta">';
        if (data.symptom_names && data.symptom_names.length > 0) {
          html +=
            '<div class="bl_voiceModal_metaRow"><dt>症状</dt><dd>' +
            escapeHtml(data.symptom_names.join('、')) +
            '</dd></div>';
        }
        if (data.shop_names && data.shop_names.length > 0) {
          html +=
            '<div class="bl_voiceModal_metaRow"><dt>対応店舗</dt><dd>' +
            escapeHtml(data.shop_names.join('、')) +
            '</dd></div>';
        }
        html += '</dl>';
        html += '</div>'; // .bl_voiceModal_info
        html += '</div>'; // .bl_voiceModal_header

        // 本文
        if (data.content) {
          html +=
            '<div class="bl_voiceModal_body wp-content">' + data.content + '</div>';
        }

        // 注釈
        html +=
          '<p class="bl_voiceModal_note">※施術効果には個人差があります。</p>';

        modalInner.innerHTML = html;
        modal.classList.add('is_open');
        document.body.classList.add('is_modalOpen');
      });
    });

    // モーダルを閉じる
    closeBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        closeModal();
      });
    });

    // Escキーで閉じる
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modal.classList.contains('is_open')) {
        closeModal();
      }
    });

    function closeModal() {
      modal.classList.remove('is_open');
      document.body.classList.remove('is_modalOpen');
    }

    // HTMLエスケープ
    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
    }
  }

  // DOMContentLoaded で初期化
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initVoiceModal);
  } else {
    initVoiceModal();
  }
})();
