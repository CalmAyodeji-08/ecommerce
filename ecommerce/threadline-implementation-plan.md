# Threadline: Implementation Plan

A phase-by-phase plan for a mobile-first ecommerce website built with PHP and MySQL, priced in Naira (₦), with a landing page and online payment gateways.

## How to work through this plan

1. Work on **one phase at a time**.
2. Every phase ends with a **Done when** checklist. Do not start the next phase until every item on that list passes.
3. **A phase that fails its mobile check is not finished.** Every checklist includes one.
4. At the end of each phase, save it in Git with a tag such as `phase-3-complete`.
5. If you find a bug from an earlier phase, fix it before moving on.
6. Animation (hero slider, scroll effects, 3D) is **not part of this plan**. It can be added as an optional phase after launch.

## Tech and tools

| Part | Choice |
| --- | --- |
| Language | PHP 8.2 or newer |
| Database | MySQL or MariaDB, accessed with PDO |
| Structure | Simple MVC in plain PHP (Laravel is an option if you want routing, login and migrations built in) |
| Front end | HTML, mobile-first CSS, small amounts of plain JavaScript |
| Local setup (Windows) | Laragon or XAMPP, VS Code, Composer, Git |
| Payments | Paystack first, Flutterwave second |

## Mobile-first standards (the most important rule)

Most customers will shop on their phones, so the phone version is the real website and the desktop version is the enlarged copy.

**Layout**
- Write CSS for phones first, then widen with breakpoints at about 600px, 900px and 1200px.
- Every page has `<meta name="viewport" content="width=device-width, initial-scale=1">`.
- No page ever scrolls sideways.
- Phones (under 900px) get the floating bottom navigation, with enough space at the bottom of each page so it never covers content. Keep it clear of the phone's home bar using `env(safe-area-inset-bottom)`. Desktop gets header links.
- Product grid: 2 columns on phones, 3 on tablets, 4 on desktop. Forms and checkout are one column on phones.

**Touch**
- Buttons and links are at least 44px high with space between them.
- Nothing important depends on hovering.

**Forms**
- Inputs use a font size of at least 16px so phones do not zoom in.
- Use the right keyboard: `type="email"` for email and `type="tel"` for phone numbers.
- Add `autocomplete` attributes, and put labels above fields.
- The "Pay" and "Place order" buttons stay reachable when the keyboard is open.

**Speed (many customers use mobile data)**
- Images are WebP, have set width and height, and load lazily (except the first one on screen).
- Keep the landing page light, ideally around 1 MB or less in total.
- Test with the browser's network throttling set to a slow connection.

**Testing every phase**
- Check at widths of 360, 390, 768 and 1280 in the browser's device mode.
- Check on a real Android phone, and on an iPhone if you can borrow one.
- Tip: with your computer and phone on the same Wi-Fi, you can open your local site on the phone using your computer's IP address. You may need to allow it through Windows Firewall.

## Currency rules (Naira)

- Store every price as a **whole number in kobo** (₦1 = 100 kobo). ₦18,000 is saved as `1800000`. This avoids rounding errors and is the unit Paystack and Flutterwave expect.
- Show prices as `₦18,000` using one helper function, so the format is the same everywhere.
- Set the currency to `NGN` on every order and every payment request.
- Delivery fees depend on the Nigerian state, with free delivery above a threshold you choose.
- Phone numbers are collected in Nigerian format (`0801...` or `+234801...`) and cleaned before saving.

---

## Phase 0: Decisions and setup

**Goal:** a working empty project and the key decisions made.

- Decide: guest checkout allowed (recommended) or account required.
- Write down the first 10 to 20 products with sizes, colours and prices in ₦.
- List the delivery states and fees, plus the free-delivery amount.
- Install Laragon, VS Code, Composer and Git, and create the project folder:

```
/public      index.php, css, js, images
/app         Controllers, Models, Views (with shared partials)
/config      database and app settings
/database    schema.sql, seed.sql
.env         secrets (never committed to Git)
```

- Set up Git, a `.gitignore` that excludes `.env`, and a database connection that works.

**Done when:**
- [ ] `localhost` shows a test page from `/public/index.php`
- [ ] The database connects through PDO
- [ ] `.env` holds the settings and is ignored by Git
- [ ] The decisions above are written down
- [ ] The test page opens on your phone

## Phase 1: Database

**Goal:** all tables created and filled with sample data.

- Tables: `users`, `addresses`, `categories`, `products`, `product_images`, `product_variants` (size, colour, stock), `carts`, `cart_items`, `wishlists`, `orders`, `order_items`, `payments`, `delivery_zones`, `subscribers` (for landing page sign-ups), `settings`.
- Prices in kobo as integers. Add indexes on slugs, category and order status.
- Add foreign keys and a `created_at` column to every table.
- Write `schema.sql` and `seed.sql` so the database can be rebuilt in one step.

