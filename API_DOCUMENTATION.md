# API Documentation - Bharata Herbal PABP

## Overview

This API provides a shared backend interface for mobile applications (Flutter/React Native) and external clients to access all customer features of the Bharata Herbal e-commerce platform.

**Base URL**: `http://localhost/api`

**API Version**: v1

**Authentication**: Laravel Sanctum (Bearer Token)

---

## Response Format

All API responses follow a consistent JSON format:

### Success Response (HTTP 200, 201)
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    // Response data structure varies by endpoint
  }
}
```

### Error Response (HTTP 400, 401, 403, 404, 422, 500)
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    // Optional: Validation errors
    "field_name": ["Error message"]
  }
}
```

---

## Authentication Endpoints

### 1. Register User
Create a new customer account.

**Endpoint:** `POST /api/register`

**Auth:** None (Public)

**Request Body:**
```json
{
  "name": "string (required, max:255)",
  "email": "string (required, email, unique)",
  "phone": "string (required, regex: /^[0-9]{10,15}$/)",
  "password": "string (required, min:6, with uppercase, number, symbol)",
  "password_confirmation": "string (required, must match password)"
}
```

**Response 201:**
```json
{
  "success": true,
  "message": "Registrasi berhasil",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "08123456789",
      "role": "customer",
      "created_at": "2026-04-23T10:30:00Z"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
  }
}
```

**Response Errors:**
- `400`: Email already registered or phone invalid
- `422`: Validation failed

---

### 2. Login User
Authenticate and get access token.

**Endpoint:** `POST /api/login`

**Auth:** None (Public)

**Request Body:**
```json
{
  "email": "string (required, email)",
  "password": "string (required)"
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "08123456789",
      "role": "customer",
      "created_at": "2026-04-23T10:30:00Z"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
  }
}
```

**Response Errors:**
- `401`: Invalid email/password
- `403`: Admin accounts cannot login via mobile

---

### 3. Logout
Revoke current access token.

**Endpoint:** `POST /api/logout`

**Auth:** Bearer Token (Required)

**Request Body:** None

**Response 200:**
```json
{
  "success": true,
  "message": "Logout berhasil",
  "data": null
}
```

---

### 4. Get Current User Profile
Retrieve authenticated user information.

**Endpoint:** `GET /api/me`

**Auth:** Bearer Token (Required)

**Request Body:** None

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "08123456789",
    "role": "customer",
    "created_at": "2026-04-23T10:30:00Z"
  }
}
```

---

## Product Endpoints

### 5. List Products
Retrieve all available products with filtering and sorting.

**Endpoint:** `GET /api/products`

**Auth:** None (Public)

**Query Parameters:**
- `page` (optional, integer, default: 1)
- `per_page` (optional, integer, default: 12, max: 100)
- `search` (optional, string) - Search in name and description
- `category` (optional, string) - Filter by category slug
- `sort` (optional, string) - Values: `price_asc`, `price_desc`, `rating`, `latest` (default)

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Jamu Herbal Plus",
        "slug": "jamu-herbal-plus",
        "description": "Jamu tradisional berkualitas...",
        "usage": "Diminum 1x sehari",
        "benefits": "Meningkatkan stamina dan imunitas",
        "composition": "Kunyit, jahe, temulawak...",
        "price": 50000,
        "discount_price": 40000,
        "effective_price": 40000,
        "has_discount": true,
        "discount_percent": 20,
        "stock": 100,
        "status": "active",
        "image_url": "http://localhost/storage/products/image.jpg",
        "is_featured": true,
        "is_bestseller": true,
        "rating": 4.5,
        "rating_count": 125,
        "sales_count": 500,
        "categories": [
          {
            "id": 1,
            "name": "Herbal",
            "slug": "herbal",
            "description": "Produk herbal alami",
            "icon": "herbal.png",
            "products_count": 50
          }
        ],
        "reviews": [],
        "created_at": "2026-01-01T00:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 10,
      "per_page": 12,
      "total": 120,
      "from": 1,
      "to": 12
    }
  }
}
```

