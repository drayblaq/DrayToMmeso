document.querySelector('.increase').addEventListener('click', function() {
    var quantity = document.getElementById('quantity');
    quantity.value = parseInt(quantity.value) + 1;
});

document.querySelector('.decrease').addEventListener('click', function() {
    var quantity = document.getElementById('quantity');
    if (parseInt(quantity.value) > 1) {
        quantity.value = parseInt(quantity.value) - 1;
    }
});
