<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** Explicit permission mapping for the website administration API. */
final class WebsiteAdminAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        abort_unless($user instanceof \App\Models\User, 401);
        $section = $request->segment(5);
        $resource = [
            'complaints' => 'complaints',
            'donation-accounts' => 'website.donation_accounts',
            'donations' => 'donations',
            'login-accounts' => 'website.accounts',
            'board-members' => 'website.board',
            'partners' => 'website.partners',
            'branches' => 'website.headquarters',
            'news' => 'website.news',
            'contact-messages' => 'website.contact_messages',
            'subscriptions' => 'website.subscriptions',
            'volunteer-requests' => 'website.volunteer_requests',
        ][$section] ?? null;
        $combined = [
            'settings' => 'website.settings.view_edit',
            'donation-page' => 'website.donation_page.view_edit',
            'projects' => 'website.content.view_edit',
            'campaigns' => 'website.campaigns_content.view_edit',
            'testimonials' => 'website.settings.view_edit',
            'features' => 'website.settings.view_edit',
            'sectors' => 'website.settings.view_edit',
            'volunteer-wall' => 'website.settings.view_edit',
            'events' => 'website.settings.view_edit',
            'faqs' => 'website.settings.view_edit',
            'room-bookings' => 'website.guest_house_content.view_edit',
        ][$section] ?? null;
        if ($section === 'content') {
            $combined = match ($request->segment(6)) {
                'projects' => 'website.content.view_edit',
                'campaigns' => 'website.campaigns_content.view_edit',
                'pages' => 'website.settings.view_edit',
                default => null,
            };
        }
        $action = match ($request->method()) {
            'GET', 'HEAD' => 'view',
            'DELETE' => 'delete',
            'PUT', 'PATCH' => 'edit',
            default => in_array($request->route()->getActionMethod(), ['verifyDonation', 'rejectDonation'], true) ? 'edit' : 'create',
        };
        // Donation approval is a financial operation, including its legacy aliases.
        if (in_array($request->route()->getActionMethod(), ['verifyDonation', 'rejectDonation'], true)) {
            $resource = 'donations';
        }
        $permission = $combined ?? ($resource ? "$resource.$action" : null);
        abort_unless($permission && $user->hasPermission($permission), 403);
        return $next($request);
    }
}
