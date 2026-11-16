(function () {
    var textarea = document.getElementById('motivering');
    var counter = document.getElementById('ordraknare');

    function updateWordCount() {
        if (!textarea || !counter) {
            return;
        }
        var value = textarea.value.trim();
        var words = value === '' ? 0 : value.split(/\s+/).length;
        counter.textContent = words + ' ord';
        counter.classList.toggle('over', words > 50);
        if (words > 50) {
            counter.textContent = words + ' ord – för många';
        }
    }

    if (textarea) {
        textarea.addEventListener('input', updateWordCount);
        updateWordCount();
    }

    var options = document.querySelectorAll('.option input');
    options.forEach(function (input) {
        function sync() {
            var label = input.closest('.option');
            if (label) {
                label.classList.toggle('selected', input.checked);
            }
        }

        input.addEventListener('change', function () {
            if (input.type === 'radio') {
                document.querySelectorAll('input[name="' + input.name + '"]').forEach(function (other) {
                    var label = other.closest('.option');
                    if (label) {
                        label.classList.remove('selected');
                    }
                });
            }
            sync();
        });

        sync();
    });

    var memCards = document.querySelectorAll('[data-mem-card]');

    function setMemCard(card, open) {
        var button = card.querySelector('[data-mem-toggle]');
        card.classList.toggle('is-collapsed', !open);
        if (button) {
            button.setAttribute('aria-expanded', open ? 'true' : 'false');
            button.textContent = open ? 'Dölj mall' : 'Visa mall';
        }
    }

    memCards.forEach(function (card) {
        var button = card.querySelector('[data-mem-toggle]');
        if (button) {
            button.addEventListener('click', function () {
                setMemCard(card, card.classList.contains('is-collapsed'));
            });
        }
    });

    document.querySelectorAll('[data-mem-all]').forEach(function (button) {
        button.addEventListener('click', function () {
            var open = button.getAttribute('data-mem-all') === 'show';
            memCards.forEach(function (card) {
                setMemCard(card, open);
            });
        });
    });

    var printButton = document.querySelector('[data-mem-print]');
    if (printButton) {
        printButton.addEventListener('click', function () {
            window.print();
        });
    }
})();
