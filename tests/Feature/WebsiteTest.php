<?php

use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Support\HtmlSanitizer;
use App\Support\Website;

function websiteStaff(array $permissions = []): User
{
    $user = User::factory()->create();
    if ($permissions) {
        foreach ($permissions as $p) {
            Permission::findOrCreate($p, 'web');
        }
        $role = Role::findOrCreate('web-'.uniqid(), 'web');
        $role->syncPermissions($permissions);
        $user->assignRole($role);
    }

    return $user;
}

function contactPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Rahim Uddin',
        'phone' => '01712345678',
        'email' => '',
        'subject' => 'AC price',
        'message' => 'What is the price of a 1.5 ton inverter AC?',
        'website' => '',
        'form_token' => encrypt(now()->subMinute()->timestamp),
    ], $overrides);
}

test('public pages render', function () {
    BlogPost::create(['title' => 'Summer offer', 'slug' => 'summer-offer', 'status' => 'published', 'published_at' => now()->subDay(), 'body' => '<p>Hi</p>']);

    $this->get('/')->assertOk()->assertInertia(fn ($p) => $p->component('Frontend/Welcome')->has('site')->has('form_token'));
    $this->get('/blog')->assertOk()->assertInertia(fn ($p) => $p->component('Frontend/Blog/Index')->where('posts.total', 1));
    $this->get('/blog/summer-offer')->assertOk()->assertInertia(fn ($p) => $p->component('Frontend/Blog/Show'));
    $this->get('/faq')->assertOk();
    $this->get('/contact')->assertOk();
    $this->get('/sitemap.xml')->assertOk()->assertSee('/blog/summer-offer', false);
});

test('drafts and future posts are not public', function () {
    BlogPost::create(['title' => 'Draft', 'slug' => 'draft', 'status' => 'draft']);
    BlogPost::create(['title' => 'Later', 'slug' => 'later', 'status' => 'published', 'published_at' => now()->addWeek()]);

    $this->get('/blog/draft')->assertNotFound();
    $this->get('/blog/later')->assertNotFound();
});

test('a switched-off website sends visitors to login and hides its pages', function () {
    Website::set(['enabled' => false]);

    $this->get('/')->assertRedirect(route('login'));
    $this->get('/blog')->assertNotFound();
    $this->post('/contact', contactPayload())->assertNotFound();
});

test('sections can be switched off one by one', function () {
    Website::set(['show_blog' => false, 'show_contact' => false]);

    $this->get('/blog')->assertNotFound();
    $this->get('/contact')->assertNotFound();
    $this->get('/faq')->assertOk();
});

test('the contact form stores a message', function () {
    $this->post('/contact', contactPayload())->assertSessionHasNoErrors()->assertSessionHas('success');

    $msg = ContactMessage::first();
    expect($msg->name)->toBe('Rahim Uddin')->and($msg->phone)->toBe('01712345678')->and($msg->read_at)->toBeNull();
});

test('the contact form needs a phone or an email and a real message', function () {
    $this->post('/contact', contactPayload(['phone' => '', 'email' => '', 'message' => 'hi']))
        ->assertSessionHasErrors(['phone', 'email', 'message']);
    expect(ContactMessage::count())->toBe(0);
});

test('bots filling the honeypot or submitting instantly are silently dropped', function () {
    $from = fn ($ip) => $this->withServerVariables(['REMOTE_ADDR' => $ip]);
    $from('10.0.0.1')->post('/contact', contactPayload(['website' => 'http://spam.example']))->assertSessionHas('success');
    $from('10.0.0.2')->post('/contact', contactPayload(['form_token' => encrypt(now()->timestamp)]))->assertSessionHas('success');
    $from('10.0.0.3')->post('/contact', contactPayload(['form_token' => 'forged']))->assertSessionHas('success');

    expect(ContactMessage::count())->toBe(0);
});

test('the contact form is rate limited per visitor', function () {
    $this->post('/contact', contactPayload())->assertSessionHasNoErrors();
    $this->post('/contact', contactPayload(['phone' => '01811111111']))->assertSessionHasNoErrors();
    $this->post('/contact', contactPayload(['phone' => '01922222222']))->assertSessionHasErrors('message');

    expect(ContactMessage::count())->toBe(2);
});

