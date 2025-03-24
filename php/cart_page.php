<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Panier</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script>

    <style>
        body {
            background: linear-gradient(to right, #f0f4f8, #e5e9f2);
        }
        .card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        #cart-total {
    position: sticky;
    bottom: 0;
    background: white;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    padding: 1rem;
    z-index: 1000;
}

        .hover-effect:hover {
            transform: scale(1.02);
            transition: transform 0.2s;
        }
        #map {
            height: 300px;
            width: 100%;
            border-radius: 0.5rem;
        }
        .button {
            background-color: #4f46e5;
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #4338ca;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 1rem;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 0.5rem;
        }
        @media (max-width: 640px) {
            .text-4xl {
                font-size: 2rem;
            }
            .text-2xl {
                font-size: 1.5rem;
            }
            .button {
                padding: 0.75rem;
            }
        }

        @media (max-width: 640px) {
    .grid-cols-2 {
        grid-template-columns: 1fr;
    }
}

    </style>
</head>
<body class="font-sans min-h-screen">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center mb-8">Votre Panier</h1>

        <div class="card p-6 mb-8 hover-effect">
            <h2 class="section-title text-2xl">Récapitulatif de la commande</h2>
            <div id="cart-items" class="space-y-6"></div>
            <div id="cart-total" class="text-right text-xl font-bold mt-6"></div>
        </div>

        <form id="payment-form" action="process_payment.php" method="POST" class="card p-8 mb-8 hover-effect">
            <h2 class="section-title text-2xl">Informations de livraison</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nom complet</label>
                    <input type="text" id="name" name="name" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg" required>
                </div>
                <div class="form-group">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg" required>
                </div>
                <div class="form-group col-span-2">
                    <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Adresse</label>
                    <input type="text" id="address" name="address" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg" placeholder="Tapez votre adresse ici" required>
                </div>
                <div class="form-group">
                    <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">Ville</label>
                    <input type="text" id="city" name="city" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg" required>
                </div>
                <div class="form-group">
                    <label for="postal_code" class="block text-sm font-semibold text-gray-700 mb-2">Code postal</label>
                    <input type="text" id="postal_code" name="postal_code" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg" required>
                </div>
            </div>

            <h2 class="text-2xl font-semibold mt-8 mb-6 text-gray-700 border-b pb-4">Sélectionner votre adresse sur la carte</h2>
<div class="w-full h-96">
    <iframe class="w-full h-full" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.199589785392!2d2.526770010035678!3d6.368211293595332!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x103cab5ed5328b6f%3A0xf0ec2149b9ff39a6!2sFRESH%20FROZEN%20FOOD%20SA%20(Annexe%20PK10)!5e0!3m2!1sen!2sbj!4v1725635374015!5m2!1sen!2sbj" allowfullscreen="" loading="lazy"></iframe>
