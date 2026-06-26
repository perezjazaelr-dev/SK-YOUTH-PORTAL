<?php

namespace App\Helpers;

class PrivacyHelper
{
    /**
     * Obfuscate PII fields on a request object based on user clearance.
     */
    public static function filterPII(object $request, ?object $user): object
    {
        // If user is Admin, DPO, or is the owner of the request, do not mask.
        if ($user && ($user->isAdmin() || $user->isDpo() || strtolower($user->email) === strtolower($request->email))) {
            return $request;
        }

        // Clone/copy request if it's an Eloquent model to prevent accidental DB save of masked values
        $cloned = clone $request;

        // Mask First/Last Name
        if (isset($cloned->first_name)) {
            $cloned->first_name = self::maskName($cloned->first_name);
        }
        if (isset($cloned->last_name)) {
            $cloned->last_name = self::maskName($cloned->last_name);
        }
        if (isset($cloned->middle_name) && $cloned->middle_name) {
            $cloned->middle_name = self::maskName($cloned->middle_name);
        }
        if (isset($cloned->requestor_first_name)) {
            $cloned->requestor_first_name = self::maskName($cloned->requestor_first_name);
        }
        if (isset($cloned->requestor_last_name)) {
            $cloned->requestor_last_name = self::maskName($cloned->requestor_last_name);
        }
        if (isset($cloned->requestor_middle_name) && $cloned->requestor_middle_name) {
            $cloned->requestor_middle_name = self::maskName($cloned->requestor_middle_name);
        }

        // Mask Contact Number
        if (isset($cloned->contact_number) && $cloned->contact_number) {
            $cloned->contact_number = self::maskContact($cloned->contact_number);
        }

        // Mask Email
        if (isset($cloned->email) && $cloned->email) {
            $cloned->email = self::maskEmail($cloned->email);
        }

        // Mask Complete Address
        if (isset($cloned->complete_address) && $cloned->complete_address) {
            $cloned->complete_address = '[REDACTED FOR PRIVACY]';
        }

        return $cloned;
    }

    /**
     * Mask a string by replacing middle characters with asterisks.
     */
    private static function maskName(string $str): string
    {
        $str = trim($str);
        $len = mb_strlen($str);
        if ($len <= 1) {
            return '*';
        }
        if ($len === 2) {
            return mb_substr($str, 0, 1) . '*';
        }
        return mb_substr($str, 0, 1) . str_repeat('*', $len - 2) . mb_substr($str, -1);
    }

    /**
     * Mask email: keeping first 2 chars of mailbox name and domain intact.
     */
    private static function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return '*****';
        }
        $name = $parts[0];
        $domain = $parts[1];
        $len = mb_strlen($name);
        if ($len <= 2) {
            $maskedName = str_repeat('*', $len);
        } else {
            $maskedName = mb_substr($name, 0, 2) . str_repeat('*', $len - 2);
        }
        return $maskedName . '@' . $domain;
    }

    /**
     * Mask contact number: keeps first 4 and last 2 characters.
     */
    private static function maskContact(string $num): string
    {
        $num = trim($num);
        $len = strlen($num);
        if ($len <= 6) {
            return str_repeat('*', $len);
        }
        return substr($num, 0, 4) . str_repeat('*', $len - 6) . substr($num, -2);
    }
}
