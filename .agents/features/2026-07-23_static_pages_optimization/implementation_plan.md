# Contact Us, About Us, Privacy Policy, and Terms & Conditions Page Performance Optimization

This plan optimizes database queries on static/informational pages: Contact Us (`/contact-us`), About Us (`/about-us`), Privacy Policies (`/privacy-policies`), and Terms & Conditions (`/terms-and-condition`).

## Proposed Changes

### [Static Pages Settings Caching]

#### [MODIFY] [AboutUsController.php](file:///c:/Users/user/Downloads/Code - v1.4.9/app/Http/Controllers/AboutUsController.php)
1. **Use Cached Settings for About Us:** Replace the direct database query `Setting::where('name', 'about_us')->first()` with retrieving from the forever-cached `'view_composer_settings_list'` collection, completely eliminating the SQL query.

#### [MODIFY] [FooterController.php](file:///c:/Users/user/Downloads/Code - v1.4.9/app/Http/Controllers/FooterController.php)
1. **Use Cached Settings for Privacy Policy & Terms:** Replace the direct database queries `Setting::where('name', 'privacy_policy')->first()` and `Setting::where('name', 'terms_conditions')->first()` with retrieving from the forever-cached `'view_composer_settings_list'` collection, eliminating 2 SQL queries.

---

## Verification Plan

### Automated Tests
- Run `php -l app/Http/Controllers/AboutUsController.php` to verify PHP syntax.
- Run `php -l app/Http/Controllers/FooterController.php` to verify PHP syntax.

### Manual Verification
1. **Contact Us (`/contact-us`):**
   - Access the Contact Us page.
   - Verify Debugbar metrics: Should show **4 queries** (baseline is 4, all are Laravel session/auth queries, 0 static settings queries).
2. **About Us (`/about-us`):**
   - Access the About Us page.
   - Verify page content renders correctly.
   - Verify Debugbar metrics: Queries should reduce to **4 queries** (saving the database query on `settings`).
3. **Privacy Policy (`/privacy-policies`):**
   - Access the Privacy Policy page.
   - Verify content and last updated date render correctly.
   - Verify Debugbar metrics: Queries should reduce to **4 queries** (saving the database query).
4. **Terms & Conditions (`/terms-and-condition`):**
   - Access the Terms & Conditions page.
   - Verify content and last updated date render correctly.
   - Verify Debugbar metrics: Queries should reduce to **4 queries** (saving the database query).