**Done when:**
- [ ] Running `schema.sql` then `seed.sql` rebuilds the whole database
- [ ] At least 20 sample products with variants and ₦ prices exist
- [ ] A test query returns products with their category and stock

## Phase 2: Layout and responsive base

**Goal:** the shared look and structure every page will use.

- Create CSS variables for colours (black, off-white, one accent), fonts and spacing.
- Build shared partials: announcement bar, header, floating bottom nav, footer, product card.
- Build the `money()` helper that prints ₦ amounts.
- Add the base router so each URL loads the right controller and view.
- Add error pages (404 and 500).

**Done when:**
- [ ] The layout looks right at 360, 390, 768 and 1280 widths
- [ ] The bottom nav shows on phones and the header links show on desktop
- [ ] `money(1800000)` shows `₦18,000`
- [ ] Unknown URLs show the 404 page
- [ ] Mobile check: no sideways scrolling, and the nav does not cover any content on a real phone

## Phase 3: Landing page

**Goal:** the front door of the site at `/`, which introduces the brand and sends visitors to the shop.

**Sections, top to bottom**
1. Announcement bar (free delivery amount, current sale).
2. Hero: a short headline, one line of explanation, a clear "Shop now" button and one main image.
3. Trust strip: free delivery over ₦ amount, secure payment, easy returns, help by WhatsApp.
4. Shop by category: large tiles for tees, hoodies and caps.
5. New arrivals: a row of products from the database that you can swipe sideways on phones.
6. Best sellers.
7. Sale or promo banner, with its end date saved in the database.
8. Why choose us: three short points.
9. Customer reviews (add once you have real ones).
10. Short FAQ: delivery time, returns, payment methods.
11. Drop alert sign-up: email saved to the `subscribers` table, with a clear thank-you or error message.
12. Footer: contact details, WhatsApp and Instagram links, delivery and returns policy.

**Rules for the landing page**
- Write the real words for each section before you build it.
- The headline and "Shop now" button must be visible on the first phone screen without scrolling.
- The hero image is WebP and as small as possible (about 150KB or less).
- Sections stack in one column on phones.
- Phone and WhatsApp links open the phone's call or chat app with one tap.
- Add a page title, a meta description and a share image.

**Done when:**
- [ ] All 12 sections show correctly at 360, 390, 768 and 1280 widths
- [ ] Every button and tile leads to the right page
- [ ] The email sign-up saves a valid email once and rejects an invalid one
- [ ] The page loads quickly with slow-network throttling on
- [ ] Mobile check: the hero, headline and button fit the first screen of a real phone, and nothing scrolls sideways

## Phase 4: Product catalogue

**Goal:** customers can find and view products.

- Category pages with filters (size, colour, price range) and sorting.
- Search with a clear "no results" message.
- Pagination on lists.
- Product page: image gallery, ₦ price, size and colour picker, stock status, description.
- On phones, the product page has a sticky "Add to bag" button.

**Done when:**
- [ ] Every product and category opens from real database data
- [ ] Search, filters and sorting work together
- [ ] Out-of-stock sizes cannot be selected
- [ ] Mobile check: filters, the size picker and the sticky button are easy to use with one thumb on a real phone

## Phase 5: Cart

**Goal:** customers can build an order.

- Session cart for visitors. Add, change quantity and remove items.
- Check stock every time the cart changes.
- Show subtotal, delivery estimate and total in ₦.
- Bag count badge in the header and nav.
- When a customer logs in later (Phase 6), their session cart merges into their saved cart.
- Calculate prices on the server only. Never trust prices sent from the browser.

**Done when:**
- [ ] Adding, updating and removing items works on phone and desktop
- [ ] A quantity above the available stock is blocked with a clear message
- [ ] The cart total matches the database prices exactly
- [ ] The cart survives a page refresh
- [ ] Mobile check: quantity buttons are easy to tap and the total is always visible

## Phase 6: Accounts

**Goal:** customers can register and manage their details.

- Register, log in, log out, and reset password by email.
- Passwords saved with `password_hash()`.
- Profile page and saved delivery addresses (with Nigerian states).
- Wishlist (heart button).
- Order history page, filled in after Phase 7.
- Protect account pages so only the owner can open them.

**Done when:**
- [ ] A new user can register, log in, log out and reset a password
- [ ] Addresses can be added, edited and deleted
- [ ] The wishlist saves and loads per user
- [ ] Logged-out visitors are redirected away from account pages
- [ ] Mobile check: forms show the right keyboards and fit on a real phone

## Phase 7: Checkout and delivery

