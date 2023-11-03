<?php
namespace CodeWallet\Messages;

class Message extends ProtoMessage {
    const REQUEST_TO_RECEIVE_BILL_KIND = 5;
    
    private $value;

    public function __construct(RequestToReceiveBill $value) {
        $this->value = $value;
    }

    public function serialize(): string {
        $serialized_data = $this->value->serialize();
        return "\x2A" . self::varint_encode(strlen($serialized_data)) . $serialized_data;
    }
}