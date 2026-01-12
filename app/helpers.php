<?php

/**
 * Check if current user has admin/supervisor privileges
 */
if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        $role = strtolower(auth()->user()->user_role ?? '');
        return in_array($role, ['admin', 'supervisor']);
    }
}

/**
 * Check if current user has owner privileges
 */
if (!function_exists('isOwner')) {
    function isOwner()
    {
        $role = strtolower(auth()->user()->user_role ?? '');
        return $role === 'owner';
    }
}

/**
 * Abort if user is not admin and not the target user
 */
if (!function_exists('authenticateIfNotAdmin')) {
    function authenticateIfNotAdmin($userID, $targetID)
    {
        if (!isAdmin() && ($userID != $targetID)) {
            return abort(401, 'You are not authorized to perform this action.');
        }
    }
}

/**
 * Get mapped role for frontend
 */
if (!function_exists('getMappedRole')) {
    function getMappedRole($oracleRole): string
    {
        return match (strtolower($oracleRole ?? '')) {
            'supervisor' => 'admin',
            'staff' => 'employee',
            'owner' => 'owner',
            default => 'employee',
        };
    }
}

/**
 * Arabic Specific function. Remove it if you don't need it.
 */
if (!function_exists('NormalizeArabic')) {
    function NormalizeArabic($string)
    {
        $string = str_replace("أ", "ا", $string);
        $string = str_replace("آ", "ا", $string);
        $string = str_replace("إ", "ا", $string);
        $string = str_replace("ة", "ه", $string);
        $string = str_replace("ي", "ى", $string);
        return str_replace("ؤ", "و", $string);
    }
}
