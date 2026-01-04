// Header scroll efekti
window.addEventListener('scroll', function() {
  const header = document.getElementById('mainHeader');
  if (window.scrollY > 80) { // 80px'den sonra değişsin (istediğin değeri değiştir)
    header.classList.add('scrolled');
  } else {
    header.classList.remove('scrolled');
  }
});

// Sayfa yüklenirken de kontrol et
window.addEventListener('load', function() {
  const header = document.getElementById('mainHeader');
  if (window.scrollY > 80) {
    header.classList.add('scrolled');
  }
});

/**
 * MOBİL ÇOK SEVİYELİ MENÜ
 */
document.addEventListener('DOMContentLoaded', function () {
  var mobileMenu = document.getElementById('offcanvasMobileMenu');
  if (!mobileMenu) return;

  var panels = mobileMenu.querySelectorAll('.mobile-menu-panel');

  function activatePanel(id) {
    panels.forEach(function (panel) {
      if (panel.getAttribute('data-menu') === id) {
        panel.classList.add('active');
      } else {
        panel.classList.remove('active');
      }
    });
  }

  // Varsayılan olarak ana menüyü göster
  activatePanel('root');

  // İleri seviye için
  mobileMenu.addEventListener('click', function (event) {
    var targetButton = event.target.closest('[data-menu-target]');
    if (targetButton) {
      event.preventDefault();
      var nextId = targetButton.getAttribute('data-menu-target');
      if (nextId) {
        activatePanel(nextId);
      }
      return;
    }

    var backButton = event.target.closest('[data-menu-back]');
    if (backButton) {
      event.preventDefault();
      var backId = backButton.getAttribute('data-menu-back');
      if (backId) {
        activatePanel(backId);
      }
    }
  });

  // Offcanvas her açıldığında ana seviyeye dön
  mobileMenu.addEventListener('shown.bs.offcanvas', function () {
    activatePanel('root');
    document.body.classList.add('mobile-menu-open');
  });

  // Offcanvas kapandığında duyuru barını geri getir
  mobileMenu.addEventListener('hidden.bs.offcanvas', function () {
    document.body.classList.remove('mobile-menu-open');
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const items = document.querySelectorAll(".announcement-item");
  if (!items.length) return;

  let current = 0;

  setInterval(() => {
    items[current].classList.remove("active");
    items[current].classList.add("exit");

    current = (current + 1) % items.length;
    items[current].classList.add("active");

    setTimeout(() => {
      items.forEach(item => item.classList.remove("exit"));
    }, 500);
  }, 3000);
});

// Function to change main product image when thumbnail is clicked
function changeMainImage(thumbnail) {
  // Remove active class from all thumbnails
  const allThumbnails = document.querySelectorAll('.thumbnail');
  allThumbnails.forEach(thumb => thumb.classList.remove('active'));
  
  // Add active class to clicked thumbnail
  thumbnail.classList.add('active');
  
  // Get the source of the clicked thumbnail and replace common WordPress thumbnail sizes to get the full-size image
  let newSrc = thumbnail.src;
  // Remove common WordPress thumbnail sizes to get the original image
  const sizes = ['-150x150', '-100x100', '-300x300', '-768x768', '-1024x1024', '-1536x1536', '-2048x2048'];
  sizes.forEach(size => {
    newSrc = newSrc.replace(size, '');
  });
  // If the image still contains a size suffix pattern, remove it using regex
  newSrc = newSrc.replace(/-\d+x\d+(\.\w+)$/, '$1');
  
  // Update the main product image
  const mainImage = document.getElementById('main-image');
  if (mainImage) {
    mainImage.src = newSrc;
  }
}

