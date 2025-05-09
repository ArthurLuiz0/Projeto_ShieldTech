export function setupAnimations() {
    // Testimonial slider functionality
    const testimonials = document.querySelectorAll('.testimonial');
    const dots = document.querySelectorAll('.dot');
    const prevButton = document.querySelector('.prev-testimonial');
    const nextButton = document.querySelector('.next-testimonial');
    
    let currentTestimonial = 0;
    const totalTestimonials = testimonials.length;
  
    function showTestimonial(index) {
      testimonials.forEach(testimonial => testimonial.classList.remove('active'));
      dots.forEach(dot => dot.classList.remove('active'));
      
      testimonials[index].classList.add('active');
      dots[index].classList.add('active');
      currentTestimonial = index;
    }
  
    if (prevButton) {
      prevButton.addEventListener('click', () => {
        let index = currentTestimonial - 1;
        if (index < 0) index = totalTestimonials - 1;
        showTestimonial(index);
      });
    }
  
    if (nextButton) {
      nextButton.addEventListener('click', () => {
        let index = currentTestimonial + 1;
        if (index >= totalTestimonials) index = 0;
        showTestimonial(index);
      });
    }
  
    dots.forEach((dot, index) => {
      dot.addEventListener('click', () => {
        showTestimonial(index);
      });
    });
    
    // Auto rotate testimonials every 6 seconds
    setInterval(() => {
      let index = currentTestimonial + 1;
      if (index >= totalTestimonials) index = 0;
      showTestimonial(index);
    }, 6000);
  
    // Animate elements when they come into view
    const animateOnScroll = () => {
      const elements = document.querySelectorAll('[data-aos]');
      
      elements.forEach(element => {
        const elementPosition = element.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        
        if (elementPosition < windowHeight * 0.85) {
          element.classList.add('aos-animate');
        }
      });
    };
  
    // Initial check for elements in view
    animateOnScroll();
    
    // Check for elements in view on scroll
    window.addEventListener('scroll', animateOnScroll);
  }



  import { initNavigation } from './navigation.js';
import { setupAnimations } from './animations.js';
import { setupLoginModal } from './login.js';

// Initialize all modules when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  initNavigation();
  setupAnimations();
  setupLoginModal();
});


export function initNavigation() {
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    const navLinks = document.querySelectorAll('.nav-list a');
  
    // Toggle mobile menu
    if (menuToggle) {
      menuToggle.addEventListener('click', () => {
        menuToggle.classList.toggle('active');
        mainNav.classList.toggle('active');
      });
    }
  
    // Close menu when nav link is clicked
    navLinks.forEach(link => {
      link.addEventListener('click', () => {
        menuToggle.classList.remove('active');
        mainNav.classList.remove('active');
      });
    });
  
    // Handle header scroll effect
    const header = document.querySelector('.header');
    let lastScrollY = window.scrollY;
  
    window.addEventListener('scroll', () => {
      if (window.scrollY > 100) {
        header.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)';
        header.style.backgroundColor = 'rgba(255, 255, 255, 0.98)';
      } else {
        header.style.boxShadow = '0 1px 3px rgba(0, 0, 0, 0.05)';
        header.style.backgroundColor = 'rgba(255, 255, 255, 0.9)';
      }
  
      // Hide/show header on scroll
      if (window.innerWidth <= 768) {
        if (window.scrollY > lastScrollY + 50) {
          header.style.transform = 'translateY(-100%)';
        } else if (window.scrollY < lastScrollY - 10) {
          header.style.transform = 'translateY(0)';
        }
        
        lastScrollY = window.scrollY;
      }
    });
  
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        
        if (targetElement) {
          const headerHeight = document.querySelector('.header').offsetHeight;
          const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;
          
          window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
          });
        }
      });
    });
  }



  export function setupLoginModal() {
    const loginButtons = document.querySelectorAll('.login-button');
    const loginModal = document.querySelector('.login-modal');
    const closeModalButton = document.querySelector('.close-modal');
    const loginForm = document.querySelector('.login-form');
  
    // Open login modal when login button is clicked
    loginButtons.forEach(button => {
      button.addEventListener('click', () => {
        loginModal.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
      });
    });
  
    // Close modal when close button is clicked
    if (closeModalButton) {
      closeModalButton.addEventListener('click', () => {
        loginModal.classList.remove('active');
        document.body.style.overflow = ''; // Re-enable scrolling
      });
    }
  
    // Close modal when clicking outside the modal content
    loginModal.addEventListener('click', (e) => {
      if (e.target === loginModal) {
        loginModal.classList.remove('active');
        document.body.style.overflow = ''; // Re-enable scrolling
      }
    });
  
    // Handle login form submission
    if (loginForm) {
      loginForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;
        
        // In a real application, you would send this data to your server
        console.log('Login attempt:', { email, password });
        
        // For demo purposes, show a success message
        alert('Login functionality would connect to your authentication system in a real application.');
        
        // Close the modal after login
        loginModal.classList.remove('active');
        document.body.style.overflow = ''; // Re-enable scrolling
      });
    }
  
    // Close modal with Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && loginModal.classList.contains('active')) {
        loginModal.classList.remove('active');
        document.body.style.overflow = ''; // Re-enable scrolling
      }
    });
  }