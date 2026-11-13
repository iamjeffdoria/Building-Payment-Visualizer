var fill = document.getElementById('fill-balance');
if (fill) {
    fill.addEventListener('click', function () {
        document.getElementById('pay-amount').value = fill.dataset.balance;
    });
}