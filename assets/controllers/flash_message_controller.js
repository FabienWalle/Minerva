import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [];

    close(event) {
        event.preventDefault();
        this.element.remove();
    }
}