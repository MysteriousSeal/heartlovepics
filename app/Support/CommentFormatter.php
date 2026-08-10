<?php

namespace App\Support;

use App\Models\User;

class CommentFormatter
{
    private const MENTION_PATTERN = '/@([a-zA-Z0-9_-]{3,30})\b/';

    /** @return array<int, string> */
    public function mentionedUsernames(string $body): array
    {
        preg_match_all(self::MENTION_PATTERN, $body, $matches);

        return array_values(array_unique($matches[1] ?? []));
    }

    public function formatBodyHtml(string $body): string
    {
        $mentions = $this->mentionedUsernames($body);

        if ($mentions === []) {
            return e($body);
        }

        $validUsernames = User::query()
            ->whereIn('username', $mentions)
            ->pluck('username')
            ->flip()
            ->all();

        $escaped = e($body);

        return preg_replace_callback(
            self::MENTION_PATTERN,
            function (array $matches) use ($validUsernames) {
                $username = $matches[1];

                if (! isset($validUsernames[$username])) {
                    return $matches[0];
                }

                $url = route('users.show', $username);

                return '<a href="'.e($url).'" class="comment-mention">@'.e($username).'</a>';
            },
            $escaped,
        ) ?? $escaped;
    }
}