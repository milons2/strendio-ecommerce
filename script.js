const infoText = document.getElementById('info-text');
const messages = [
    "Website Under Maintenance",
    "We are improving your shopping experience",
    "New products arriving soon - Stay Tuned !"
];
let currentMessageIndex = 0;
function changeMessage() {
    infoText.style.opacity = "0";
    //infoText.classList.remove('active'); // Hide the current message

    setTimeout(() => { // Use setTimeout for smooth transition
        infoText.textContent = messages[currentMessageIndex];
        //infoText.classList.add('active'); // Show the new message
        currentMessageIndex = (currentMessageIndex + 1) % messages.length;
        infoText.style.opacity = "1";
    }, 1500); // Wait for the fade-out transition (1s)
}
// Initial display
changeMessage();

setInterval(changeMessage, 5000); // Change every 10 seconds

document.addEventListener("DOMContentLoaded", () => {
    const navToggle = document.querySelector(".nav-toggle");
    const navLinks = document.querySelector(".nav-links");
  
    // Toggle Mobile Menu
    navToggle.addEventListener("click", () => {
      navLinks.classList.toggle("show");
    });
  
    // Close menu when clicking a link (optional)
    navLinks.addEventListener("click", (e) => {
      if (e.target.tagName === "A") {
        navLinks.classList.remove("show");
      }
    });
});

function updateCart(newIconClass, cartCount) {
    // Update cart icon
    const cartIcon = document.querySelector("#cart-icon i");
    if (cartIcon) {
      cartIcon.className = newIconClass;
    }
  
    // Update cart count (if exists)
    const cartCountElement = document.querySelector("#cart-count");
    if (cartCountElement) {
      cartCountElement.textContent = cartCount;
    }
}
  
// Simulate an AJAX call
setTimeout(() => {
    // Simulated server response
    const newIconClass = "fas fa-cart-arrow-down"; // New icon class
    const newCartCount = 5; // Updated cart count
  
    updateCart(newIconClass, newCartCount);
}, 2000);

// End of Head
document.addEventListener('DOMContentLoaded', () => {
  const slides = document.querySelectorAll('.banner-slide');
  const nextBtn = document.getElementById('next-btn');
  const prevBtn = document.getElementById('prev-btn');
  let currentSlide = 0;

  function updateSlides() {
    slides.forEach((slide, index) => {
      slide.classList.remove('active', 'prev');
      if (index === currentSlide) {
        slide.classList.add('active');
      } 
      else if (index === (currentSlide - 1 + slides.length) % slides.length) {
        slide.classList.add('prev');
      }
    });
  }

  // Next Slide
  function nextSlide() {
      currentSlide = (currentSlide + 1) % slides.length;
      updateSlides();
  }

  // Previous Slide
  function prevSlide() {
      currentSlide = (currentSlide - 1 + slides.length) % slides.length;
      updateSlides();
  }

  // Auto-Slide
  let autoSlideInterval = setInterval(nextSlide, 5000);

  // Event Listeners
  nextBtn.addEventListener('click', () => { 
    clearInterval(autoSlideInterval); // Reset auto-slide on manual interaction
    nextSlide();
    autoSlideInterval = setInterval(nextSlide, 5000);
  });

  prevBtn.addEventListener('click', () => {
    clearInterval(autoSlideInterval); // Reset auto-slide on manual interaction
    prevSlide();
    autoSlideInterval = setInterval(nextSlide, 5000);
  });

  // Initialize the first slide
  updateSlides();
});
