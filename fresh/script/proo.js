

function filterProducts() {
    let input = document.getElementById('search-input').value.toLowerCase();
    let productCards = document.getElementsByClassName('pro');

    for (let i = 0; i < productCards.length; i++) {
        let productName = productCards[i].getElementsByClassName('des')[0].getElementsByTagName('span')[0].innerText.toLowerCase();
        if (productName.includes(input)) {
            productCards[i].style.display = "block"; // Show the product
        } else {
            productCards[i].style.display = "none"; // Hide the product
        }
    }
}

const fishPrices = {
    blackbelly: {
        small: "15 000cfa",
        medium: "20 000cfa",
        large: "23 000cfa"
    },

    Bonito: {
        small: "2 000cfa",
        medium: "3 000cfa",
        large: "5 000cfa"
    },

    Tilapia: {
        small: "15 000cfa",
        medium: "19 000cfa",
        large: "23 000cfa"
    },

    Sardine: {
        small: "5 000cfa",
        medium: "9 000cfa",
        large: "14 600cfa"
    },

    barracuda: {
        small: "5 000cfa",
        medium: "9 000cfa",
        large: "14 600cfa"
    },


    // Add more fish prices as necessary
};


function updatePrice(fish) {
    const sizeDropdown = document.getElementById(`size-${fish}`);
    const selectedSize = sizeDropdown.value;
    const priceDisplay = document.getElementById(`price-${fish}`);
    priceDisplay.innerHTML = fishPrices[fish][selectedSize];

     // Check if the fish and selected size exist in the fishPrices object
     if (fishPrices[fish] && fishPrices[fish][selectedSize]) {
        priceDisplay.innerHTML = fishPrices[fish][selectedSize];
    } else {
        console.log("Fish or size not found in prices");
    }
}