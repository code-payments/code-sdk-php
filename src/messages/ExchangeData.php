<?php
namespace CodeWallet\Messages;

class ExchangeData extends ProtoMessage {
    private $currency;
    private $rate;
    private $quarks;
    private $nativeAmount;

    public function __construct(string $currency, float $rate, int $quarks, float $nativeAmount) {
        $this->currency = $currency;
        $this->rate = $rate;
        $this->quarks = $quarks;
        $this->nativeAmount = $nativeAmount;
    }

    public function serialize(): string {
        $currency_bytes = $this->currency;
        $serialized = "\x0A" . self::varint_encode(strlen($currency_bytes)) . $currency_bytes;
        $serialized .= "\x11" . self::double_encode($this->rate);
        $serialized .= "\x19" . self::double_encode($this->nativeAmount);
        $serialized .= "\x20" . self::varint_encode($this->quarks);
        return $serialized;
    }
}
?>