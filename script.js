// Select the hamburger menu and the navigation links container
const hamburger = document.querySelector('.hamburger');
const navLinks = document.querySelector('.nav-links');

// Toggle the 'active' class on the nav links when the hamburger is clicked
hamburger.addEventListener('click', () => {
  navLinks.classList.toggle('active');
});
