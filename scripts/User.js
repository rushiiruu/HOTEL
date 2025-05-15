// Form validation
document.getElementById('account-form').addEventListener('submit', function(event) {
    const fname = document.getElementById('Fname').value.trim();
    const lname = document.getElementById('Lname').value.trim();
    const username = document.getElementById('username').value.trim();
    
    if (fname === '' || lname === '' || username === '') {
        event.preventDefault();
        alert('Please fill in all required fields.');
    }
});