</div>
            <h2 class="section-title text-2xl mt-8">Informations de paiement</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="card_number" class="block text-sm font-semibold text-gray-700 mb-2">Numéro de carte</label>
                    <input type="number" id="card_number" name="card_number" maxlength="16" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg" required>
                </div>
                <div class="form-group">
                    <label for="expiry_date" class="block text-sm font-semibold text-gray-700 mb-2">Date d'expiration</label>
                    <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/AA" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg" required>
                </div>
                <div class="form-group col-span-1">
                    <label for="cvv" class="block text-sm font-semibold text-gray-700 mb-2">CVV</label>
                    <input type="number" id="cvv" name="cvv" maxlength="3" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg" required>
                </div>
            </div>

            
            <h2 class="text-2xl font-semibold mt-8 mb-6 text-gray-700 border-b pb-4">Méthodes de paiement</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <img src="https://th.bing.com/th/id/OIP.3Kx0CdaYJySg6Tn4wSSuWwAAAA?rs=1&pid=ImgDetMain" alt="MoMo" class="w-full h-auto object-contain" />
                <img src="https://th.bing.com/th/id/OIP.R8EQJsEkQ0mUWjsvugjLvQHaHa?rs=1&pid=ImgDetMain" alt="Celtis" class="w-full h-auto object-contain" />
                <img src="https://techenafrique.com/wp-content/uploads/2021/11/MoovAfrica.jpg" alt="Moov" class="w-full h-auto object-contain" />
                <img src="https://th.bing.com/th/id/OIP.LLn5bzE5IVMW5u3VrbmsRwHaHa?pid=ImgDet&w=182&h=182&c=7" alt="Autre option" class="w-full h-auto object-contain" />
            </div>

            <button type="submit" class="button w-full text-lg font-semibold mt-8">
                Payer
            </button>
        </form>

        <h2 class="section-title text-2xl mt-8">FAQs</h2>
        <div class="bg-white p-4 rounded-lg shadow mt-4">
            <p><strong>Q: Quand vais-je recevoir ma commande?</strong></p>
            <p>R: Les délais de livraison varient en fonction de votre localisation. Vous serez informé de la date de livraison estimée lors du paiement.</p>
            <p><strong>Q: Puis-je modifier mon adresse de livraison après avoir passé ma commande?</strong></p>
            <p>R: Oui, vous pouvez modifier votre adresse de livraison dans un délai de 30 minutes après la commande.</p>
        </div>
    </div>

   
    <script>
