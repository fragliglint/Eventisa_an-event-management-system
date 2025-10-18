// Events page JavaScript functionality

document.addEventListener('DOMContentLoaded', function() {
    // View toggle functionality
    const gridViewBtn = document.getElementById('gridView');
    const listViewBtn = document.getElementById('listView');
    const eventsContainer = document.getElementById('eventsContainer');
    
    if (gridViewBtn && listViewBtn && eventsContainer) {
        gridViewBtn.addEventListener('click', function() {
            eventsContainer.className = 'grid-view';
            gridViewBtn.classList.add('active');
            listViewBtn.classList.remove('active');
        });
        
        listViewBtn.addEventListener('click', function() {
            eventsContainer.className = 'list-view';
            listViewBtn.classList.add('active');
            gridViewBtn.classList.remove('active');
        });
    }
    
    // Tab functionality
    const tabs = document.querySelectorAll('.tab');
    const statusFilter = document.getElementById('statusFilter');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const tabName = this.getAttribute('data-tab');
            statusFilter.value = tabName;
            
            // Update active tab
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Submit the form
            document.getElementById('filterForm').submit();
        });
    });
    
    // Event card interactions
    const eventCards = document.querySelectorAll('.event-card');
    
    eventCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Don't trigger if clicking action buttons
            if (!e.target.closest('.event-action-btn') && !e.target.closest('.icon-action')) {
                const eventId = this.getAttribute('data-id');
                viewEventDetails(eventId);
            }
        });
    });
    
    // Search functionality
    const searchInput = document.getElementById('search');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500);
        });
    }
    
    // Category filter change
    const categoryFilter = document.getElementById('categoryFilter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
});

function viewEventDetails(eventId) {
    // In a real implementation, this would navigate to event details page
    console.log('Viewing event details for ID:', eventId);
    // window.location.href = `event_details.php?id=${eventId}`;
}

// Export event data
function exportEvents(format = 'csv') {
    alert(`Exporting events as ${format.toUpperCase()}`);
    // In a real implementation, this would generate and download a file
}

// Delete event with confirmation
function deleteEvent(eventId, eventTitle) {
    if (confirm(`Are you sure you want to delete "${eventTitle}"? This action cannot be undone.`)) {
        // In a real implementation, this would make an API call to delete the event
        fetch(`delete_event.php?id=${eventId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove event card from DOM
                const eventCard = document.querySelector(`.event-card[data-id="${eventId}"]`);
                if (eventCard) {
                    eventCard.remove();
                }
                alert('Event deleted successfully');
            } else {
                alert('Error deleting event: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting event');
        });
    }
}

// Duplicate event
function duplicateEvent(eventId) {
    if (confirm('Create a copy of this event?')) {
        // In a real implementation, this would make an API call to duplicate the event
        fetch(`duplicate_event.php?id=${eventId}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Event duplicated successfully');
                // Refresh the page or add the new event to the grid
                location.reload();
            } else {
                alert('Error duplicating event: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error duplicating event');
        });
    }
}

// Quick status update
function updateEventStatus(eventId, newStatus) {
    // In a real implementation, this would make an API call to update the status
    fetch(`update_event_status.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: eventId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the status badge in the DOM
            const statusBadge = document.querySelector(`.event-card[data-id="${eventId}"] .event-status`);
            if (statusBadge) {
                statusBadge.textContent = newStatus;
                statusBadge.className = `event-status ${newStatus.toLowerCase()}`;
            }
            alert('Event status updated');
        } else {
            alert('Error updating event status: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating event status');
    });
}