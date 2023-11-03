<?php
namespace CodeWallet\Messages;

abstract class ProtoMessage {
    
    public abstract function serialize(): string;
    
    protected static function varint_encode(int $number): string {
        $parts = [];
        while ($number > 0x7F) {
            $parts[] = ($number & 0x7F) | 0x80;
            $number >>= 7;
        }
        $parts[] = $number;
        return implode('', array_map('chr', $parts));
    }

    protected static function double_encode(float $number): string {
        return pack('d', $number);
    }
}
?>