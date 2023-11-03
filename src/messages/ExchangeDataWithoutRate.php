<?php
namespace CodeWallet\Messages;

class ExchangeDataWithoutRate extends ProtoMessage {
    private $currency;
    private $nativeAmount;

    public function __construct(string $currency, float $nativeAmount) {
        $this->currency = $currency;
        $this->nativeAmount = $nativeAmount;
    }

    public function serialize(): string {
        $currency_bytes = $this->currency;
        $serialized = "\x0A" . self::varint_encode(strlen($currency_bytes)) . $currency_bytes;
        $serialized .= "\x11" . self::double_encode($this->nativeAmount);
        return $serialized;
    }
}
?>
