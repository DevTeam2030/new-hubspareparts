<?php

namespace App\Helpers;

use App\Enums\SessionKey;

class CompareHelper
{
    /**
     * Get compare product IDs from session
     * @return array
     */
    public static function getSessionCompareIds(): array
    {
        return session()->get(SessionKey::PRODUCT_COMPARE_LIST, []);
    }
    
    /**
     * Set compare product IDs in session
     * @param array $productIds
     * @return void
     */
    public static function setSessionCompareIds(array $productIds): void
    {
        session()->forget(SessionKey::PRODUCT_COMPARE_LIST);
        session()->put(SessionKey::PRODUCT_COMPARE_LIST, $productIds);
    }
    
    /**
     * Add product to session compare list
     * @param int $productId
     * @param int $limit
     * @return array
     */
    public static function addProductToSession(int $productId, int $limit = 3): array
    {
        $compareProductIds = self::getSessionCompareIds();
        
        // Check if product already in compare list
        if (in_array($productId, $compareProductIds)) {
            // Remove from compare
            $compareProductIds = array_diff($compareProductIds, [$productId]);
            $action = 'removed';
        } else {
            // Add to compare (limit to specified number of items)
            if (count($compareProductIds) >= $limit) {
                // Remove first item
                array_shift($compareProductIds);
            }
            $compareProductIds[] = $productId;
            $action = 'added';
        }
        
        self::setSessionCompareIds(array_values($compareProductIds));
        
        return [
            'action' => $action,
            'product_ids' => array_values($compareProductIds),
            'count' => count($compareProductIds)
        ];
    }
    
    /**
     * Remove product from session compare list
     * @param int $productId
     * @return array
     */
    public static function removeProductFromSession(int $productId): array
    {
        $compareProductIds = self::getSessionCompareIds();
        
        if (in_array($productId, $compareProductIds)) {
            $compareProductIds = array_diff($compareProductIds, [$productId]);
            self::setSessionCompareIds(array_values($compareProductIds));
        }
        
        return [
            'product_ids' => array_values($compareProductIds),
            'count' => count($compareProductIds)
        ];
    }
    
    /**
     * Clear session compare list
     * @return void
     */
    public static function clearSessionCompare(): void
    {
        self::setSessionCompareIds([]);
    }
    
    /**
     * Check if product is in session compare list
     * @param int $productId
     * @return bool
     */
    public static function isProductInSessionCompare(int $productId): bool
    {
        $compareProductIds = self::getSessionCompareIds();
        return in_array($productId, $compareProductIds);
    }
    
    /**
     * Get session compare count
     * @return int
     */
    public static function getSessionCompareCount(): int
    {
        return count(self::getSessionCompareIds());
    }
}
