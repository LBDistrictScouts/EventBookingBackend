<?php
declare(strict_types=1);

namespace App\Model\Enum;

use Cake\Database\Type\EnumLabelInterface;
use Cake\Utility\Inflector;
use JsonSerializable;

enum ParticipantTypeCategory: int implements EnumLabelInterface, JsonSerializable
{
    case YoungPerson = 0;
    case Adult = 1;
    case Animal = 2;

    /**
     * @return string
     */
    public function label(): string
    {
        return Inflector::humanize(Inflector::underscore($this->name));
    }

    /**
     * @return string
     */
    public function jsonSerialize(): string
    {
        return $this->label();
    }
}
