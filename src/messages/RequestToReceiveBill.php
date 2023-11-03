<?php
namespace CodeWallet\Messages;

class RequestToReceiveBill extends ProtoMessage {
    private $requestorAccount;
    private $exchangeData;

    public function __construct(SolanaAccountId $requestorAccount, object $exchangeData) {
        $this->requestorAccount = $requestorAccount;
        $this->exchangeData = $exchangeData;
    }

    public function serialize(): string {
        $serialized = $this->requestorAccount->serialize();
        $ed_bytes = $this->exchangeData->serialize();

        if ($this->exchangeData instanceof ExchangeData) {
            $serialized .= "\x12";
        } else {
            $serialized .= "\x1A";
        }

        $serialized .= self::varint_encode(strlen($ed_bytes)) . $ed_bytes;
        return $serialized;
    }
}