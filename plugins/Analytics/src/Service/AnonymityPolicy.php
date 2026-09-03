<?php
declare(strict_types=1);

namespace Analytics\Service;

class AnonymityPolicy
{
    public function isSuppressed(int $responseCount, int $minResponses): bool
    {
        return $responseCount < $minResponses;
    }

    public function redact(array $payload, int $responseCount, int $minResponses): array
    {
        if (!$this->isSuppressed($responseCount, $minResponses)) {
            $payload['suppressed'] = false;
            $payload['response_count'] = $responseCount;

            return $payload;
        }

        return [
            'suppressed' => true,
            'response_count' => $responseCount,
            'min_responses' => $minResponses,
            'message' => 'Results are hidden until the anonymity floor is met.',
        ];
    }
}