**Response Errors:**
- `422`: Invalid sort value or page number

---

### 6. Get Product Details
Retrieve detailed information for a specific product.

**Endpoint:** `GET /api/products/{slug}`

**Auth:** None (Public)

**URL Parameters:**
- `slug` (string, required) - Product slug

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 1,
    "name": "Jamu Herbal Plus",
    "slug": "jamu-herbal-plus",
    "description": "Jamu tradisional berkualitas...",
    "usage": "Diminum 1x sehari",
    "benefits": "Meningkatkan stamina dan imunitas",
    "composition": "Kunyit, jahe, temulawak...",
    "price": 50000,
    "discount_price": 40000,
    "effective_price": 40000,
    "has_discount": true,
    "discount_percent": 20,
    "stock": 100,
    "status": "active",
    "image_url": "http://localhost/storage/products/image.jpg",
    "is_featured": true,
    "is_bestseller": true,
    "rating": 4.5,
    "rating_count": 125,
    "sales_count": 500,
    "categories": [...],
    "reviews": [
      {
        "id": 1,
        "product_id": 1,
        "product_name": "Jamu Herbal Plus",
        "user_id": 5,
        "user_name": "Jane Doe",
        "rating": 5,
        "comment": "Sangat membantu! Saya merasa lebih sehat.",
        "image_url": null,
        "reviewer_name": "Jane Doe",
        "reviewer_title": "Customer Verified",
        "is_featured": true,
        "created_at": "2026-03-15T10:00:00Z"
      }
    ],
    "created_at": "2026-01-01T00:00:00Z"
  }
}
```

**Response Errors:**
- `404`: Product not found

---

### 7. List Categories
Retrieve all product categories.

**Endpoint:** `GET /api/categories`

**Auth:** None (Public)

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": [
    {
      "id": 1,
      "name": "Herbal",
      "slug": "herbal",
      "description": "Produk herbal alami",
      "icon": "herbal.png",
      "products_count": 50
    },
    {
      "id": 2,
      "name": "Vitamin",
      "slug": "vitamin",
      "description": "Suplemen vitamin",
      "icon": "vitamin.png",
      "products_count": 30
    }
  ]
}
```

---

## Cart Endpoints

### 8. Get Cart
Retrieve current user's cart.

**Endpoint:** `GET /api/cart`

