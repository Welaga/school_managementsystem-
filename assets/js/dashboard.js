// Dashboard specific JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize datepickers
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        if (!input.value) {
            input.value = new Date().toISOString().substr(0, 10);
        }
    });
    
    // Initialize timepickers
    const timeInputs = document.querySelectorAll('input[type="time"]');
    timeInputs.forEach(input => {
        if (!input.value) {
            const now = new Date();
            input.value = now.toTimeString().substr(0, 5);
        }
    });
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let valid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = '#e74c3c';
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
    
    // Dynamic form elements
    const dynamicSelects = document.querySelectorAll('select[data-dynamic]');
    dynamicSelects.forEach(select => {
        select.addEventListener('change', function() {
            const targetId = this.getAttribute('data-target');
            const value = this.value;
            
            if (targetId && value) {
                // This would typically make an AJAX request to fetch dynamic options
                console.log(`Loading options for ${targetId} based on selection: ${value}`);
                
                // Simulate loading
                const target = document.getElementById(targetId);
                if (target) {
                    target.innerHTML = '<option value="">Loading...</option>';
                    
                    // Simulate AJAX response
                    setTimeout(() => {
                        target.innerHTML = `
                            <option value="">Select an option</option>
                            <option value="1">Option 1</option>
                            <option value="2">Option 2</option>
                            <option value="3">Option 3</option>
                        `;
                    }, 500);
                }
            }
        });
    });
    
    // Tab functionality
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabContentId = this.getAttribute('data-tab');
            
            // Hide all tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Deactivate all tabs
            document.querySelectorAll('.tab').forEach(t => {
                t.classList.remove('active');
            });
            
            // Activate current tab
            this.classList.add('active');
            
            // Show current tab content
            if (tabContentId) {
                const tabContent = document.getElementById(tabContentId);
                if (tabContent) {
                    tabContent.classList.add('active');
                }
            }
        });
    });
    
    // Auto-dismiss alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
});

// AJAX function for dynamic content loading
function loadContent(url, containerId, callback) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    container.innerHTML = '<div class="loading">Loading...</div>';
    
    fetch(url)
        .then(response => response.text())
        .then(data => {
            container.innerHTML = data;
            if (callback && typeof callback === 'function') {
                callback();
            }
        })
        .catch(error => {
            container.innerHTML = `<div class="error">Error loading content: ${error}</div>`;
        });
}

// Function to update status via AJAX
function updateStatus(url, id, status, callback) {
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${id}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (callback && typeof callback === 'function') {
                callback(data);
            } else {
                showNotification('Status updated successfully', 'success');
                // Reload after 1 second
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } else {
            showNotification('Error updating status: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Error: ' + error, 'error');
    });
}