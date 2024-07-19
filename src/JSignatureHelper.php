<?php

namespace sandritsch91\yii2\jSignature;

use jSignature_Tools_Base30;
use jSignature_Tools_SVG;

/**
 * Class JSignatureHelper
 *
 * Provides static function to convert between different signature formats.
 */
class JSignatureHelper
{
    const SVG_PREFIX = 'data:image/svg+xml;base64,';

    /**
     * Convert a base30 string to a native format.
     * @param string $base30
     * @return array
     */
    public static function base30ToNative(string $base30): array
    {
        static::loadClasses();
        return (new jSignature_Tools_Base30())->Base64ToNative($base30);
    }

    /**
     * Convert native format to svg
     * @param array $native
     * is useful if you want to display the svg in an img tag.
     * @return string
     */
    public static function nativeToSvg(array $native): string
    {
        static::loadClasses();
        return (new jSignature_Tools_SVG())->NativeToSVG($native);
    }

    /**
     * Convert a base30 string to svg
     * @param string $base30
     * @return string
     */
    public static function base30ToSvg(string $base30): string
    {
        return $native = self::nativeToSvg(self::base30ToNative($base30));
    }

    /**
     * Convert a native format to svg base64
     * @param array $native
     * @param bool $prefix whether to prefix the base64 string with 'data:image/svg+xml;base64,'
     * @return string
     */
    public static function nativeToSvgBase64(array $native, bool $prefix): string
    {
        $base64 = base64_encode(self::nativeToSvg($native));
        return $prefix ? self::SVG_PREFIX . $base64 : $base64;
    }

    /**
     * Convert a base30 string to svg base64
     * @param string $base30
     * @param bool $prefix whether to prefix the base64 string with 'data:image/svg+xml;base64,'
     * @return string
     */
    public static function base30ToSvgBase64(string $base30, bool $prefix): string
    {
        return self::nativeToSvgBase64(self::base30ToNative($base30), $prefix);
    }

    /**
     * Load the required classes.
     * @return void
     */
    protected static function loadClasses(): void
    {
        $alias = '@bower/jsignature/extras/SignatureDataConversion_PHP/core/';
        require_once \Yii::getAlias("$alias/jSignature_Tools_Base30.php");
        require_once \Yii::getAlias("$alias/jSignature_Tools_SVG.php");
    }
}
