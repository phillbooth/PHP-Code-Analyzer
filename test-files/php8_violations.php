<?php

class Php8Violations
{
    // Violation for 'ConsiderReadonlyProperties' (ID 1)
    public string $name;
    public int $age;

    // Violation for 'UseConstructorPropertyPromotion' (ID 2)
    public function __construct(string $name, int $age)
    {
        $this->name = $name;
        $this->age = $age;
    }

    // Violation for 'PreferMatchExpression' (ID 3)
    public function getStatus(int $code): string
    {
        switch ($code) {
            case 200:
                return 'OK';
            case 404:
                return 'Not Found';
            default:
                return 'Unknown';
        }
    }

    // Violation for 'PreferNullsafeOperator' (ID 4)
    public function getUserEmail(?object $user): ?string
    {
        if ($user && $user->profile && $user->profile->contact) {
            return $user->profile->contact->email;
        }
        return null;
    }

    // Violation for 'UseUnionTypes' (ID 5) - targeting old PHPDoc style
    /**
     * @param string|int $input
     * @return bool|string
     */
    public function processInput($input)
    {
        return is_string($input) ? true : false;
    }

    // Violation for 'PreferEnums' (ID 6)
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
}

