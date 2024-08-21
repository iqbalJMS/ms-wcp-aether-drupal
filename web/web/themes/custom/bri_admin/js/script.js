document.addEventListener('DOMContentLoaded', function() {
  var modal = document.getElementById('imageModal');
  var btns = document.querySelectorAll('.open-modal');
  var span = document.querySelector('.close-modal');
  var modalImg = document.getElementById('modalImage');

  btns.forEach(function (btn) {
    btn.onclick = function() {
      modal.style.display = 'flex';
      modalImg.src = btn.getAttribute('data-image-url');
    }
  })

  span.onclick = function() {
    modal.style.display = 'none';
  }

  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = 'none';
    }
  }
});