<?php

namespace CodeWallet\Messages;

class SolanaAccountId extends ProtoMessage {
    private $value;

    public function __construct(string $value) {
        $this->value = $value;
    }

    public function serialize(): string {
        $value_serialized = "\x0A" . self::varint_encode(strlen($this->value)) . $this->value;
        return "\x0A" . self::varint_encode(strlen($value_serialized)) . $value_serialized;
    }
}
?>