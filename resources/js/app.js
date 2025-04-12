// Contact provider functionality
// document.addEventListener('DOMContentLoaded', function() {
    // Contact provider button
    // const contactButtons = document.querySelectorAll('.contact-provider-btn');
    // contactButtons.forEach(button => {
    //     button.addEventListener('click', function() {
    //         const phoneNumber = this.dataset.phone;
    //         document.getElementById('providerPhoneNumber').textContent = phoneNumber;
            
            // const callBtn = document.getElementById('callProviderBtn');
            // const messageBtn = document.getElementById('messageProviderBtn');
            
            // callBtn.href = `tel:${phoneNumber}`;
            // messageBtn.href = `sms:${phoneNumber}`;
            
    //         $('#contactProviderModal').modal('show');
    //     });
    // });

    // Star rating input
    const starInputs = document.querySelectorAll('.star-rating-input input');
    starInputs.forEach(input => {
        input.addEventListener('change', function() {
            const rating = this.value;
            const labels = document.querySelectorAll('.star-rating-input label');
            
            labels.forEach((label, index) => {
                if (index < rating) {
                    label.innerHTML = '<i class="fas fa-star"></i>';
                } else {
                    label.innerHTML = '<i class="far fa-star"></i>';
                }
            });
        });
    });

    // Initialize star rating display
    const starLabels = document.querySelectorAll('.star-rating-input label');
    starLabels.forEach(label => {
        label.innerHTML = '<i class="far fa-star"></i>';
    });
