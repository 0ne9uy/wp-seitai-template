/**
 * File: shop-list.js
 * Usage: 店舗一覧タブ切り替え（フロントページセクション用）
 * 使用ページ: front-page
 */

document.addEventListener('DOMContentLoaded', function() {
  var section = document.getElementById('shop-list');
  if (!section) return;

  var tabs = section.querySelectorAll('.bl_shopList_tab');
  var panels = section.querySelectorAll('.bl_shopList_panel');

  if (tabs.length === 0 || panels.length === 0) return;

  tabs.forEach(function(tab) {
    tab.addEventListener('click', function() {
      var area = this.getAttribute('data-area');

      tabs.forEach(function(t) {
        t.classList.remove('is_active');
        t.setAttribute('aria-selected', 'false');
      });
      this.classList.add('is_active');
      this.setAttribute('aria-selected', 'true');

      panels.forEach(function(panel) {
        panel.classList.remove('is_active');
      });
      var targetPanel = document.getElementById('shopList-' + area);
      if (targetPanel) {
        targetPanel.classList.add('is_active');
      }
    });
  });
});
