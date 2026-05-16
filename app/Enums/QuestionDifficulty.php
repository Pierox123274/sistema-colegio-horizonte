<?php

namespace App\Enums;

enum QuestionDifficulty: string
{
    case Basic = 'basic';
    case Intermediate = 'intermediate';
    case Advanced = 'advanced';

    /** @return list<self> */
    public static function ordered(): array
    {
        return [self::Basic, self::Intermediate, self::Advanced];
    }

    public function index(): int
    {
        return match ($this) {
            self::Basic => 0,
            self::Intermediate => 1,
            self::Advanced => 2,
        };
    }

    public static function fromIndex(int $i): self
    {
        /** @var list<self> $o */
        $o = self::ordered();

        return $o[max(0, min(2, $i))];
    }
}
