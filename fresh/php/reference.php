<?php
// Include the connection file
include('connection.php');

// Start the session for cart management
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Fetch products from the database
$sql = "SELECT * FROM product";
$stmt = $bd->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll();

// Fetch measurements (cm)
$sql_cm = "SELECT * FROM measurement_cm ORDER BY cm_value";
$stmt_cm = $bd->prepare($sql_cm);
$stmt_cm->execute();
$measurements_cm = $stmt_cm->fetchAll();

// Fetch measurements (g)
$sql_g = "SELECT * FROM measurement_g ORDER BY g_value";
$stmt_g = $bd->prepare($sql_g);
$stmt_g->execute();
$measurements_g = $stmt_g->fetchAll();

// Fetch countries
$sql_countries = "SELECT * FROM countries";
$stmt_countries = $bd->prepare($sql_countries);
$stmt_countries->execute();
$countries = $stmt_countries->fetchAll();

// Fetch measurements for kg
$sql_kg = "SELECT * FROM measurement_kg ORDER BY kg_value";
$stmt_kg = $bd->prepare($sql_kg);
$stmt_kg->execute();
$measurements_kg = $stmt_kg->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vente de Poissons en Ligne</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="../css/styy.css">
</head>
<body>
<header>
    <div class="header-container">
        <div class="header-content">
            <div class="brand-section">
                <img src="../img/logo-fresh.png" alt="Company Logo" class="logo">
                <h1>Fresh Frozen Food</h1>
            </div>

            <nav>
                <ul class="nav-list">
                    <li><a href="../html/index.html" class="nav-link">Home</a></li>
                    <li><a href="../html/about.html" class="nav-link">About Us</a></li>
                    <li><a href="../html/contact.html" class="nav-link">Contact Us</a></li>
                    <li>
                        <a href="cart_page.php" class="cart-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span id="cart-count">0</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <button class="mobile-menu-btn">☰</button>
        </div>
    </div>
    <section id="product1" class="section-p1">
        <h2>Catalogue de Produit</h2>
        <p>Vente de Poissons en Ligne</p>

        <div id="search-bar">
            <input type="text" id="search-input" placeholder="Rechercher un poisson..." onkeyup="filterProducts()">
            <button onclick="filterProducts()">Rechercher</button>
        </div>
        <div class="pro-container">
            <?php foreach ($products as $product): ?>
                <div class="pro" data-product-id="<?php echo htmlspecialchars($product['product_id']); ?>">
                    <div class="product-image-container">
                        <img src="<?php echo htmlspecialchars($product['product_picture']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        <div class="quick-actions">
                            <button class="wishlist-btn">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="des">
                        <h4 id="product-name-<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['product_name']); ?></h4>
                        <div class="star">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <div class="all-options">
                            <!-- Size Type Selection -->
                            <div class="selection-step">
                                <label for="size-type-<?php echo $product['product_id']; ?>">Type de mesure:</label>
                                <select id="size-type-<?php echo $product['product_id']; ?>" onchange="updateAllOptions(<?php echo $product['product_id']; ?>)">
                                    <option value="">Sélectionner</option>
                                    <option value="cm">Centimètres (cm)</option>
                                    <option value="g">Grammes (g)</option>
                                </select>
                            </div>

                            <!-- Size Values -->
                            <div class="selection-step hidden" id="value-section-<?php echo $product['product_id']; ?>">
                                <label for="size-value-<?php echo $product['product_id']; ?>">Valeur:</label>
                                <select id="size-value-<?php echo $product['product_id']; ?>" onchange="updateAllOptions(<?php echo $product['product_id']; ?>)">
                                    <option value="">Sélectionner</option>
                                </select>
                            </div>

                            <!-- Country Selection -->
                            <div class="selection-step">
                                <label for="country-<?php echo $product['product_id']; ?>">Pays d'origine:</label>
                                <select id="country-<?php echo $product['product_id']; ?>" onchange="updateAllOptions(<?php echo $product['product_id']; ?>)">
                                    <option value="">Sélectionner</option>
                                    <?php foreach ($countries as $country): ?>
                                        <option value="<?php echo htmlspecialchars($country['country_id']); ?>">
                                            <?php echo htmlspecialchars($country['country_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Kg Selection -->
                            <div class="selection-step">
                                <label for="kg-value-<?php echo $product['product_id']; ?>">Poids (kg):</label>
                                <select id="kg-value-<?php echo $product['product_id']; ?>" onchange="updateAllOptions(<?php echo $product['product_id']; ?>)">
                                    <option value="">Sélectionner</option>
                                    <?php foreach ($measurements_kg as $kg): ?>
                                        <option value="<?php echo htmlspecialchars($kg['kg_id']); ?>">
                                            <?php echo htmlspecialchars($kg['kg_value']); ?> kg
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="price-cart-container">
                        <p id="price-<?php echo $product['product_id']; ?>" class="price">Prix: -- FCFA</p>
                        
                        <button class="add-to-cart-btn" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                    </div>

                            
                        </div>
                    </div>
                    <div id="notification-<?php echo $product['product_id']; ?>" class="notification hidden">
                        Produit ajouté au panier!
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <footer class="bg-900 py-16">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                <div>
                    <h3 class="text-xl font-bold mb-4 text-blue-400">Fresh Frozen Food</h3>
                    <p class="text-gray-400">Premium quality frozen seafood for your culinary adventures.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Home</a></li>
                        <li><a href="pro.html" class="hover:text-blue-400 transition-colors">Products</a></li>
                        <li><a href="about.html" class="hover:text-blue-400 transition-colors">About Us</a></li>
                        <li><a href="contact.html" class="hover:text-blue-400 transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Newsletter</h4>
                    <form class="space-y-4">
                        <div class="relative">
                            <input type="email" placeholder="Your email address" class="w-full px-4 py-2 bg-gray-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                            Subscribe
                        </button>
                    </form>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Find Us</h4>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.199589785392!2d2.526770010035678!3d6.368211293595332!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x103cabdcbb9e20c1%3A0x7227e6cf4215e9!2sGroupe%20NSIA!5e0!3m2!1sfr!2scm!4v1666482187752!5m2!1sfr!2scm" class="w-full h-48 rounded-lg" loading="lazy"></iframe>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>© 2024 Fresh Frozen Food. All rights reserved.</p>
            </div>
        </div>
    </footer>
        
        
        <script>
document.addEventListener('DOMContentLoaded', function() {
    const products = document.getElementsByClassName('pro');
    Array.from(products).forEach(product => {
        const productId = product.getAttribute('data-product-id');
        const valueSection = document.getElementById(`value-section-${productId}`);
        valueSection.classList.add('hidden');

        // Add change listeners to all select elements
        const sizeTypeSelect = document.getElementById(`size-type-${productId}`);
        const sizeValueSelect = document.getElementById(`size-value-${productId}`);
        const countrySelect = document.getElementById(`country-${productId}`);
        const kgSelect = document.getElementById(`kg-value-${productId}`);

        sizeTypeSelect.addEventListener('change', () => handleSizeTypeChange(productId));
        sizeValueSelect.addEventListener('change', () => checkAndUpdatePrice(productId));
        countrySelect.addEventListener('change', () => checkAndUpdatePrice(productId));
        kgSelect.addEventListener('change', () => checkAndUpdatePrice(productId));
    });
});

function addToCart(productId) {
    const productName = document.getElementById(`product-name-${productId}`).textContent;
    const sizeTypeSelect = document.getElementById(`size-type-${productId}`);
    const sizeType = sizeTypeSelect.options[sizeTypeSelect.selectedIndex].text; // cm or g
    const sizeValueSelect = document.getElementById(`size-value-${productId}`);
    const sizeValue = sizeValueSelect.options[sizeValueSelect.selectedIndex].text; // Actual measurement value
    const countrySelect = document.getElementById(`country-${productId}`);
    const country = countrySelect.options[countrySelect.selectedIndex].text;
    const kgSelect = document.getElementById(`kg-value-${productId}`);
    const weight = kgSelect.options[kgSelect.selectedIndex].text; // kg
    const priceElement = document.getElementById(`price-${productId}`);
    const priceText = priceElement ? priceElement.textContent : '0 FCFA'; // Fallback to '0 FCFA'
    const price = parseFloat(priceText.replace('Prix: ', '').replace(' FCFA', '').replace(/\s/g, '')); // Remove spaces if present
   
    const cartItem = {
        productId,
        productName,
        sizeType, // cm or g
        sizeValue, // Added to capture the measurement value
        country,
        weight,
        price,
        quantity: 1
    };

    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const existingItemIndex = cart.findIndex(item => 
        item.productId === cartItem.productId && 
        item.sizeType === cartItem.sizeType && 
        item.sizeValue === cartItem.sizeValue && // Added to match specific size value
        item.country === cartItem.country && 
        item.weight === cartItem.weight
    );

    if (existingItemIndex !== -1) {
        cart[existingItemIndex].quantity += 1;
    } else {
        cart.push(cartItem);
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    showNotification(cartItem.productId);
}

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const cartCount = document.getElementById('cart-count');
    cartCount.textContent = totalItems;
}

function showNotification(productId) {
    const notification = document.getElementById(`notification-${productId}`);
    notification.classList.remove('hidden');
    
    // Animate cart icon
    const cartIcon = document.querySelector('.cart-link i');
    cartIcon.classList.add('cart-animation');

    // Remove animation and notification after delay
    setTimeout(() => {
        notification.classList.add('hidden');
        cartIcon.classList.remove('cart-animation');
    }, 3000);
}

// Initialize cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
});

