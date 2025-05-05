document.addEventListener('DOMContentLoaded', function() {
    // Set initial active tab
    const pendingTab = document.getElementById('pending-tab');
    const approvedTab = document.getElementById('approved-tab');
    
    if (pendingTab) {
        pendingTab.classList.add('bg-blue-500', 'text-white');
    }

    // Add click event listeners to tabs
    if (pendingTab) {
        pendingTab.addEventListener('click', function() {
            showSection('pending-renters');
        });
    }

    if (approvedTab) {
        approvedTab.addEventListener('click', function() {
            showSection('approved-renters');
        });
    }
});

function showSection(sectionId) {
    // Hide all sections
    const pendingSection = document.getElementById('pending-renters');
    const approvedSection = document.getElementById('approved-renters');
    const pendingTab = document.getElementById('pending-tab');
    const approvedTab = document.getElementById('approved-tab');

    if (pendingSection && approvedSection) {
        pendingSection.classList.add('hidden');
        approvedSection.classList.add('hidden');
    }

    // Show selected section
    const selectedSection = document.getElementById(sectionId);
    if (selectedSection) {
        selectedSection.classList.remove('hidden');
    }

    // Update tab styles
    if (pendingTab && approvedTab) {
        pendingTab.classList.remove('bg-blue-500', 'text-white');
        approvedTab.classList.remove('bg-blue-500', 'text-white');

        // Add active style to selected tab
        if (sectionId === 'pending-renters') {
            pendingTab.classList.add('bg-blue-500', 'text-white');
        } else {
            approvedTab.classList.add('bg-blue-500', 'text-white');
        }
    }
} 