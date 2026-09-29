(function () {
    'use strict';

    var items = document.querySelectorAll('.sw-service .sw-accordion__item');

    if (!items.length) {
        return;
    }

    items.forEach(function (item) {
        var trigger = item.querySelector('.sw-accordion__trigger');
        var panel = item.querySelector('.sw-accordion__panel');

        if (!trigger || !panel) {
            return;
        }

        trigger.addEventListener('click', function () {
            var isOpen = item.classList.contains('is-open');

            items.forEach(function (otherItem) {
                var otherTrigger = otherItem.querySelector('.sw-accordion__trigger');
                var otherPanel = otherItem.querySelector('.sw-accordion__panel');

                otherItem.classList.remove('is-open');

                if (otherTrigger) {
                    otherTrigger.setAttribute('aria-expanded', 'false');
                }

                if (otherPanel) {
                    otherPanel.hidden = true;
                }
            });

            if (!isOpen) {
                item.classList.add('is-open');
                trigger.setAttribute('aria-expanded', 'true');
                panel.hidden = false;
            }
        });
    });
})();