**Auth:** Bearer Token (Required)

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 1,
    "items": [
      {
        "id": 1,
        "product_id": 1,
        "product_name": "Jamu Herbal Plus",
        "product_image": "http://localhost/storage/products/image.jpg",
        "quantity": 2,
        "unit_price": 40000,
        "subtotal": 80000,
        "is_selected": true,
        "created_at": "2026-04-20T10:00:00Z"
      }
    ],
    "total": 80000,
    "selected_count": 1,
    "total_items": 2,
    "minimum_order_amount": 50000,
    "is_minimum_met": true,
    "created_at": "2026-04-20T10:00:00Z"
  }
}
```

---

### 9. Add to Cart
Add a product to cart.

**Endpoint:** `POST /api/cart`

**Auth:** Bearer Token (Required)

**Request Body:**
```json
{
  "product_id": "integer (required, exists in products)",
  "quantity": "integer (required, min:1, max:99)"
}
```

**Response 201:**
```json
{
  "success": true,
  "message": "Produk \"Jamu Herbal Plus\" berhasil ditambahkan ke keranjang.",
  "data": {
    "id": 1,
    "items": [...],
    "total": 80000,
    "selected_count": 1,
    "total_items": 2,
    "minimum_order_amount": 50000,
    "is_minimum_met": true,
    "created_at": "2026-04-20T10:00:00Z"
  }
}
```

**Response Errors:**
- `400`: Product unavailable, out of stock, or insufficient quantity
- `404`: Product not found
- `422`: Validation failed

---

### 10. Update Cart Item Quantity
Update quantity of a cart item.

**Endpoint:** `PATCH /api/cart/{cartItem}`

**Auth:** Bearer Token (Required)

**URL Parameters:**
- `cartItem` (integer, required) - Cart item ID

**Request Body:**
```json
{
  "quantity": "integer (required, min:1, max:99)"
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 1,
    "items": [...],
    "total": 120000,
    "selected_count": 1,
    "total_items": 3,
    "minimum_order_amount": 50000,
    "is_minimum_met": true,
    "created_at": "2026-04-20T10:00:00Z"
  }
}
```

**Response Errors:**
- `403`: Unauthorized (item belongs to different user)
- `400`: Insufficient stock
- `404`: Cart item not found
- `422`: Invalid quantity

---

### 11. Remove from Cart
Delete an item from cart.

**Endpoint:** `DELETE /api/cart/{cartItem}`

**Auth:** Bearer Token (Required)

**URL Parameters:**
- `cartItem` (integer, required) - Cart item ID

**Response 200:**
```json
{
  "success": true,
  "message": "Produk \"Jamu Herbal Plus\" berhasil dihapus dari keranjang.",
  "data": {
    "id": 1,
    "items": [],
    "total": 0,
    "selected_count": 0,
    "total_items": 0,
    "minimum_order_amount": 50000,
    "is_minimum_met": false,
    "created_at": "2026-04-20T10:00:00Z"
  }
}
```

**Response Errors:**
- `403`: Unauthorized
- `404`: Cart item not found

---

### 12. Toggle Item Selection
Select/deselect a cart item for checkout.

**Endpoint:** `PATCH /api/cart/{cartItem}/toggle-select`

**Auth:** Bearer Token (Required)

**URL Parameters:**
- `cartItem` (integer, required) - Cart item ID

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 1,
    "items": [...],
    "total": 80000,
    "selected_count": 1,
    "total_items": 2,
    "minimum_order_amount": 50000,
    "is_minimum_met": true,
    "created_at": "2026-04-20T10:00:00Z"
  }
}
```

---

### 13. Toggle Select All Items
Select/deselect all cart items.

**Endpoint:** `POST /api/cart/toggle-select-all`

**Auth:** Bearer Token (Required)

**Request Body:**
```json
{
  "select_all": "boolean (required)"
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 1,
    "items": [...],
    "total": 160000,
    "selected_count": 2,
    "total_items": 4,
    "minimum_order_amount": 50000,
    "is_minimum_met": true,
    "created_at": "2026-04-20T10:00:00Z"
  }
}
```

---

### 14. Clear Cart
Delete all items from cart.

**Endpoint:** `DELETE /api/cart`

**Auth:** Bearer Token (Required)

**Response 200:**
```json
{
  "success": true,
  "message": "Keranjang berhasil dikosongkan.",
  "data": null
}
```

---

## Checkout Endpoints

### 15. Get Checkout Summary
Retrieve checkout data including addresses and payment methods.

**Endpoint:** `GET /api/checkout`

