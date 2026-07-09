# Loyalty Coin System — Design

## Goal
Customer product kinle coin pabe (admin product-wise set kore dibe koto coin). Sei coin diye customer notun product "kinte" parbe ekta separate Coin Shop section theke. Hisab (sales report, accounting) thik thakte hobe — coin-redeemed order normal cash sale er sathe mix hobe na, r coin credit/debit shob track thakbe.

## Core rules

1. **Coin earn**: Admin product edit form theke set kore `coin_reward` (koto coin proti unit e). Order e oi product thakle `coin_reward * quantity` coin customer pabe.
2. **Coin credit timing**: Order **status = delivered** hole coin credit hoy — order place korার shathe shathe na (return/cancel er possibility thake, tai delivered confirm howar por e credit deya nirapod).
3. **Reversal**: Delivered howar por order jodi `cancelled` / `returned` / `refunded` hoy, ager credit kora coin fere niye neya hobe (jodi user oi coin already khoroch na kore fele — negative balance porjonto tao allow kora hobe, ledger e track thakbe).
4. **Coin redeem**: Product e admin flag korte parbe `is_coin_redeemable = true` r set korbe `coin_price` (koto coin lagbe oi product kinte). Redeem korar age balance check hoy (available coin thakle e order dite dibe), order place korar **shathe shathe e coin kete newa hoy** (delivery porjonto wait kore na — earn er niyom theke ei jaygay ta alada, kaaron customer already product commit kore fellese). Order pore cancel/return/refund hole coin fere credit hoye jai.
5. **Ledger**: Shob credit/debit ekta `coin_transactions` table e log hobe (type: earn / redeem / reversal_earn / reversal_redeem / admin_adjust), jate admin full audit trail dekhte pare r `users.coins_balance` (cached fast-read column) shob shomoy ledger er sum er shathe match kore.
6. **Idempotency**: Ekta order er jonno coin ekbar e credit hobe (double-credit prevent), delivered→cancelled→delivered emon flicker hole o coin duibar credit hobe na.
7. **Race-safety**: Redeem korar shomoy DB transaction + row lock use kore double-spend / negative-balance-by-race prevent kora hobe.

## Data model

### `products` table (new columns)
| column | type | note |
|---|---|---|
| `coin_reward` | unsignedInteger, default 0 | koto coin dibe eyi product kinle |
| `is_coin_redeemable` | boolean, default false | Coin Shop e dekhabe kina |
| `coin_price` | unsignedInteger, nullable | koto coin lagbe redeem korte |

### `users` table (new column)
| column | type | note |
|---|---|---|
| `coins_balance` | unsignedInteger, default 0 | cached balance, ledger er sum er shathe always match |

### `orders` table (new columns)
| column | type | note |
|---|---|---|
| `coins_earned` | unsignedInteger, default 0 | order place korar shomoy e snapshot (product coin_reward change hole future affect na kore) |
| `coins_awarded_at` | timestamp, nullable | credit deya hoye gele set hoy (idempotency guard) |
| `coins_reversed_at` | timestamp, nullable | reversal hoye gele set hoy |
| `coins_used` | unsignedInteger, default 0 | coin-redemption order hole koto coin khoroch hoise |
| `is_coin_redemption` | boolean, default false | eyi order ki coin diye kena — sales report e cash sale theke alada kore dekhabe |

### New table `coin_transactions` (ledger)
| column | type | note |
|---|---|---|
| `user_id` | FK | kar |
| `order_id` | FK nullable | kon order er jonno (admin adjust hole null) |
| `type` | enum(earn, redeem, reversal_earn, reversal_redeem, admin_adjust) | |
| `amount` | integer (signed) | + hole credit, - hole debit |
| `balance_after` | unsignedInteger | oi shomoy er balance (audit trail, quick reconcile) |
| `description` | string nullable | e.g. "Order #123 delivered — earned 40 coins" |
| `created_at/updated_at` | | |

## Business logic (service layer)

Notun `app/Services/CoinService.php`:
- `awardForOrder(Order $order)` — idempotent (coins_awarded_at check), sum(item.product.coin_reward * qty), credit balance, ledger row `earn`.
- `reverseForOrder(Order $order)` — jodi age awarded hoye thake ar reverse na hoye thake, balance theke minus kore, ledger row `reversal_earn`.
- `redeem(User $user, Product $product)` — balance check, DB transaction + `lockForUpdate`, stock check, Order+OrderItem create (`is_coin_redemption=true`), balance debit, ledger row `redeem`.
- `reverseRedeem(Order $order)` — coin-redemption order cancel/return hole coin fere credit, ledger row `reversal_redeem`.
- `adjust(User $user, int $amount, string $reason)` — admin manual bonus/correction, ledger row `admin_adjust`.

**Hook point**: `OrderService::updateStatus()` (existing single status-transition entry point) — status `delivered` hole `awardForOrder()`, status `cancelled/returned/refunded` hole (context onujayi) `reverseForOrder()` or `reverseRedeem()` call hobe.

## Admin side

1. Product create/edit form (existing "Status" card e is_promoted checkbox er pashe) — notun fields:
   - `coin_reward` number input — "Coins earned per unit"
   - `is_coin_redeemable` checkbox — "Redeemable with Coins"
   - `coin_price` number input (checkbox check thakle show/required) — "Coin price"
2. Notun admin page **Coin Ledger** (`admin.coins.index`) — shob `coin_transactions` list, filter by user/type, pagination — jate hisab audit kora jai.
3. Order list e `is_coin_redemption` badge — jate cash sale theke alada kore dekha jai reporting e.

## Customer side

1. Notun page **"My Coins"** (customer dashboard e navigation item) — dekhabe:
   - Current coin balance
   - Transaction history (earn/redeem/reversal list)
   - Redeemable products grid ("Coin Shop") — coin_price shoho, balance kom hole button disable
2. Product listing/details page e — `coin_reward > 0` hole "🪙 Earn X coins" badge (jemon discount badge ache, oi rokom).
3. Redeem button click → confirm → order create hoy (coins deduct hoye jai immediately), customer order history te dekha jabe normal order er moto, kintu "Coin Redemption" label shoho.

## Accounting correctness

- Coin-redeemed order er `total_amount = 0`, `payment_status = 'paid'` (payment method = coins, real money movement nai) — sales revenue report e eta cash sale theke exclude/segment kora jabe `is_coin_redemption` flag dekhe.
- `coins_earned` snapshot rakhar mane, product er `coin_reward` pore change hole purano order er hisab bodlabe na.
- Delivered→cancelled reversal shomvob mane balance negative o hote pare (user already khoroch kore fele thakle) — eta ledger e clearly dekha jabe, admin manually reconcile korte parbe. (V1 e simple; future e "hold until X days after delivery" cooling-period add kora jete pare jodi lagে.)

## Implementation order
1. Migrations (products, users, orders, coin_transactions)
2. Models (CoinTransaction + update Product/User/Order)
3. CoinService
4. Hook in OrderService::updateStatus
5. Customer Coin Shop page + nav link + redeem flow
6. Admin product form fields + validation
7. Admin coin ledger page
