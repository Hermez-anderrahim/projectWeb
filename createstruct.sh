s#!/bin/bash

# Create the directory structure for the frontend
echo "Creating the directory structure..."

# Create assets directory and subdirectories
mkdir -p boutique-en-ligne/assets/css
mkdir -p boutique-en-ligne/assets/js
mkdir -p boutique-en-ligne/assets/images

# Create views directory and subdirectories
mkdir -p boutique-en-ligne/views/partials
mkdir -p boutique-en-ligne/views/auth
mkdir -p boutique-en-ligne/views/products
mkdir -p boutique-en-ligne/views/cart
mkdir -p boutique-en-ligne/views/orders
mkdir -p boutique-en-ligne/views/admin

# Create js directory
mkdir -p boutique-en-ligne/js

# Create empty CSS files
touch boutique-en-ligne/assets/css/style.css
touch boutique-en-ligne/assets/css/responsive.css

# Create empty JavaScript files in assets/js
touch boutique-en-ligne/assets/js/main.js
touch boutique-en-ligne/assets/js/auth.js
touch boutique-en-ligne/assets/js/products.js
touch boutique-en-ligne/assets/js/cart.js
touch boutique-en-ligne/assets/js/orders.js

# Create empty JavaScript files in js
touch boutique-en-ligne/js/api.js
touch boutique-en-ligne/js/utils.js

# Create empty placeholder image
touch boutique-en-ligne/assets/images/placeholder.jpg

# Create empty partials
touch boutique-en-ligne/views/partials/header.php
touch boutique-en-ligne/views/partials/footer.php
touch boutique-en-ligne/views/partials/navbar.php

# Create empty auth views
touch boutique-en-ligne/views/auth/login.php
touch boutique-en-ligne/views/auth/register.php

# Create empty product views
touch boutique-en-ligne/views/products/list.php
touch boutique-en-ligne/views/products/detail.php

# Create empty cart view
touch boutique-en-ligne/views/cart/index.php

# Create empty order views
touch boutique-en-ligne/views/orders/create.php
touch boutique-en-ligne/views/orders/history.php

# Create empty admin views
touch boutique-en-ligne/views/admin/products.php
touch boutique-en-ligne/views/admin/orders.php

echo "Directory structure created successfully with empty files."
echo "You can now populate these files with your own content."