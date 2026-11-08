
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    // Function to toggle the display of the lead add form and update button text
    function toggleLeadForm() {
        var leadForm = document.getElementById('leadForm');
        var toggleFormBtn = document.getElementById('toggleFormBtn');

        if (!leadForm || !toggleFormBtn) {
            return;
        }
        
        if (leadForm.style.display === 'none' || leadForm.style.display === '') {
            leadForm.style.display = 'block';
            toggleFormBtn.textContent = 'Close Form';
        } else {
            leadForm.style.display = 'none';
            toggleFormBtn.textContent = 'Add Lead';
        }
    }

    var toggleFormBtn = document.getElementById('toggleFormBtn');
    if (toggleFormBtn) {
        toggleFormBtn.addEventListener('click', toggleLeadForm);
    }
</script>
</body>
</html>