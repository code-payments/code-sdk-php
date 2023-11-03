<?php

namespace CodeWallet\Library;

class Rendezvous {
    /**
     * Generates a rendezvous keypair based on a payload.
     *
     * @param CodePayload $payload The payload.
     *
     * @return Keypair The generated keypair.
     */
    public static function generate_rendezvous_keypair(CodePayload $payload): Keypair {
        // Compute the SHA256 hash of the binary payload.
        $hash = hash('sha256', $payload->toBinary(), true);
        
        // Generate the Keypair from the hash.
        return Keypair::fromSeed($hash);
    }
}