**Auth:** Bearer Token (Required)

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "cart": {
      "id": 1,
      "items": [...],
      "total": 80000,
      "selected_count": 1,
      "total_items": 2,
      "minimum_order_amount": 50000,
      "is_minimum_met": true,
      "created_at": "2026-04-20T10:00:00Z"
    },
    "selected_items": [
      {
        "product_id": 1,
        "product_name": "Jamu Herbal Plus",
        "quantity": 2,
        "unit_price": 40000,
        "subtotal": 80000
      }
    ],
    "addresses": [
      {
        "id": 1,
        "label": "Rumah",
        "recipient_name": "John Doe",
        "phone": "08123456789",
        "street": "Jl. Merdeka 123",
        "city": "Jakarta",
        "province": "DKI Jakarta",
        "postal_code": "12345",
        "full_address": "Jl. Merdeka 123, Jakarta, DKI Jakarta 12345",
        "is_default": true,
        "created_at": "2026-04-15T10:00:00Z"
      }
    ],
    "default_address": {...},
    "payment_methods": ["transfer", "cash_on_delivery"],
    "bank_accounts": [
      {
        "id": 1,
        "bank_name": "Bank Central Asia",
        "account_number": "123456789",
        "account_holder": "PT Bharata Herbal",
        "notes": "Transfer via ATM atau mobile banking"
      }
    ],
    "subtotal": 80000,
    "shipping_cost": 20000,
    "total": 100000,
    "minimum_order_amount": 50000,
    "is_minimum_met": true
  }
}
```

**Response Errors:**
- `400`: Cart empty or no items selected
- `404`: No addresses found

---

### 16. Place Order
Create new order from selected cart items.

**Endpoint:** `POST /api/checkout`

**Auth:** Bearer Token (Required)

**Request Body:**
```json
{
  "address_id": "integer (required, exists in user's addresses)",
  "payment_method": "string (required, in: transfer, cash_on_delivery)",
  "notes": "string (optional, max:255)"
}
```

**Response 201:**
```json
{
  "success": true,
  "message": "Pesanan berhasil dibuat.",
  "data": {
    "order_id": 123,
    "order_number": "ORD-000123",
    "total_price": 100000,
    "status": "unpaid"
  }
}
```

**Response Errors:**
- `400`: Cart empty, minimum order not met, or address not found
- `404`: Address not found
- `422`: Validation failed

---

## Order Endpoints

### 17. List Orders
Retrieve user's orders with optional status filter.

**Endpoint:** `GET /api/orders`

**Auth:** Bearer Token (Required)

**Query Parameters:**
- `status` (optional, string) - Filter by status: `pending`, `unpaid`, `processing`, `shipped`, `completed`, `cancelled`
- `page` (optional, integer, default: 1)
- `per_page` (optional, integer, default: 10)

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "data": [
      {
        "id": 123,
        "order_number": "ORD-000123",
        "status": "completed",
        "subtotal": 80000,
        "shipping_cost": 20000,
        "total_price": 100000,
        "notes": "Harus tiba sebelum hari Jumat",
        "tracking_number": "TRACK123456",
        "courier_name": "JNE",
        "cancel_reason": null,
        "payment_deadline": null,
        "estimated_delivery_at": "2026-04-25T23:59:59Z",
        "items": [...],
        "address": {...},
        "payment": null,
        "created_at": "2026-04-20T10:00:00Z",
        "updated_at": "2026-04-24T10:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 5,
      "per_page": 10,
      "total": 47,
      "from": 1,
      "to": 10
    }
  }
}
```

---

### 18. Get Order Details
Retrieve detailed information for a specific order.

**Endpoint:** `GET /api/orders/{order}`

**Auth:** Bearer Token (Required)

