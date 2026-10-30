<?php
declare(strict_types=1);

final class Cipher
{
    public static function encrypt(string $text, string $keyword): string
    {
        return self::transform($text, $keyword, 1);
    }

    public static function decrypt(string $text, string $keyword): string
    {
        return self::transform($text, $keyword, -1);
    }

    public static function isValidKeyword(string $keyword): bool
    {
        return $keyword !== ''
            && preg_match('/^[A-Za-z]+$/', $keyword) === 1;
    }

    private static function transform(
        string $text,
        string $keyword,
        int $direction
    ): string {
        if (!self::isValidKeyword($keyword)) {
            throw new InvalidArgumentException(
                'The keyword must contain letters from A to Z only.'
            );
        }

        $normalizedKey = strtoupper($keyword);
        $keyLength = strlen($normalizedKey);
        $keyIndex = 0;
        $result = '';

        for ($index = 0, $length = strlen($text); $index < $length; $index++) {
            $character = $text[$index];
            $code = ord($character);

            $isUppercase = $code >= 65 && $code <= 90;
            $isLowercase = $code >= 97 && $code <= 122;

            // Preserve spaces, numbers, punctuation, and unsupported characters.
            if (!$isUppercase && !$isLowercase) {
                $result .= $character;
                continue;
            }

            $base = $isUppercase ? 65 : 97;
            $shift = ord($normalizedKey[$keyIndex % $keyLength]) - 65;

            $shiftedPosition = (
                $code
                - $base
                + ($direction * $shift)
                + 26
            ) % 26;

            $result .= chr($base + $shiftedPosition);
            $keyIndex++;
        }

        return $result;
    }
}