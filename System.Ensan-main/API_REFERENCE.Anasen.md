# 🍎 Anasen API Reference (Documentation for Frontend)

This documentation details the REST API endpoints for the **Anasen** charity platform, designed for integration with the Angular frontend.

**Base URL:** `http://{your-server}/api`  
**Auth Header (Admin):** `Authorization: Bearer {token}`

## 🚀 Running the Server
To make the API accessible to the Angular frontend, run:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```
*(Use your server's IP address in the frontend configuration)*

---

## 🔐 Authentication

### 1. Request OTP
**Endpoint:** `POST /api/auth/login`  
**Body:**
```json
{
  "phone": "01012345678"
}
```
**Response:**
```json
{
  "success": true,
  "message": "OTP sent successfully"
}
```

### 2. Verify OTP
**Endpoint:** `POST /api/auth/verify-otp`  
**Body:**
```json
{
  "phone": "01012345678",
  "otp": "12345"
}
```
**Response:**
```json
{
  "success": true,
  "token": "YOUR_API_TOKEN_HERE",
  "user": { "id": 1, "name": "...", "role": "donor|admin" }
}
```

### 3. Logout
**Endpoint:** `POST /api/auth/logout`  
**Headers:** `Authorization: Bearer {token}`  
**Response:**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

## 💰 Donations

### 1. Create Donation (Step 1)
**Endpoint:** `POST /api/donations`  
**Headers:** `Authorization: Bearer {token}`  
**Body:**
```json
{
  "amount": 500,
  "category": "campaign|project|general|sadaqa|kafala",
  "target_id": 5, 
  "payment_method": "instapay|vodafone|representative"
}
```
*Note: `target_id` is required for `campaign` and `project` categories.*

### 2. Upload Proof (Step 2)
**Endpoint:** `POST /api/donations/upload-proof`  
**Headers:** `Authorization: Bearer {token}`  
**Body:** `multipart/form-data`
- `donation_id`: (int) ID returned from Step 1
- `proof_image`: (file) Image/Screenshot

---

## 🛡️ Admin Dashboard

### 1. List All Accounts
**Endpoint:** `GET /api/admin/users`  
**Headers:** `Authorization: Bearer {token}` (Admin only)
**Description:** Returns a list of all users registered via the mobile/web API.
**Fields returned:** `id`, `name`, `phone`, `created_at`, `donations_count`, `total_donations`.

### 2. Donor File (Operation Log)
**Endpoint:** `GET /api/admin/users/{id}`  
**Headers:** `Authorization: Bearer {token}` (Admin only)

### 3. Delete User Account
**Endpoint:** `DELETE /api/admin/users/{id}`  
**Headers:** `Authorization: Bearer {token}` (Admin only)
**Description:** Permanently deletes a user's login account.

### 4. Review Pending Donations
**Endpoint:** `GET /api/admin/donations`  
**Headers:** `Authorization: Bearer {token}` (Admin only)

### 4. Verify/Reject Donation
**Endpoints:** 
- `POST /api/admin/donations/verify`
- `POST /api/admin/donations/reject`  
**Headers:** `Authorization: Bearer {token}` (Admin only)
**Body:**
```json
{
  "donation_id": 123,
  "reason": "Optional rejection reason"
}
```


---

## 🏛️ Ensan Pillars (Integrated Services)

### 1. List All Active Pillars
**Endpoint:** `GET /api/v1/mobile/home`  
**Description:** Returns the mobile home content including active integrated service pillars. Each pillar now contains a `cards` array for dynamic donation options.
**Response snippet:**
```json
{
  "status": "success",
  "data": {
    "integrated_services": [
      {
        "id": 1,
        "title": "...",
        "cards": [
          {
            "id": 5,
            "title": "حملة رمضان",
            "price": "200.00",
            "image_url": "..."
          }
        ]
      }
    ]
  }
}
```

### 2. Get Pillar Details
**Endpoint:** `GET /api/v1/mobile/integrated-services/{slug}`  
**Description:** Returns full details of a specific pillar, including its related projects, services, and custom donation cards.

---

## 💡 Integration Tips (Angular)

### Auth Interceptor
Ensure you add the `Authorization` header to every request:
```typescript
const headers = new HttpHeaders({
  'Authorization': `Bearer ${token}`
});
```
