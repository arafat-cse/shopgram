<?php
namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name',            'value' => 'ShopGram',              'group' => 'general'],
            ['key' => 'site_logo',            'value' => '',                      'group' => 'general'],
            ['key' => 'site_favicon',         'value' => '',                      'group' => 'general'],
            ['key' => 'contact_email',        'value' => 'info@shopgram.com',     'group' => 'general'],
            ['key' => 'contact_phone',        'value' => '01700000000',           'group' => 'general'],
            ['key' => 'address',              'value' => 'Dhaka, Bangladesh',     'group' => 'general'],
            ['key' => 'contact_intro',        'value' => 'Amra usually short time-er moddhei reply kori.', 'group' => 'general'],
            ['key' => 'support_hours',        'value' => 'Saturday - Thursday, 10:00 AM - 8:00 PM', 'group' => 'general'],
            ['key' => 'mission',              'value' => 'Quality products, fair price, and fast delivery for every customer.', 'group' => 'general'],
            ['key' => 'vision',               'value' => 'To make ShopGram a trusted daily shopping destination in Bangladesh.', 'group' => 'general'],
            ['key' => 'facebook',             'value' => '',                      'group' => 'social'],
            ['key' => 'youtube',              'value' => '',                      'group' => 'social'],
            ['key' => 'instagram',            'value' => '',                      'group' => 'social'],
            ['key' => 'whatsapp',             'value' => '',                      'group' => 'social'],
            ['key' => 'meta_title',           'value' => 'ShopGram - Online Shop','group' => 'seo'],
            ['key' => 'meta_description',     'value' => 'Best online shopping',  'group' => 'seo'],
            ['key' => 'currency_symbol',      'value' => '৳',                    'group' => 'currency'],
            ['key' => 'currency_position',    'value' => 'before',               'group' => 'currency'],
            ['key' => 'tax_percentage',       'value' => '0',                    'group' => 'tax'],
            ['key' => 'inside_city_charge',   'value' => '60',                   'group' => 'delivery'],
            ['key' => 'outside_city_charge',  'value' => '120',                  'group' => 'delivery'],
            ['key' => 'maintenance_mode',     'value' => '0',                    'group' => 'maintenance'],
            ['key' => 'maintenance_message',  'value' => 'Site is under maintenance.', 'group' => 'maintenance'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
