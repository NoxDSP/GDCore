// GDCore Admin Panel JavaScript

function rateLevel(levelID) {
    const stars = prompt('Enter stars (0-10):');
    if (stars !== null) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="rate">
            <input type="hidden" name="levelID" value="${levelID}">
            <input type="hidden" name="stars" value="${stars}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this?');
}
