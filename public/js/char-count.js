/* Licznik znaków pod polem tekstowym, np. „169 / 600” – dla każdego pola z atrybutem data-counter (id licznika). */
document.querySelectorAll('[data-counter]').forEach((field) => {
    const counter = document.getElementById(field.dataset.counter);

    field.addEventListener('input', () => {
        counter.textContent = field.value.length;
    });
});