test('link-stuffed messages are rejected', function () {
    $this->post('/contact', contactPayload(['message' => 'buy http://a.io http://b.io http://c.io http://d.io now']))
        ->assertSessionHasErrors('message');
});

test('website admin pages need their permissions', function () {
    $this->actingAs(websiteStaff())->get(route('website.settings'))->assertForbidden();
    $this->actingAs(websiteStaff())->get(route('contact-messages.index'))->assertForbidden();

    $this->actingAs(websiteStaff(['Website Manage']))->get(route('website.settings'))->assertOk();
    $this->actingAs(websiteStaff(['Contact Message Index']))->get(route('contact-messages.index'))->assertOk();
});

test('reading a message marks it read and only deleters may trash it', function () {
    $msg = ContactMessage::create(['name' => 'A', 'phone' => '017', 'message' => 'Hello there friend']);
    $reader = websiteStaff(['Contact Message Index']);

    $this->actingAs($reader)->post(route('contact-messages.read', $msg))->assertRedirect();
    expect($msg->fresh()->read_at)->not->toBeNull()->and($msg->fresh()->read_by)->toBe($reader->id);

    $this->actingAs($reader)->post(route('contact-messages.bulk'), ['ids' => [$msg->id], 'action' => 'delete'])->assertForbidden();
    $this->actingAs(websiteStaff(['Contact Message Index', 'Contact Message Delete']))
        ->post(route('contact-messages.bulk'), ['ids' => [$msg->id], 'action' => 'delete'])->assertRedirect();
    expect(ContactMessage::count())->toBe(0)->and(ContactMessage::withTrashed()->count())->toBe(1);
});

test('website settings save translated texts and the map', function () {
    $this->actingAs(websiteStaff(['Website Manage']))->post(route('website.settings.update'), [
        'hero_title' => ['en' => 'Best ACs in town', 'bn' => 'শহরের সেরা এসি'],
        'map_location' => '23.7808, 90.2792',
        'show_blog' => '0',
        'enabled' => '1',
    ])->assertSessionHasNoErrors();

    app()->setLocale('bn');
    $site = Website::forLocale('bn');
    expect($site['hero_title'])->toBe('শহরের সেরা এসি')
        ->and(Website::forLocale('es')['hero_title'])->toBe('Best ACs in town')
        ->and($site['show_blog'])->toBeFalse()
        ->and($site['map_embed_url'])->toStartWith('https://maps.google.com/maps?q=23.7808');
});

test('website settings reject maps that cannot be embedded', function () {
    $this->actingAs(websiteStaff(['Website Manage']))
        ->post(route('website.settings.update'), ['map_location' => 'https://maps.app.goo.gl/abc123'])
        ->assertSessionHasErrors('map_location');
});

test('map input is normalized to a google maps embed url', function () {
    $iframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!2d90.27" width="600" height="450"></iframe>';
    expect(Website::mapEmbedUrl($iframe))->toBe('https://www.google.com/maps/embed?pb=!1m18!2d90.27')
        ->and(Website::mapEmbedUrl('https://www.google.com/maps/place/Bashundhara+City/@23.7508,90.3907,17z'))->toContain('q=23.7508%2C90.3907')
        ->and(Website::mapEmbedUrl('<iframe src="https://evil.example/maps/embed"></iframe>'))->toBeNull()
        ->and(Website::mapEmbedUrl('javascript:alert(1)'))->toContain('maps.google.com/maps?q=javascript')
        ->and(Website::mapEmbedUrl(''))->toBeNull();
});

