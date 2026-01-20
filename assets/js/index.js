// File: index.js
// Description: /inc/enque-my-scripts.phpでこのファイルを読み込んで、/functions.phpで実行しています。

document.addEventListener('DOMContentLoaded', function() {
  // ハンバーガーメニューの制御
  const hamburger = document.querySelector('.js_humb');
  const nav = document.querySelector('.bl_header_nav');
  const body = document.body;

  if (hamburger && nav) {
    hamburger.addEventListener('click', function() {
      hamburger.classList.toggle('is_active');
      nav.classList.toggle('is_open');
      body.classList.toggle('is_menuOpen');
    });

    // メニュー内のリンクをクリックしたらメニューを閉じる
    const navLinks = nav.querySelectorAll('a');
    navLinks.forEach(function(link) {
      link.addEventListener('click', function() {
        hamburger.classList.remove('is_active');
        nav.classList.remove('is_open');
        body.classList.remove('is_menuOpen');
      });
    });
  }
});
