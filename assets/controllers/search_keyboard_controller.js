import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'result', 'results'];
    static values = { selectedIndex: { type: Number, default: -1 } };

    connect() {
        document.addEventListener('keydown', this.handleKeydown.bind(this));
    }

    disconnect() {
        document.removeEventListener('keydown', this.handleKeydown.bind(this));
    }

    clear(event) {
        event.preventDefault();
        this.inputTarget.value = '';
        this.inputTarget.dispatchEvent(new Event('input'));
        this.selectedIndexValue = -1;
        if (this.hasResultsTarget) {
            this.resultsTarget.style.display = 'none';
        }
    }

    handleKeydown(event) {
        if (!this.hasInputTarget || event.target !== this.inputTarget) return;

        switch (event.key) {
            case 'ArrowDown':
                event.preventDefault();
                this.#selectNext();
                break;
            case 'ArrowUp':
                event.preventDefault();
                this.#selectPrevious();
                break;
            case 'Enter':
                this.#activateSelected();
                break;
        }
    }

    #selectNext() {
        const nextIndex = Math.min(this.selectedIndexValue + 1, this.resultTargets.length - 1);
        this.selectedIndexValue = nextIndex;
        this.#scrollIntoView();
    }

    #selectPrevious() {
        const prevIndex = Math.max(this.selectedIndexValue - 1, -1);
        this.selectedIndexValue = prevIndex;
        this.#scrollIntoView();
    }

    #activateSelected() {
        if (this.selectedIndexValue >= 0) {
            this.resultTargets[this.selectedIndexValue].click();
        }
    }

    #scrollIntoView() {
        if (this.selectedIndexValue >= 0) {
            this.resultTargets[this.selectedIndexValue].scrollIntoView({
                block: 'nearest'
            });
        }
    }

    selectedIndexValueChanged(index) {
        this.resultTargets.forEach((el, i) => {
            el.classList.toggle('bg-gray-100', i === index);
        });
    }
}