**URL Parameters:**
- `order` (integer, required) - Order ID

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 123,
    "order_number": "ORD-000123",
    "status": "completed",
    "subtotal": 80000,
    "shipping_cost": 20000,
    "total_price": 100000,
    "notes": "Harus tiba sebelum hari Jumat",
    "tracking_number": "TRACK123456",
    "courier_name": "JNE",
    "cancel_reason": null,
    "payment_deadline": null,
    "estimated_delivery_at": "2026-04-25T23:59:59Z",
    "items": [
      {
        "id": 1,
        "order_id": 123,
        "product_id": 1,
        "product_name": "Jamu Herbal Plus",
        "product_image": "http://localhost/storage/products/image.jpg",
        "quantity": 2,
        "unit_price": 40000,
        "subtotal": 80000,
        "created_at": "2026-04-20T10:00:00Z"
      }
    ],
    "address": {
      "id": 1,
      "label": "Rumah",
      "recipient_name": "John Doe",
      "phone": "08123456789",
      "street": "Jl. Merdeka 123",
      "city": "Jakarta",
      "province": "DKI Jakarta",
      "postal_code": "12345",
      "full_address": "Jl. Merdeka 123, Jakarta, DKI Jakarta 12345",
      "is_default": true,
      "created_at": "2026-04-15T10:00:00Z"
    },
    "payment": null,
    "created_at": "2026-04-20T10:00:00Z",
    "updated_at": "2026-04-24T10:00:00Z"
  }
}
```

**Response Errors:**
- `403`: Order belongs to different user
- `404`: Order not found

---

### 19. Cancel Order
Cancel a pending or unpaid order.

**Endpoint:** `POST /api/orders/{order}/cancel`

**Auth:** Bearer Token (Required)

**URL Parameters:**
- `order` (integer, required) - Order ID

**Request Body:**
```json
{
  "cancel_reason": "string (optional, max:255)"
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Pesanan berhasil dibatalkan.",
  "data": {
    "id": 123,
    "order_number": "ORD-000123",
    "status": "cancelled",
    "subtotal": 80000,
    "shipping_cost": 20000,
    "total_price": 100000,
    "notes": null,
    "tracking_number": null,
    "courier_name": null,
    "cancel_reason": "Berubah pikiran",
    ...
  }
}
```

**Response Errors:**
- `400`: Order cannot be cancelled (shipped, completed, or already cancelled)
- `403`: Order belongs to different user
- `404`: Order not found

---

### 20. Pay Order
Retrieve payment details for unpaid order.

**Endpoint:** `POST /api/orders/{order}/pay`

**Auth:** Bearer Token (Required)

**URL Parameters:**
- `order` (integer, required) - Order ID

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "payment": {
      "id": 1,
      "method": "bank_transfer",
      "amount": 100000,
      "status": "pending",
      "bank_transfer_details": {
        "account_number": "123456789",
        "account_holder": "PT Bharata Herbal",
        "amount": 100000
      }
    }
  }
}
```

**Response Errors:**
- `400`: Order doesn't need payment
- `403`: Order belongs to different user
- `404`: Order or payment data not found

---

### 21. Buy Again
Add all products from a previous order to cart.

**Endpoint:** `POST /api/orders/{order}/buy-again`

**Auth:** Bearer Token (Required)

**URL Parameters:**
- `order` (integer, required) - Order ID

**Response 200:**
```json
{
  "success": true,
  "message": "Produk dari pesanan berhasil ditambahkan ke keranjang.",
  "data": {
    "cart": {
      "id": 1,
      "items": [...],
      "total": 80000,
      "selected_count": 2,
      "total_items": 2,
      "minimum_order_amount": 50000,
      "is_minimum_met": true,
      "created_at": "2026-04-20T10:00:00Z"
    },
    "message": "Produk dari pesanan berhasil ditambahkan ke keranjang."
  }
}
```

**Response Errors:**
- `403`: Order belongs to different user
- `404`: Order not found

---

## Review Endpoints

### 22. Add Review
Add review to product after order completion.

**Endpoint:** `POST /api/orders/{order}/reviews`

**Auth:** Bearer Token (Required)

**URL Parameters:**
- `order` (integer, required) - Order ID

**Request Body:**
```json
{
  "product_id": "integer (required, exists in order items)",
  "rating": "integer (required, min:1, max:5)",
  "comment": "string (optional, max:1000)",
  "reviewer_name": "string (optional, max:255)",
  "reviewer_title": "string (optional, max:255)"
}
```

