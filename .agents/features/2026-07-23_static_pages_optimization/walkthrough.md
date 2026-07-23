# Static & Informational Pages Optimization Walkthrough

We have successfully optimized the database query footprint for the informational pages.

---

## Final Performance Metrics Achieved

| Page / Route | Baseline Queries | Optimized Queries | Baseline Models | Optimized Models |
|---|---|---|---|---|
| **Contact Us** (`/contact-us`) | 4 Statements | **4 Statements** | 2 Models | **2 Models** |
| **About Us** (`/about-us`) | 5 Statements | **4 Statements** | 3 Models | **2 Models** |
| **Privacy Policy** (`/privacy-policies`) | 5 Statements | **4 Statements** | 3 Models | **2 Models** |
| **Terms & Conditions** (`/terms-and-condition`) | 5 Statements | **4 Statements** | 3 Models | **2 Models** |

---

## Detailed Modifications

### [AboutUsController.php](file:///c:/Users/user/Downloads/Code - v1.4.9/app/Http/Controllers/AboutUsController.php)
1. **Retrieve Setting from Cache:** Swapped out the direct database query `Setting::where('name', 'about_us')->first()` for retrieving the `'about_us'` setting from the forever-cached settings collection (`view_composer_settings_list`). This saves 1 query and 1 `Setting` model hydration.

### [FooterController.php](file:///c:/Users/user/Downloads/Code - v1.4.9/app/Http/Controllers/FooterController.php)
1. **Retrieve Privacy Policy from Cache:** Swapped `Setting::where('name', 'privacy_policy')->first()` for cached settings collection lookup.
2. **Retrieve Terms & Conditions from Cache:** Swapped `Setting::where('name', 'terms_conditions')->first()` for cached settings collection lookup.
