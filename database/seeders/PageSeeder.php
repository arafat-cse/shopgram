<?php
namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title'   => 'Privacy Policy',
                'content' => "<h2>Privacy Policy</h2>
<p>Last updated: " . now()->format('d F Y') . "</p>
<p>ShopGram is committed to protecting your privacy. This policy explains how we collect, use, and safeguard your personal information.</p>

<h3>Information We Collect</h3>
<ul>
  <li>Name, email address, and phone number when you register or place an order</li>
  <li>Shipping and billing address</li>
  <li>Payment information (processed securely — we do not store card details)</li>
  <li>Browsing and purchase history on our platform</li>
</ul>

<h3>How We Use Your Information</h3>
<ul>
  <li>To process and fulfill your orders</li>
  <li>To send order confirmation and delivery updates</li>
  <li>To improve our services and customer experience</li>
  <li>To send promotional offers (you can unsubscribe anytime)</li>
</ul>

<h3>Data Security</h3>
<p>We use industry-standard encryption and security measures to protect your data. We never sell or share your personal information with third parties without your consent.</p>

<h3>Contact Us</h3>
<p>If you have any questions about this policy, please contact us through our Contact page.</p>",
                'slug'           => 'privacy-policy',
                'status'         => 'active',
                'show_in_footer' => true,
            ],
            [
                'title'   => 'Return Policy',
                'content' => "<h2>Return Policy</h2>
<p>Last updated: " . now()->format('d F Y') . "</p>
<p>We want you to be completely satisfied with your purchase. If you are not happy, we are here to help.</p>

<h3>Return Window</h3>
<p>You have <strong>7 days</strong> from the date of delivery to request a return.</p>

<h3>Eligible Items</h3>
<ul>
  <li>Items must be unused and in original packaging</li>
  <li>Items must have all original tags attached</li>
  <li>Electronics must not have been opened or activated</li>
</ul>

<h3>Non-Returnable Items</h3>
<ul>
  <li>Perishable goods (food, flowers)</li>
  <li>Intimate or sanitary goods</li>
  <li>Downloadable software or digital products</li>
  <li>Sale items marked as final sale</li>
</ul>

<h3>Return Process</h3>
<ol>
  <li>Log in to your account and go to My Orders</li>
  <li>Select the item and click \"Request Return\"</li>
  <li>Our team will review and approve within 24 hours</li>
  <li>Ship the item back to our address</li>
  <li>Refund will be processed within 5-7 business days</li>
</ol>

<h3>Contact Us</h3>
<p>For return assistance, please contact our support team through the Contact page.</p>",
                'slug'           => 'return-policy',
                'status'         => 'active',
                'show_in_footer' => true,
            ],
            [
                'title'   => 'Terms & Conditions',
                'content' => "<h2>Terms & Conditions</h2>
<p>Last updated: " . now()->format('d F Y') . "</p>
<p>By using ShopGram, you agree to these terms and conditions. Please read them carefully.</p>

<h3>Use of Website</h3>
<ul>
  <li>You must be at least 18 years old to make purchases</li>
  <li>You are responsible for maintaining the confidentiality of your account</li>
  <li>You agree not to use the site for any unlawful purpose</li>
</ul>

<h3>Products and Pricing</h3>
<ul>
  <li>All prices are listed in Bangladeshi Taka (৳)</li>
  <li>We reserve the right to change prices at any time</li>
  <li>Product images are for illustration — actual product may vary slightly</li>
  <li>We reserve the right to cancel orders in case of pricing errors</li>
</ul>

<h3>Orders and Payments</h3>
<ul>
  <li>Orders are confirmed only after successful payment</li>
  <li>We accept bKash, Nagad, card payments, and cash on delivery</li>
  <li>Delivery times are estimates and may vary</li>
</ul>

<h3>Limitation of Liability</h3>
<p>ShopGram shall not be liable for any indirect, incidental, or consequential damages arising from the use of our services.</p>

<h3>Contact Us</h3>
<p>For any queries regarding these terms, please contact us through our Contact page.</p>",
                'slug'           => 'terms-and-conditions',
                'status'         => 'active',
                'show_in_footer' => true,
            ],
        ];

        foreach ($pages as $data) {
            Page::firstOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
