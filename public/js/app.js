document.addEventListener('DOMContentLoaded', function () {
    console.log('Mộc Nhiên Stationery loaded.');
});

function toggleMobileMenu() {
    const mainNav = document.getElementById('main-nav');
    if (mainNav) {
        mainNav.classList.toggle('active');
    }
}

function toggleMobileSearch() {
    const header = document.querySelector('.header');
    if (header) {
        header.classList.toggle('searching');
    }
}
