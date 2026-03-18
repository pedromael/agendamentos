const btnMenu = document.getElementById('btnMenu');
const sidebar = document.getElementById('sidebar');

btnMenu.addEventListener('click', () => {
  if (window.innerWidth < 992) {
    sidebar.classList.toggle('show');
  }
});
document.addEventListener('click', (e) => {
  if (window.innerWidth < 992) {
    if (!sidebar.contains(e.target) && !btnMenu.contains(e.target)) {
      sidebar.classList.remove('show');
    }
  }
});

window.addEventListener('resize', () => {
  if (window.innerWidth >= 992) {
    sidebar.classList.remove('show');
  }
});