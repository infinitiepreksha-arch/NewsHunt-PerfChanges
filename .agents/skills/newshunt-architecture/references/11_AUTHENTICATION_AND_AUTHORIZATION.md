# 11 - Authentication & Authorization Architecture

## 1. Authentication Guards & Streams

The platform uses three distinct authentication guards:

### A. Web Session Authentication (`web` Guard)
* Serves the customer frontend and advertiser portal.
* Handled by `UserLoginController` and `UserRegisterController`.
* Uses standard encrypted HTTP cookies and sessions.

### B. Admin Panel Authentication (`admin` Guard)
* Serves the back-office CMS dashboard (`/admin`).
* Handled by `AdminControllers\DashboardController` and `Auth\LoginController`.
* Enforces `authcheck` middleware to verify administrative credentials.

### C. Mobile REST API Authentication (`auth:sanctum` Guard)
* Serves mobile application client requests under `/api/v1/`.
* Issues Bearer Tokens via Sanctum (`$user->createToken('auth_token')->plainTextToken`).
* Authenticated requests pass the header: `Authorization: Bearer <sanctum_token>`.

---

## 2. Social Authentication & Firebase Integrations
* **Google Socialite Redirects:** Handles Google OAuth login flows.
* **Firebase Token Verification:** `/api/v1/firebaseauth` routes verify Firebase ID tokens passed by native mobile apps (`FirebaseController@firebaseTokenverify`), mapping users or creating new profiles.
* **Phone Callbacks:** Firebase Phone Authentication callbacks process mobile SMS verification.

---

## 3. Spatie Role-Based Access Control (RBAC)
Role and permission authorization is powered by `spatie/laravel-permission`.

* **Roles:** `Super Admin`, `Admin`, `Editor`, `Advertiser`, `User`.
* **Permissions Tables:** `permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions`.
* **Route Protection:** Administrative endpoints use permission middleware gates (e.g. `middleware('permission:list-rssfeed')`, `middleware('permission:subscription-model-and-header/footer-script-settings')`).
