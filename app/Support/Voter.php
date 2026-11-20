<?php

namespace App\Support;

use Illuminate\Http\Request;

final class Voter
{
    /**
     * Stable, non-reversible voter id: one rating per browser profile.
     */
    public static function hash(Request $request): string
    {
        return substr(
            hash_hmac(
                'sha256',
                (string) $request->ip().'|'.(string) $request->userAgent(),
                (string) config('portfolio.ratings.voter_secret'),
            ),
            0,
            32,
        );
    }
}
