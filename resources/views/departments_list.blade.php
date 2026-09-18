<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchBox = document.getElementById('searchBox');
        const searchInput = document.getElementById('searchInput');
        let items = Array.from(document.querySelectorAll('.dprt-item'));
        if (items.length === 0) return;

        // Зчитуємо збережений індекс підрозділу з сесії
        let savedIndex = sessionStorage.getItem('active_dprt_index');
        let currentIndex = savedIndex !== null ? parseInt(savedIndex, 10) : 0;

        if (isNaN(currentIndex) || currentIndex < 0 || currentIndex >= items.length) {
            currentIndex = 0;
        }

        function openDepartmentAll(item, index) {
            sessionStorage.setItem('active_dprt_index', index);
            const id = item.getAttribute('data-id');
            if (id) {
                window.location.href = `/working/list/all?departmn=${id}`;
            }
        }

        // Оновлена функція з параметром позиціонування blockPosition ('center' або 'nearest')
        function setActiveItem(index, isInitial = false) {
            const visibleItems = items.filter(item => item.style.display !== 'none');
            if (visibleItems.length === 0) return;

            items.forEach(item => item.classList.remove('active'));

            if (index < 0) index = 0;
            if (index >= visibleItems.length) index = visibleItems.length - 1;

            currentIndex = index;
            const activeItem = visibleItems[currentIndex];
            activeItem.classList.add('active');

            // Зберігаємо поточний індекс у сесії
            const originalIndex = items.indexOf(activeItem);
            if (originalIndex !== -1) {
                sessionStorage.setItem('active_dprt_index', originalIndex);
            }

            // При початковому відновленні центруємо елемент у вікні (block: 'center')
            activeItem.scrollIntoView({
                behavior: isInitial ? 'auto' : 'smooth',
                block: isInitial ? 'center' : 'nearest'
            });
        }

        // Початкове відновлення позиції з центруванням
        setTimeout(() => {
            setActiveItem(currentIndex, true);
        }, 50);

        window.toggleSearch = function() {
            if (searchBox.style.display === 'block') {
                searchBox.style.display = 'none';
                searchInput.value = '';
                items.forEach(item => item.style.display = 'block');
                setActiveItem(currentIndex, true);
            } else {
                searchBox.style.display = 'block';
                searchInput.focus();
            }
        };

        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();

            items.forEach(item => {
                const name = item.getAttribute('data-name') || '';
                if (name.includes(query)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });

            setActiveItem(0, true);
        });

        items.forEach((item, index) => {
            item.addEventListener('click', function (e) {
                if (e.target.tagName === 'A') {
                    sessionStorage.setItem('active_dprt_index', index);
                    return;
                }

                const visibleItems = items.filter(i => i.style.display !== 'none');
                const idx = visibleItems.indexOf(item);
                if (idx !== -1) setActiveItem(idx, false);
            });

            item.addEventListener('dblclick', function (e) {
                if (e.target.tagName === 'A') return;
                openDepartmentAll(item, index);
            });
        });

        document.addEventListener('keydown', function (e) {
            if (document.activeElement === searchInput && e.key !== 'Enter' && e.key !== 'ArrowDown') {
                return;
            }

            const visibleItems = items.filter(i => i.style.display !== 'none');

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (currentIndex < visibleItems.length - 1) setActiveItem(currentIndex + 1, false);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (currentIndex > 0) setActiveItem(currentIndex - 1, false);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (visibleItems[currentIndex]) openDepartmentAll(visibleItems[currentIndex], items.indexOf(visibleItems[currentIndex]));
            }
        });
    });
</script>
