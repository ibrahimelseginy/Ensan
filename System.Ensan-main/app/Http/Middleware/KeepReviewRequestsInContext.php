<?php

namespace App\Http\Middleware;

use App\Models\ChangeRequest;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KeepReviewRequestsInContext
{
    /**
     * Keep users on the screen that created/found a review request.
     * The review centre is still available through its normal navigation link.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $response instanceof RedirectResponse
            || $request->routeIs('change-requests.*')
            || ! $this->targetsReviewRequestsPage($response)) {
            return $response;
        }

        $response->setTargetUrl($this->safePreviousUrl($request));

        if ($request->hasSession()) {
            $changeRequestId = $request->session()->get('review_request_id');

            if (! $changeRequestId) {
                $changeRequestId = ChangeRequest::query()
                    ->where('status', 'pending')
                    ->when($request->user(), fn ($query, $user) => $query->where('user_id', $user->getKey()))
                    ->latest('id')
                    ->value('id');
            }

            if ($changeRequestId) {
                $request->session()->flash('review_request_id', $changeRequestId);
                $request->session()->flash('review_request_notice', true);
            }
        }

        return $response;
    }

    private function targetsReviewRequestsPage(RedirectResponse $response): bool
    {
        return $this->normalisedPath($response->getTargetUrl())
            === $this->normalisedPath(route('change-requests.index'));
    }

    private function safePreviousUrl(Request $request): string
    {
        $previous = $request->headers->get('referer');

        if (is_string($previous)
            && $this->hasSameOrigin($request, $previous)
            && (! $request->isMethod('GET')
                || $this->normalisedUrl($previous) !== $this->normalisedUrl($request->fullUrl()))) {
            return $previous;
        }

        return route('dashboard.index');
    }

    private function hasSameOrigin(Request $request, string $url): bool
    {
        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['host'])) {
            return false;
        }

        $urlPort = isset($parts['port'])
            ? (int) $parts['port']
            : (($parts['scheme'] ?? 'http') === 'https' ? 443 : 80);
        $requestPort = $request->getPort();

        return strtolower($parts['host']) === strtolower($request->getHost())
            && $urlPort === $requestPort;
    }

    private function normalisedPath(string $url): string
    {
        return '/' . trim((string) parse_url($url, PHP_URL_PATH), '/');
    }

    private function normalisedUrl(string $url): string
    {
        return rtrim($url, '/');
    }
}
