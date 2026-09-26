<style>
    .app-character-counter {
        display: block;
        margin-top: .25rem;
        color: #66736f;
        font-size: .75rem;
        line-height: 1.25rem;
        text-align: right;
    }

    .app-character-counter[data-state="warning"] {
        color: #8a5b06;
        font-weight: 600;
    }

    .app-character-counter[data-state="limit"] {
        color: #a52e27;
        font-weight: 700;
    }
</style>

<script>
    (() => {
        const selector = 'input[maxlength]:not([type="password"]):not([type="hidden"]):not([type="number"]), textarea[maxlength]';
        const threshold = 0.9;

        const updateCounter = (field) => {
            const limit = Number.parseInt(field.getAttribute('maxlength'), 10);
            const wrapper = field.closest('[data-field-wrapper]');

            if (!Number.isFinite(limit) || limit < 1 || !wrapper) {
                return;
            }

            const contentColumn = wrapper.querySelector('.fi-fo-field-content-col') || wrapper;
            let counter = contentColumn.querySelector(':scope > [data-app-character-counter]');

            if (!counter) {
                counter = document.createElement('span');
                counter.className = 'app-character-counter';
                counter.dataset.appCharacterCounter = 'true';
                counter.setAttribute('aria-live', 'off');
                contentColumn.append(counter);
            }

            const length = field.value.length;
            const remaining = Math.max(0, limit - length);
            const atLimit = length >= limit;
            const nearLimit = length >= Math.ceil(limit * threshold);

            counter.dataset.state = atLimit ? 'limit' : nearLimit ? 'warning' : 'normal';
            counter.textContent = atLimit
                ? `Límite alcanzado (${length}/${limit})`
                : `Te quedan ${remaining} caracteres (${length}/${limit})${nearLimit ? ' · cerca del límite' : ''}`;
            counter.setAttribute('aria-live', nearLimit ? 'polite' : 'off');
        };

        const scan = (node) => {
            if (!(node instanceof Element)) {
                return;
            }

            if (node.matches(selector)) {
                updateCounter(node);
            }

            node.querySelectorAll(selector).forEach(updateCounter);
        };

        const initialize = () => {
            scan(document.body);

            document.addEventListener('input', (event) => {
                if (event.target instanceof Element && event.target.matches(selector)) {
                    updateCounter(event.target);
                }
            });

            document.addEventListener('livewire:init', () => {
                window.Livewire.hook('morph.updated', ({ el }) => scan(el));
            });

            document.addEventListener('livewire:initialized', () => scan(document.body));
            document.addEventListener('livewire:navigated', () => scan(document.body));

            new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    mutation.addedNodes.forEach(scan);

                    if (mutation.type === 'attributes' && mutation.target instanceof Element) {
                        scan(mutation.target);
                    }
                });
            }).observe(document.body, {
                attributes: true,
                attributeFilter: ['maxlength'],
                childList: true,
                subtree: true,
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initialize, { once: true });
        } else {
            initialize();
        }
    })();
</script>