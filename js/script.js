// Confirm delete action
function confirmDelete(id, type) {
    if (confirm(`Are you sure you want to delete this ${type}?`)) {
        document.getElementById(`delete-${type}-${id}`).submit();
    }
}

// Toggle advanced search options
function toggleAdvancedSearch() {
    const advancedSearch = document.getElementById('advanced-search');
    if (advancedSearch.style.display === 'none' || !advancedSearch.style.display) {
        advancedSearch.style.display = 'block';
    } else {
        advancedSearch.style.display = 'none';
    }
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Date validation for policy start and end dates
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');
    
    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('change', function() {
            endDateInput.min = startDateInput.value;
        });
        
        endDateInput.addEventListener('change', function() {
            if (new Date(endDateInput.value) <= new Date(startDateInput.value)) {
                alert('End date must be after start date');
                endDateInput.value = '';
            }
        });
    }
});
