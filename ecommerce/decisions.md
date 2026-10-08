# AZFAT BEST BOUTIQUE — Project Decisions

Recorded at Phase 0.

## Guest checkout
Allowed. Customers do not need to create an account to complete a purchase.

## Currency
All prices stored in kobo (integer). ₦1 = 100 kobo.
Free delivery threshold: ₦50,000 (stored as 5000000 kobo).

## Products (placeholder — to be replaced with real products)
20 placeholder products across tees, hoodies and caps.
Sizes: XS, S, M, L, XL, XXL (apparel); One Size (caps).
Colours: Black, White, Navy, Olive, Burgundy, Camel.
Price range: ₦8,500 – ₦35,000.

## Delivery zones and fees (placeholder)
| Zone | States | Fee |
|------|--------|-----|
| Lagos | Lagos | ₦1,500 |
| South-West | Ogun, Oyo, Osun, Ondo, Ekiti | ₦2,000 |
| South-South | Rivers, Delta, Edo, Bayelsa, Akwa Ibom, Cross River | ₦2,500 |
| South-East | Anambra, Imo, Enugu, Ebonyi, Abia | ₦2,500 |
| North-Central | FCT, Kogi, Benue, Nassarawa, Plateau, Niger, Kwara | ₦3,000 |
| North-West | Kano, Kaduna, Katsina, Sokoto, Kebbi, Zamfara, Jigawa | ₦3,500 |
| North-East | Borno, Yobe, Gombe, Adamawa, Taraba, Bauchi | ₦4,000 |

Free delivery on orders above ₦50,000.

## Tech
- Plain PHP 8.2 MVC (no Laravel)
- MySQL / MariaDB via PDO
- Paystack (primary), Flutterwave (secondary)
- Composer for autoloading and third-party packages

## Brand
- Name: AZFAT BEST BOUTIQUE
- Colours: Black (#111111), Off-white (#f5f5f0), Accent gold (#c9a84c)
- WhatsApp: placeholder (update in .env)
- Instagram: @azfatbestboutique (placeholder)
