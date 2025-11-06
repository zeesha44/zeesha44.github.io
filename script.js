// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Navbar background change on scroll
window.addEventListener('scroll', function() {
    if (window.scrollY > 50) {
        document.querySelector('.navbar').classList.add('bg-white');
    } else {
        document.querySelector('.navbar').classList.remove('bg-white');
    }
});

// Initialize hero carousel explicitly to ensure auto-slide every 3 seconds
document.addEventListener('DOMContentLoaded', function() {
    var heroCarousel = document.querySelector('#heroCarousel');
    if (heroCarousel) {
        var carousel = new bootstrap.Carousel(heroCarousel, {
            interval: 3000,
            wrap: true,
            ride: 'carousel'
        });
    }

    // Multi-step form functionality
    const proprietorshipRadios = document.querySelectorAll('input[name="proprietorshipType"]');
    proprietorshipRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const step3 = document.getElementById('step3');
            const step4 = document.getElementById('step4');
            if (this.value === 'sole') {
                step3.style.display = 'block';
                step4.style.display = 'none';
            } else if (this.value === 'partnership') {
                step3.style.display = 'none';
                step4.style.display = 'block';
            }
        });
    });

    // School level other option
    const schoolLevelSelect = document.getElementById('schoolLevel');
    const otherLevelDiv = document.getElementById('otherLevelDiv');
    schoolLevelSelect.addEventListener('change', function() {
        if (this.value === 'other') {
            otherLevelDiv.style.display = 'block';
        } else {
            otherLevelDiv.style.display = 'none';
        }
    });
});
