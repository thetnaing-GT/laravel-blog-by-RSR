document.addEventListener('DOMContentLoaded', function() {
    // Initialize category select elements
    const initCategorySelect = () => {
        const categorySelect = document.getElementById('categorySelect');
        const newCategoryInput = document.getElementById('newCategoryInput');

        if (categorySelect && newCategoryInput) {
            // When existing category is selected, clear new category input
            categorySelect.addEventListener('change', function() {
                if (this.value) {
                    newCategoryInput.value = '';
                }
            });
            
            // When typing in new category, clear existing selection
            newCategoryInput.addEventListener('input', function() {
                if (this.value) {
                    categorySelect.value = '';
                }
            });
        }
    };

    // Initialize the function
    initCategorySelect();
});