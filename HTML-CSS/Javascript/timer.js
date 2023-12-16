document.addEventListener('DOMContentLoaded', (event) => {
    const borrowedBooks = document.querySelectorAll('.borrowed-book');

    borrowedBooks.forEach(book => {
        const returnDate = new Date(book.getAttribute('data-return-date')).getTime();
        const timerElement = book.querySelector('.countdown-timer');

        // Update the countdown every second
        const interval = setInterval(function() {
            const now = new Date().getTime();
            const timeLeft = returnDate - now;

            // Time calculations for days and hours
            const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
            const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

            // Output the result in the timer element
            if (timeLeft > 0) {
                timerElement.innerHTML = days + "d " + hours + "h " + "Left" ;
                timerElement.style.backgroundColor = "green"; // Green box for active countdown
            } else {
                // If the countdown is over, display expired message
                timerElement.innerHTML = "Expired!";
                timerElement.style.backgroundColor = "red"; // Red box for expired countdown
                clearInterval(interval); // Stop the countdown
            }
        }, 1000);
    });
});