**Response 201:**
```json
{
  "success": true,
  "message": "Review berhasil ditambahkan.",
  "data": {
    "id": 1,
    "product_id": 1,
    "product_name": "Jamu Herbal Plus",
    "user_id": 1,
    "user_name": "John Doe",
    "rating": 5,
    "comment": "Produk bagus dan cepat sampai!",
    "image_url": null,
    "reviewer_name": "John Doe",
    "reviewer_title": "Verified Buyer",
    "is_featured": false,
    "created_at": "2026-04-24T15:30:00Z"
  }
}
```

**Response Errors:**
- `400`: Order not completed or review already exists
- `403`: Order belongs to different user
- `404`: Order or product not found
- `422`: Validation failed

---

### 23. Delete Review
Delete a review created by the user.

**Endpoint:** `DELETE /api/reviews/{review}`

**Auth:** Bearer Token (Required)

**URL Parameters:**
- `review` (integer, required) - Review ID

**Response 200:**
```json
{
  "success": true,
  "message": "Review berhasil dihapus.",
  "data": null
}
```

**Response Errors:**
- `403`: Review belongs to different user
- `404`: Review not found

---

## Profile Endpoints

### 24. Get Profile
Retrieve current user's profile.

**Endpoint:** `GET /api/profile`

**Auth:** Bearer Token (Required)

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "08123456789",
    "role": "customer",
    "created_at": "2026-01-15T10:00:00Z"
  }
}
```

---

### 25. Update Profile
Update user's profile information.

**Endpoint:** `PUT /api/profile`

**Auth:** Bearer Token (Required)

**Request Body:**
```json
{
  "name": "string (required, max:255)",
  "email": "string (required, email, unique except own)",
  "phone": "string (required, regex: /^[0-9]{10,15}$/)"
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Profil berhasil diperbarui.",
  "data": {
    "id": 1,
    "name": "Jane Doe",
    "email": "jane@example.com",
    "phone": "08987654321",
    "role": "customer",
    "created_at": "2026-01-15T10:00:00Z"
  }
}
```

**Response Errors:**
- `422`: Email already used or validation failed

---

### 26. Update Password
Change user's password.

**Endpoint:** `PUT /api/profile/password`

**Auth:** Bearer Token (Required)

**Request Body:**
```json
{
  "current_password": "string (required)",
  "password": "string (required, min:6, with uppercase, number, symbol)",
  "password_confirmation": "string (required, must match password)"
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Password berhasil diperbarui.",
  "data": null
}
```

**Response Errors:**
- `422`: Current password incorrect or validation failed

---

## Address Endpoints

### 27. List Addresses
Retrieve all user's addresses.

**Endpoint:** `GET /api/addresses`

**Auth:** Bearer Token (Required)

**Response 200:**
```json
{
  "success": true,
  "message": "OK",
  "data": [
    {
      "id": 1,
      "label": "Rumah",
      "recipient_name": "John Doe",
      "phone": "08123456789",
      "street": "Jl. Merdeka 123",
      "city": "Jakarta",
      "province": "DKI Jakarta",
      "postal_code": "12345",
      "full_address": "Jl. Merdeka 123, Jakarta, DKI Jakarta 12345",
      "is_default": true,
      "created_at": "2026-04-15T10:00:00Z"
    },
    {
      "id": 2,
      "label": "Kantor",
      "recipient_name": "John Doe",
      "phone": "08123456789",
      "street": "Jl. Gatot Subroto 456",
      "city": "Jakarta",
      "province": "DKI Jakarta",
      "postal_code": "12346",
      "full_address": "Jl. Gatot Subroto 456, Jakarta, DKI Jakarta 12346",
      "is_default": false,
      "created_at": "2026-04-15T10:00:00Z"
    }
  ]
}
```

---

### 28. Add Address
Create a new address.

**Endpoint:** `POST /api/addresses`

**Auth:** Bearer Token (Required)

**Request Body:**
```json
{
  "label": "string (required, max:50)",
  "recipient_name": "string (required, max:255)",
  "phone": "string (required, regex: /^[0-9]{10,15}$/)",
  "street": "string (required, max:255)",
  "city": "string (required, max:100)",
  "province": "string (required, max:100)",
  "postal_code": "string (required, regex: /^[0-9]{5,6}$/)",
  "is_default": "boolean (optional)"
}
```

**Response 201:**
```json
{
  "success": true,
  "message": "Alamat berhasil ditambahkan.",
  "data": {
    "id": 3,
    "label": "Rumah Orang Tua",
    "recipient_name": "John Doe",
    "phone": "08567890123",
    "street": "Jl. Ahmad Yani 789",
    "city": "Bandung",
    "province": "Jawa Barat",
    "postal_code": "40123",
    "full_address": "Jl. Ahmad Yani 789, Bandung, Jawa Barat 40123",
    "is_default": false,
    "created_at": "2026-04-24T10:00:00Z"
  }
}
```

**Response Errors:**
- `422`: Validation failed (invalid phone, postal code, etc.)

---

### 29. Delete Address
Delete an address.

**Endpoint:** `DELETE /api/addresses/{address}`

**Auth:** Bearer Token (Required)

**URL Parameters:**
- `address` (integer, required) - Address ID

**Response 200:**
```json
{
  "success": true,
  "message": "Alamat berhasil dihapus.",
  "data": null
}
```

**Response Errors:**
- `403`: Address belongs to different user
- `404`: Address not found

---

### 30. Set Default Address
Set an address as default.

**Endpoint:** `PATCH /api/addresses/{address}/default`

**Auth:** Bearer Token (Required)

**URL Parameters:**
- `address` (integer, required) - Address ID

**Response 200:**
```json
{
  "success": true,
  "message": "Alamat default berhasil diubah.",
  "data": {
    "id": 2,
    "label": "Kantor",
    "recipient_name": "John Doe",
    "phone": "08123456789",
    "street": "Jl. Gatot Subroto 456",
    "city": "Jakarta",
    "province": "DKI Jakarta",
    "postal_code": "12346",
    "full_address": "Jl. Gatot Subroto 456, Jakarta, DKI Jakarta 12346",
    "is_default": true,
    "created_at": "2026-04-15T10:00:00Z"
  }
}
```

**Response Errors:**
- `403`: Address belongs to different user
- `404`: Address not found

---

## Status Codes

| Code | Description |
|------|-------------|
| 200 | Success - Request successful |
| 201 | Created - Resource created successfully |
| 400 | Bad Request - Invalid request or business logic error |
| 401 | Unauthorized - Missing or invalid token |
| 403 | Forbidden - User not authorized to access resource |
| 404 | Not Found - Resource does not exist |
| 422 | Unprocessable Entity - Validation failed |
| 500 | Internal Server Error - Server error |

---

## Authentication

All authenticated endpoints require the `Authorization` header:

```
Authorization: Bearer {token}
```

The token is obtained from login or register endpoints and is a Laravel Sanctum personal access token.

---

## Rate Limiting

Currently, there is no rate limiting implemented. Future versions may include rate limiting.

---

## Pagination

List endpoints that return paginated data follow this structure:

```json
{
  "data": [...],
  "pagination": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 12,
    "total": 120,
    "from": 1,
    "to": 12
  }
}
```

---

## Error Handling

All errors follow the standard error response format:

```json
{
  "success": false,
  "message": "Human-readable error message",
  "errors": {
    // Optional: Validation errors for 422 status
  }
}
```

---

## Date Format

All dates are returned in ISO 8601 format (UTC):

```
2026-04-23T10:30:00Z
```

---

## Currency

All prices are in Indonesian Rupiah (IDR) and returned as integers (without decimal points).

---

## File Uploads

Currently, the API does not support file uploads via request body. Review images must be uploaded separately or through web interface.

---

## Version History

### v1.0.0 (2026-04-23)
- Initial API release
- Support for products, cart, checkout, orders, reviews, profile, and addresses

---

## Support

For API issues or questions, please contact the development team.
