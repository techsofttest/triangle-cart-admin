document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('headerSearchInput');
    const searchResults = document.getElementById('searchResults');
    let debounceTimer;

    if (!searchInput || !searchResults) return;

    searchInput.addEventListener('input', function () {
        const keyword = this.value.trim();

        clearTimeout(debounceTimer);

        if (keyword.length < 2) {
            searchResults.classList.add('d-none');
            searchResults.innerHTML = '';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetchSearchSuggestions(keyword);
        }, 300);
    });

    // Handle Enter key
    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            const keyword = this.value.trim();
            if (keyword) {
                window.location.href = `/products?search=${encodeURIComponent(keyword)}`;
            }
        }
    });

    // Handle search icon click
    const searchIcon = document.querySelector('.search-icon');
    if (searchIcon) {
        searchIcon.addEventListener('click', function () {
            const keyword = searchInput.value.trim();
            if (keyword) {
                window.location.href = `/products?search=${encodeURIComponent(keyword)}`;
            } else {
                searchInput.focus();
            }
        });
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('d-none');
        }
    });

    async function fetchSearchSuggestions(keyword) {
        try {
            const response = await fetch(`/search-suggestions?keyword=${encodeURIComponent(keyword)}`);
            const data = await response.json();
            renderResults(data, keyword);
        } catch (error) {
            console.error('Search error:', error);
        }
    }

    function renderResults(data, keyword) {
        const hasResults = data.products.length > 0 || data.categories.length > 0 || data.collections.length > 0 || data.subcategories.length > 0;

        if (!hasResults) {
            searchResults.innerHTML = `<div class="p-3 text-muted text-center" style="font-size: 13px;">No results found for "${keyword}"</div>`;
            searchResults.classList.remove('d-none');
            return;
        }

        let html = '';

        // Categories
        if (data.categories.length > 0 || data.subcategories.length > 0) {
            html += `<div class="search-category-section">
                        <div class="search-category-title">Categories</div>`;
            
            data.categories.forEach(item => {
                html += renderItem(item);
            });

            data.subcategories.forEach(item => {
                html += renderItem(item);
            });

            html += `</div>`;
        }

        // Collections
        if (data.collections.length > 0) {
            html += `<div class="search-category-section">
                        <div class="search-category-title">Collections</div>`;
            data.collections.forEach(item => {
                html += renderItem(item);
            });
            html += `</div>`;
        }

        // Products
        if (data.products.length > 0) {
            html += `<div class="search-category-section">
                        <div class="search-category-title">Products</div>`;
            data.products.forEach(item => {
                html += renderProductItem(item);
            });
            html += `</div>`;
        }

        searchResults.innerHTML = html;
        searchResults.classList.remove('d-none');
    }

    function renderItem(item) {
        return `
            <a href="${item.url}" class="search-item d-flex align-items-center gap-3">
                <div class="search-item-img">
                    <img src="${item.image}" alt="${item.name}">
                </div>
                <div class="search-item-info">
                    <div class="search-item-name">${item.name}</div>
                </div>
            </a>
        `;
    }

    function renderProductItem(item) {
        return `
            <a href="${item.url}" class="search-item d-flex align-items-center gap-3">
                <div class="search-item-img">
                    <img src="${item.image}" alt="${item.name}">
                </div>
                <div class="search-item-info">
                    <div class="search-item-name">${item.name}</div>
                    <div class="search-item-extra">₹${item.price}</div>
                </div>
            </a>
        `;
    }
});