// Function to get formatted cart data for display
function getFormattedCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    return cart.map(item => ({
        ...item,
        totalPrice: (item.price * item.quantity).toLocaleString(),
        formattedPrice: Number(item.price).toLocaleString()
    }));
}

// Function to clear cart
function clearCart() {
    localStorage.removeItem('cart');
    updateCartCount();
}

function handleSizeTypeChange(productId) {
    const sizeTypeSelect = document.getElementById(`size-type-${productId}`);
    const sizeValueSelect = document.getElementById(`size-value-${productId}`);
    const valueSection = document.getElementById(`value-section-${productId}`);
    
    // Clear existing options
    sizeValueSelect.innerHTML = '<option value="">Sélectionner</option>';
    
    if (sizeTypeSelect.value) {
        valueSection.classList.remove('hidden');
        
        const table = sizeTypeSelect.value === 'cm' ? 'measurement_cm' : 'measurement_g';
        const valueField = sizeTypeSelect.value === 'cm' ? 'cm_value' : 'g_value';
        const idField = sizeTypeSelect.value === 'cm' ? 'cm_id' : 'g_id';

        fetch(`get_measurements.php?product_id=${productId}&table=${table}&value_field=${valueField}&id_field=${idField}`)
            .then(response => response.json())
            .then(data => {
                if (Array.isArray(data)) {
                    // Clear any existing options first
                    sizeValueSelect.innerHTML = '<option value="">Sélectionner</option>';
                    
                    // Create a Set to track unique values
                    const uniqueValues = new Set();
                    
                    data.forEach(measurement => {
                        let displayValue;
                        if (sizeTypeSelect.value === 'cm') {
                            // Keep the CM display logic the same
                            displayValue = measurement.size_name 
                                ? `${measurement.value} (${measurement.size_name})`
                                : `${measurement.value}`;
                        } else {
                            // Modified G display logic to show range
                            displayValue = measurement.g_range_end 
                                ? `${measurement.value}-${measurement.g_range_end}grs` 
                                : `${measurement.value}grs`;
                        }
                        
                        // Skip if we've already added this value
                        if (uniqueValues.has(displayValue)) {
                            return;
                        }
                        uniqueValues.add(displayValue);
                        
                        const option = new Option(displayValue, measurement.id);
                        sizeValueSelect.add(option);
                    });
                }
                checkAndUpdatePrice(productId);
            })
            .catch(error => {
                console.error('Erreur:', error);
            });
    } else {
        valueSection.classList.add('hidden');
        checkAndUpdatePrice(productId);
    }
}

function checkAndUpdatePrice(productId) {
    const sizeType = document.getElementById(`size-type-${productId}`).value;
    const sizeValue = document.getElementById(`size-value-${productId}`).value;
    const countryValue = document.getElementById(`country-${productId}`).value;
    const kgValue = document.getElementById(`kg-value-${productId}`).value;
    const priceElement = document.getElementById(`price-${productId}`);

    // Check if all required values are selected
    if (!sizeType || !sizeValue || !countryValue || !kgValue) {
        priceElement.textContent = 'Prix: -- FCFA';
        return;
    }

    // Show loading state
    priceElement.textContent = 'Chargement du prix...';

    const data = {
        product_id: productId,
        cm_size: sizeType === 'cm' ? sizeValue : null,
        g_size: sizeType === 'g' ? sizeValue : null,
        country: countryValue,
        weight_value: kgValue
    };

    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            priceElement.textContent = `Prix: ${data.price}`;
        } else {
            priceElement.textContent = data.error || 'Prix non disponible';
            console.error('Price fetch error:', data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        priceElement.textContent = 'Erreur de communication';
    });
}
    </script>
</body>
</html>