**Goal:** a cart becomes a pending order.

- Checkout steps: contact, delivery address, delivery method, review.
- Guest checkout (email, phone, address) and logged-in checkout with saved addresses.
- Delivery fee worked out from the selected state, with free delivery above your threshold.
- Create the order and its items in **one database transaction**, with status `pending_payment`.
- Statuses: `pending_payment`, `paid`, `processing`, `shipped`, `delivered`, `cancelled`, `failed`.
- Order summary page with a unique order number.

**Done when:**
- [ ] Guests and logged-in users can both reach the payment step
- [ ] A pending order is saved correctly with all items and the total in kobo
- [ ] Delivery fees change correctly with the state
- [ ] Mobile check: checkout is one easy column and the order button stays reachable when the keyboard is open

## Phase 8: Payments and order emails

**Goal:** customers can pay, and the shop knows for certain when they have.

**Gateways**
- **Paystack** (main): cards, bank transfer, USSD and bank options.
- **Flutterwave** (second option): added after Paystack works fully.
- **Pay on delivery** or manual bank transfer (optional): the order stays `pending_payment` until you confirm it.

**Payment flow (same for each gateway)**
1. Create the order, then give it a unique payment reference.
2. Start the payment from the **server**, using the order total in kobo and currency `NGN`.
3. Send the customer to the gateway page or popup.
4. When they return, and again when the gateway's **webhook** arrives, verify the payment on the server.
5. Check three things: the reference matches, the amount matches the order total, and the currency is `NGN`.
6. If all match, mark the order `paid` and reduce stock. Make this step safe to run twice so the same payment is never counted two times.
7. Log every payment attempt in the `payments` table.

**Rules**
- Secret keys live in `.env` only, never in the browser code or in Git.
- Check the webhook's signature header before trusting it.
- Never mark an order paid just because the customer landed on a "success" page.
- Build and test everything in the gateway's **test mode** first.

**Emails:** order confirmation after payment, plus "shipped" and "delivered" updates.

**Done when:**
- [ ] A test payment marks the order `paid` and reduces stock exactly once
- [ ] A failed or cancelled payment leaves the order unpaid and lets the customer try again
- [ ] A wrong amount or reference is rejected
- [ ] The webhook works even if the customer closes the page
- [ ] Confirmation emails arrive with the right ₦ amounts
- [ ] Mobile check: a full test payment works on a real phone, including coming back to the site afterwards

## Phase 9: Admin panel

**Goal:** you can run the shop without touching the database.

- Separate admin login with an admin-only role.
- Products: add, edit, hide and delete, with image upload (resized to WebP) and stock per size.
- Categories and delivery fees by state.
- Orders: list, filter by status, view details, change status (this triggers the customer email).
- Simple dashboard: orders today, sales total in ₦, low-stock items.

**Done when:**
- [ ] A new product added in admin appears on the shop straight away
- [ ] Changing an order status updates the customer's order page and sends an email
- [ ] Non-admin users cannot open any admin page
- [ ] Mobile check: you can manage orders from your phone

## Phase 10: Security, testing and speed

**Goal:** make it safe, correct and fast before going live.

- Security: prepared statements everywhere, escape all output, CSRF tokens on every form, secure session settings, login attempt limits, file upload checks, HTTPS only.
- Validation: check every form on the server, not only in the browser.
- Testing: run full journeys (guest buy, account buy, failed payment, out-of-stock, cancelled order) on phone and desktop.
- Speed: compress images, enable caching and add database indexes. Aim for a Lighthouse mobile score of 80 or above.
- Accessibility: visible focus, readable contrast, labels on all form fields.

**Done when:**
- [ ] All journeys above pass with no errors
- [ ] No page scrolls sideways on any tested width
- [ ] The security checklist is complete
- [ ] The Lighthouse mobile score meets your target on the landing page, a product page and checkout

## Phase 11: Deployment and going live

**Goal:** the real store is online and taking real payments.

- Choose hosting that supports PHP and MySQL, and connect your domain.
- Turn on SSL (HTTPS).
- Move the database and files, and set the production `.env`.
- Complete business verification with Paystack (and Flutterwave if used), then switch from **test keys to live keys**.
- Set the live webhook URL in each gateway dashboard.
- Set up automatic database backups and error logging.
- Place one small real order and refund it to confirm everything works.

**Done when:**
- [ ] The site loads over HTTPS on a real domain
- [ ] A real payment completes, the order shows as `paid`, and the email arrives
- [ ] Backups run automatically
- [ ] Test keys are removed from the live site
- [ ] Mobile check: the live site works on a phone using mobile data

---

## Optional later phase: animation

After launch, the hero slider and scroll animations can be added without changing the shop itself.
