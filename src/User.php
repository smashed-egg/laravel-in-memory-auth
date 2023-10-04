<?php

namespace SmashedEgg\LaravelInMemoryAuth;

use ArrayAccess;
use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\JsonEncodingException;
use JsonSerializable;

class User extends GenericUser implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    public function toArray()
    {
        return $this->attributes;
    }

    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonEncodingException('Error encoding user ['.get_class($this).'] with ID ['.$this->getAuthIdentifier().'] to JSON: '.json_last_error_msg());
        }

        return $json;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->attributes[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->attributes[$offset]);
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}