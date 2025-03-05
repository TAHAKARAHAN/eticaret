document.addEventListener('DOMContentLoaded', function() {
    // Parse URL parameters to check for social login errors
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    
    // Display error messages if any
    if (error) {
        let errorMessage = '';
        
        switch(error) {
            case 'no_email':
                errorMessage = 'Sosyal medya hesabınızdan e-posta bilgisi alınamadı. Lütfen normal kayıt formunu kullanın.';
                break;
            case 'token':
                errorMessage = 'Giriş işlemi sırasında bir hata oluştu. Lütfen tekrar deneyin.';
                break;
            case 'register':
                errorMessage = 'Hesap oluşturulurken bir hata oluştu. Lütfen tekrar deneyin.';
                break;
            default:
                errorMessage = 'Bir hata oluştu. Lütfen tekrar deneyin.';
        }
        
        // Create error notification
        if (errorMessage) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger';
            errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + errorMessage;
            
            // Insert error message at the top of the login form
            const formContainer = document.querySelector('.box_account .form_container');
            formContainer.insertBefore(errorDiv, formContainer.firstChild);
        }
    }
    
    // Check if user is coming from a new registration
    const yeni = urlParams.get('yeni');
    if (yeni === '1') {
        // Show welcome message for new social media registrations
        const welcomeDiv = document.createElement('div');
        welcomeDiv.className = 'alert alert-success';
        welcomeDiv.innerHTML = '<i class="fas fa-check-circle"></i> Sosyal medya hesabınızla başarıyla kayıt oldunuz!';
        
        // Add to page
        const pageHeader = document.querySelector('.page_header');
        if (pageHeader) {
            pageHeader.appendChild(welcomeDiv);
        }
    }
});
