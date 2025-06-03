import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = { sliderId: String };

    prev(event) {
        event.preventDefault();
        const slider = document.getElementById(`slider-${this.sliderIdValue}`);
        slider?.scrollBy({ left: -200, behavior: 'smooth' });
    }

    next(event) {
        event.preventDefault();
        const slider = document.getElementById(`slider-${this.sliderIdValue}`);
        slider?.scrollBy({ left: 200, behavior: 'smooth' });
    }
}