function updateCartDisplay() {
    const cartContainer = document.getElementById('cart-items');
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Handle empty cart scenario
    if (cart.length === 0) {
        cartContainer.innerHTML = `
            <div class="text-gray-500 text-center py-8">
                <i class="fas fa-shopping-cart text-4xl mb-4 text-gray-400"></i>
                <p>Votre panier est vide</p>
            </div>
        `;
        document.getElementById('cart-total').innerHTML = '<span class="text-2xl">Total: 0 FCFA</span>';
        updateCartCount();
        return;
    }

    // Generate cart items HTML
    let totalAmount = 0;
    cartContainer.innerHTML = cart.map((item, index) => {
        const itemTotal = item.price * item.quantity;
        totalAmount += itemTotal;

        return `
            <div class="cart-item flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition-colors duration-200" data-product-id="${item.productId}" data-index="${index}">
                <div class="cart-details flex-grow">
                    <strong class="text-lg text-gray-800">${item.productName}</strong>
                    <div class="specs text-sm text-gray-600 mt-2">
                        <span class="block">Type de mesure: ${item.sizeValue}</span>
                        <span class="block">Pays: ${item.country}</span>
                        <span class="block">Poids: ${item.weight}</span>
                        <span class="block">Prix unitaire: ${Number(item.price).toLocaleString()} FCFA</span>
                    </div>
                </div>
                <div class="quantity-controls flex items-center space-x-4">
                    <div class="flex items-center space-x-2 bg-gray-100 rounded-lg p-2">
                        <button 
                            type="button" 
                            class="quantity-button w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-sm hover:bg-gray-50" 
                            onclick="decrementQuantity(${index})"
                        >
                            -
                        </button>
                        <input 
                            type="number" 
                            min="1" 
                            value="${item.quantity}" 
                            class="quantity-input w-12 text-center bg-transparent focus:outline-none"
                            onchange="updateQuantityManually(${index}, this.value)"
                        />
                        <button 
                            type="button" 
                            class="quantity-button w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-sm hover:bg-gray-50" 
                            onclick="incrementQuantity(${index})"
                        >
                            +
                        </button>
                    </div>
                    <span class="item-subtotal font-semibold text-gray-800">
                        ${Number(itemTotal).toLocaleString()} FCFA
                    </span>
                    <button 
                        onclick="removeItem(${index})" 
                        class="text-red-500 hover:text-red-700 transition-colors duration-200"
                    >
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }).join('');

    // Update total amount
    document.getElementById('cart-total').innerHTML = `
        <span class="text-2xl">Total: ${Number(totalAmount).toLocaleString()} FCFA</span>
    `;

    // Update cart count in UI
    updateCartCount();
}

function updateQuantityManually(index, newQuantity) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Validate input
    newQuantity = parseInt(newQuantity);
    
    // Ensure quantity is at least 1
    if (isNaN(newQuantity) || newQuantity < 1) {
        newQuantity = 1;
    }
    
    // Update quantity
    cart[index].quantity = newQuantity;
    
    // Save updated cart
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Refresh display
    updateCartDisplay();
    syncCartWithServer();
}

function decrementQuantity(index) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (cart[index].quantity > 1) {
        cart[index].quantity -= 1;
    } else {
        cart.splice(index, 1); // Remove item if quantity becomes 0
    }
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartDisplay();
    syncCartWithServer();
}

function incrementQuantity(index) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart[index].quantity += 1;
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartDisplay();
    syncCartWithServer();
}

function removeItem(index) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartDisplay();
    syncCartWithServer();
}

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = cart.reduce((total, item) => total + item.quantity, 0);
        cartCountElement.classList.toggle('hidden', cart.length === 0);
    }
}

// Initialize cart display when page loads
document.addEventListener('DOMContentLoaded', updateCartDisplay);

function sendCartDataToBackend() {
        const cartData = JSON.parse(localStorage.getItem('cart')) || [];

        // Send the data via AJAX to reference.php
        fetch('reference.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cartData })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response from server:', data);
        })
        .catch(error => console.error('Error sending data:', error));
    }

    // Call the function to send data when the page loads
    document.addEventListener('DOMContentLoaded', sendCartDataToBackend);

// Call the loadCart function when the page loads
document.addEventListener('DOMContentLoaded', loadCart);
       let map, marker, geocoder;

function initMap() {
    geocoder = new google.maps.Geocoder();
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 0, lng: 0 },
        zoom: 2,
    });

    marker = new google.maps.Marker({
        position: { lat: 0, lng: 0 },
        map: map,
        draggable: true,
    });

    google.maps.event.addListener(marker, 'dragend', function(event) {
        const lat = event.latLng.lat();
        const lng = event.latLng.lng();
        geocodeLatLng(lat, lng);
    });

    // Add a click event listener to the map
    map.addListener('click', function(event) {
        marker.setPosition(event.latLng);
        const lat = event.latLng.lat();
        const lng = event.latLng.lng();
        geocodeLatLng(lat, lng);
    });
}

function initializeAddressAutocomplete() {
    const addressInput = document.getElementById('address');
    const autocomplete = new google.maps.places.Autocomplete(addressInput, {
        types: ['address'],
    });
}
document.addEventListener('DOMContentLoaded', initializeAddressAutocomplete);


function geocodeLatLng(lat, lng) {
    const latlng = { lat: lat, lng: lng };
    geocoder.geocode({ location: latlng }, function(results, status) {
        if (status === "OK") {
            if (results[0]) {
                document.getElementById('address').value = results[0].formatted_address;
            } else {
                document.getElementById('address').value = "Aucune adresse trouvée";
            }
        } else {
            document.getElementById('address').value = "Géocode échoué: " + status;
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    updateCartDisplay();
    initMap(); // Initialize the map when the document is ready
});


document.querySelectorAll('.faq-question').forEach(item => {
    item.addEventListener('click', () => {
        const answer = item.nextElementSibling;
        answer.classList.toggle('hidden');
    });
});


        document.getElementById('payment-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            if (cart.length === 0) {
                alert('Votre panier est vide');
                return;
            }

            this.submit();
        });

        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 16) value = value.slice(0, 16);
            this.value = value;
        });

        document.getElementById('expiry_date').addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 4) value = value.slice(0, 4);
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2);
            }
            this.value = value;
        });

        document.getElementById('cvv').addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 3) value = value.slice(0, 3);
            this.value = value;
        });
    </script>
    
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script>
</body>
</html>