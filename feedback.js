
        // Star rating interaction
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating');
        stars.forEach(star => {
            star.addEventListener('click', () => {
                const rating = star.getAttribute('data-rating');
                ratingInput.value = rating; // Set hidden input value
                stars.forEach(s => s.classList.remove('active'));
                for (let i = 0; i < rating; i++) {
                    stars[i].classList.add('active');
                }
            });
        });

        // Form submission (now handled by PHP, but keep JS for client-side validation if needed)
        // Removed preventDefault since we want PHP to handle it
    