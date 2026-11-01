<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $permissions = [
        'Website Manage',
        'Contact Message Index',
        'Contact Message Delete',
    ];

    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 191)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('subject', 191)->nullable();
            $table->text('message');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('locale', 8)->nullable();
            $table->timestamp('read_at')->nullable()->index();
            $table->foreignId('read_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('replied_at')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('created_at');
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question', 255);
            $table->text('answer');
            $table->string('locale', 8)->nullable()->index(); // null = shown for every language
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title', 191);
            $table->string('slug', 191)->unique();
            $table->string('excerpt', 500)->nullable();
            $table->longText('body')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('locale', 8)->nullable()->index();
            $table->enum('status', ['draft', 'published'])->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->string('meta_title', 191)->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        $now = now();
        foreach ($this->permissions as $name) {
            DB::table('permissions')->updateOrInsert(['name' => $name, 'guard_name' => 'web'], ['created_at' => $now, 'updated_at' => $now]);
        }
        $superAdminId = DB::table('roles')->where('name', 'Super Admin')->value('id');
        if ($superAdminId) {
            foreach (DB::table('permissions')->whereIn('name', $this->permissions)->pluck('id') as $pid) {
                DB::table('role_has_permissions')->insertOrIgnore(['permission_id' => $pid, 'role_id' => $superAdminId]);
            }
        }

        // Starter FAQs so the public page is not empty on a fresh install
        $faqs = [
            ['en', 'What are your opening hours?', 'We are open every day from 10:00 AM to 9:00 PM, except public holidays.'],
            ['en', 'Do you offer installments or EMI?', 'Yes. We offer in-house installment plans with a small down payment, and 0% bank EMI on selected credit cards.'],
            ['en', 'Is there a warranty on products?', 'Most products come with an official brand warranty. The warranty period is printed on your invoice.'],
            ['bn', 'আপনাদের দোকান কখন খোলা থাকে?', 'সরকারি ছুটির দিন ছাড়া প্রতিদিন সকাল ১০টা থেকে রাত ৯টা পর্যন্ত খোলা থাকে।'],
            ['bn', 'কিস্তি বা ইএমআই সুবিধা আছে কি?', 'হ্যাঁ। অল্প ডাউন পেমেন্টে নিজস্ব কিস্তি সুবিধা এবং নির্দিষ্ট ক্রেডিট কার্ডে ০% ব্যাংক ইএমআই সুবিধা আছে।'],
            ['bn', 'পণ্যে কি ওয়ারেন্টি আছে?', 'বেশিরভাগ পণ্যে অফিসিয়াল ব্র্যান্ড ওয়ারেন্টি আছে। ওয়ারেন্টির মেয়াদ আপনার ইনভয়েসে লেখা থাকে।'],
        ];
        foreach ($faqs as $i => [$locale, $q, $a]) {
            DB::table('faqs')->insert(['locale' => $locale, 'question' => $q, 'answer' => $a, 'sort_order' => $i % 3, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now]);
        }

        Cache::forget('spatie.permission.cache');
        Cache::forever('auth_version', time());
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('contact_messages');
        DB::table('permissions')->whereIn('name', $this->permissions)->delete();
    }
};
