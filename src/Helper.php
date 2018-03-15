<?php

namespace Novutec\WhoisParser;


class Helper
{
    /**
     * Converts IP address to binary
     *
     * @param string $ip
     * @return string|null
     */
    public static function ip2bin(string $ip): ?string
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
            return base_convert(ip2long($ip), 10, 2);
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
            throw new \InvalidArgumentException('');
        }

        if (($ip_n = inet_pton($ip)) === false) {
            return null;
        }

        $bits = 15; // 16 x 8 bit = 128bit (ipv6)
        $ipbin = '';

        while ($bits >= 0) {
            $bin = sprintf('%08b', (ord($ip_n[$bits])));
            $ipbin = $bin . $ipbin;
            $bits = $bits - 1;
        }

        return $ipbin;
    }

    /**
     * Converts binary to IP address
     *
     * @param string $bin
     * @return string|null
     */
    public static function bin2ip(string $bin): ?string
    {
        if (strlen($bin) <= 32) {
            // 32bits (ipv4)
            return long2ip(base_convert($bin, 2, 10));
        }

        if (strlen($bin) !== 128) {
            throw new \InvalidArgumentException('');
        }

        $pad = 128 - strlen($bin);

        for ($i = 1; $i <= $pad; $i++) {
            $bin = '0' . $bin;
        }

        $bits = 0;
        $ipv6 = '';

        while ($bits <= 7) {
            $bin_part = substr($bin, ($bits * 16), 16);
            $ipv6 = $ipv6 . dechex(bindec($bin_part)) . ':';
            $bits = $bits + 1;
        }

        return inet_ntop(inet_pton(substr($ipv6, 0, - 1)));
    }
}