test('blog bodies are sanitized', function () {
    $dirty = '<p onclick="x()">Hi <a href="javascript:alert(1)">bad</a> <a href="https://ok.example" target="_blank">ok</a></p>'
        .'<script>alert(1)</script><img src="x" onerror="alert(1)"><iframe src="https://evil.example"></iframe>'
        .'<iframe src="https://www.youtube.com/embed/abc"></iframe><p class="ql-align-center foo">c</p>';
    $clean = HtmlSanitizer::clean($dirty);

    expect($clean)->not->toContain('onclick')->not->toContain('javascript:')->not->toContain('<script')
        ->not->toContain('onerror')->not->toContain('evil.example')->not->toContain('foo')
        ->toContain('rel="noopener noreferrer nofollow"')->toContain('youtube.com/embed/abc')->toContain('class="ql-align-center"');
});

test('saving a post sanitizes it and makes a unique slug from a bangla title', function () {
    $admin = websiteStaff(['Website Manage']);
    $this->actingAs($admin)->post(route('website.posts.store'), ['title' => 'ঈদ অফার', 'status' => 'published', 'body' => '<p>অফার</p><script>x</script>'])->assertSessionHasNoErrors();
    $this->actingAs($admin)->post(route('website.posts.store'), ['title' => 'ঈদ অফার', 'status' => 'draft']);

    $posts = BlogPost::orderBy('id')->get();
    // Bangla titles are transliterated to Latin letters, which keeps links easy to share
    expect($posts[0]->slug)->toBe('eed-ofar')->and($posts[1]->slug)->toBe('eed-ofar-2')
        ->and($posts[0]->body)->toBe('<p>অফার</p>')->and($posts[0]->published_at)->not->toBeNull();
    $this->get('/blog/eed-ofar')->assertOk();
});

test('the user manual is open to every signed-in user and falls back to english', function () {
    $this->get(route('docs.index'))->assertRedirect(route('login'));

    $user = websiteStaff();
    $this->actingAs($user)->get(route('docs.index'))->assertOk()
        ->assertInertia(fn ($p) => $p->component('Docs/Index')->has('chapters.0.html'));

    $user->update(['locale' => 'es']);
    $this->actingAs($user)->get(route('docs.index', 'pos-sale'))->assertOk()->assertInertia(fn ($p) => $p->where('current', 'pos-sale'));
    $this->actingAs($user)->get(route('docs.index', 'no-such-chapter'))->assertNotFound();
});

test('bangla appearance settings are saved and shared', function () {
    $admin = User::factory()->create();
    $admin->assignRole(Role::findOrCreate('Super Admin', 'web'));

    $this->actingAs($admin)->put(route('settings.update'), ['settings' => [
        'general_application_name' => 'Munna AC Bazar', 'general_phone' => '017', 'general_address' => 'Dhaka',
        'general_language' => 'bn', 'general_timezone' => 'Asia/Dhaka', 'general_date_format' => 'd-m-Y', 'general_time_format' => '12 Hours',
        'appearance_font_bn' => 'hind-siliguri', 'appearance_bn_font_scale' => 110, 'locale_bn_digits' => true, 'locale_number_grouping' => 'indian',
    ]])->assertSessionHasNoErrors();

    expect(\App\Support\Fonts::stylesheetUrl())->toContain('hind-siliguri')
        ->and(\App\Support\Fonts::cssVariables())->toContain("--font-bengali:'Hind Siliguri'")->toContain('--bn-font-scale:1.1');

    $this->actingAs($admin)->get(route('docs.index'))->assertInertia(fn ($p) => $p->where('configs.bn_digits', true)->where('configs.number_grouping', 'indian'));

    // a guest with no language chosen gets the shop's default language
    auth()->logout();
    $this->get('/')->assertInertia(fn ($p) => $p->where('locale.current', 'bn'));
});

test('an unknown font choice is rejected', function () {
    $admin = User::factory()->create();
    $admin->assignRole(Role::findOrCreate('Super Admin', 'web'));
    Setting::set('general_application_name', 'X');

    $this->actingAs($admin)->put(route('settings.update'), ['settings' => [
        'general_application_name' => 'X', 'general_phone' => '1', 'general_address' => 'A', 'general_language' => 'en',
        'general_timezone' => 'UTC', 'general_date_format' => 'd-m-Y', 'general_time_format' => '24 Hours', 'appearance_font_bn' => 'comic-sans',
    ]])->assertSessionHasErrors('settings.appearance_font_bn');
});
